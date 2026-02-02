<?php

namespace App\Livewire\Position;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Position;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Position Audit Trail')]
class PositionAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedPosition = null;
    public $positions = [];

    public function mount()
    {
        $this->positions = Position::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedPosition'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedPosition()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedPosition']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Position::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always Position, we can directly query the positions table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM positions WHERE positions.id = activity_log.subject_id AND positions.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedPosition, function ($query) {
                $query->where('subject_id', $this->selectedPosition);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Position::class)->count(),
            'today_activities' => Activity::where('subject_type', Position::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Position::class)
                ->where('description', 'created position')->count(),
            'updated_count' => Activity::where('subject_type', Position::class)
                ->where('description', 'updated position')->count(),
            'deleted_count' => Activity::where('subject_type', Position::class)
                ->where('description', 'deleted position')->count(),
        ];

        return view('livewire.position.position-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
