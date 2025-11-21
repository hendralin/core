<?php

namespace App\Livewire\Templates;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Template;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Template Audit Trail')]
class TemplatesAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedTemplate = null;
    public $templates = [];

    public function mount()
    {
        $this->templates = Template::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedTemplate'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedTemplate()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedTemplate']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Template::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always Template, we can directly query the templates table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM templates WHERE templates.id = activity_log.subject_id AND templates.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedTemplate, function ($query) {
                $query->where('subject_id', $this->selectedTemplate);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Template::class)->count(),
            'today_activities' => Activity::where('subject_type', Template::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Template::class)
                ->where('description', 'created a new template')->count(),
            'updated_count' => Activity::where('subject_type', Template::class)
                ->where('description', 'updated template information')->count(),
            'deleted_count' => Activity::where('subject_type', Template::class)
                ->where('description', 'deleted template')->count(),
        ];

        return view('livewire.templates.templates-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
