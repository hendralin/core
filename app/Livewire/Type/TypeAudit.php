<?php

namespace App\Livewire\Type;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Type;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Type Audit Trail')]
class TypeAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedType = null;
    public $types = [];

    public function mount()
    {
        $this->types = Type::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedType'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedType()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedType']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Type::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always Type, we can directly query the types table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM types WHERE types.id = activity_log.subject_id AND types.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedType, function ($query) {
                $query->where('subject_id', $this->selectedType);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Type::class)->count(),
            'today_activities' => Activity::where('subject_type', Type::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Type::class)
                ->where('description', 'created type')->count(),
            'updated_count' => Activity::where('subject_type', Type::class)
                ->where('description', 'updated type')->count(),
            'deleted_count' => Activity::where('subject_type', Type::class)
                ->where('description', 'deleted type')->count(),
        ];

        return view('livewire.type.type-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
