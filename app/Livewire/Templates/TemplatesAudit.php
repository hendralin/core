<?php

namespace App\Livewire\Templates;

use App\Models\Session;
use Livewire\Component;
use App\Models\Activity;
use App\Models\Template;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;

#[Title('Template Audit Trail')]
class TemplatesAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedTemplate = null;
    public $selectedSession = null;
    public $templates = [];
    public $sessions = [];

    public function mount()
    {
        // Only show templates and sessions created by current user
        $this->templates = Template::where('created_by', Auth::id())->get();
        $this->sessions = Session::where('created_by', Auth::id())->get();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedTemplate', 'selectedSession'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedTemplate()
    {
        $this->resetPage();
    }

    public function updatedSelectedSession()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedTemplate', 'selectedSession']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject.wahaSession'])
            ->where('subject_type', Template::class)
            ->whereHas('subject', function ($query) {
                $query->where('created_by', Auth::id());
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always Template, we can directly query the templates table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM templates WHERE templates.id = activity_log.subject_id AND templates.name LIKE ?)", ['%' . $this->search . '%']);
                        })
                        ->orWhere(function ($sessionQuery) {
                            // Search in session names
                            $sessionQuery->whereRaw("EXISTS (SELECT 1 FROM templates t JOIN waha_sessions ws ON t.waha_session_id = ws.id WHERE t.id = activity_log.subject_id AND ws.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedTemplate, function ($query) {
                $query->where('subject_id', $this->selectedTemplate);
            })
            ->when($this->selectedSession, function ($query) {
                $query->whereRaw("EXISTS (SELECT 1 FROM templates WHERE templates.id = activity_log.subject_id AND templates.waha_session_id = ?)", [$this->selectedSession]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics - only for templates created by current user
        $stats = [
            'total_activities' => Activity::where('subject_type', Template::class)
                ->whereHas('subject', function ($query) {
                    $query->where('created_by', Auth::id());
                })->count(),
            'today_activities' => Activity::where('subject_type', Template::class)
                ->whereHas('subject', function ($query) {
                    $query->where('created_by', Auth::id());
                })
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Template::class)
                ->whereHas('subject', function ($query) {
                    $query->where('created_by', Auth::id());
                })
                ->where('description', 'created a new template')->count(),
            'updated_count' => Activity::where('subject_type', Template::class)
                ->whereHas('subject', function ($query) {
                    $query->where('created_by', Auth::id());
                })
                ->where('description', 'updated template information')->count(),
            'deleted_count' => Activity::where('subject_type', Template::class)
                ->whereHas('subject', function ($query) {
                    $query->where('created_by', Auth::id());
                })
                ->where('description', 'deleted template')->count(),
        ];

        return view('livewire.templates.templates-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
