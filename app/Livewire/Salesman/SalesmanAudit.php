<?php

namespace App\Livewire\Salesman;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Salesman;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Salesman Audit Trail')]
class SalesmanAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedSalesman = null;
    public $salesmen = [];

    public function mount()
    {
        $this->salesmen = Salesman::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedSalesman'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedSalesman()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedSalesman']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Salesman::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always Salesman, we can directly query the salesmen table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM salesmen WHERE salesmen.id = activity_log.subject_id AND salesmen.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedSalesman, function ($query) {
                $query->where('subject_id', $this->selectedSalesman);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Salesman::class)->count(),
            'today_activities' => Activity::where('subject_type', Salesman::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Salesman::class)
                ->where('description', 'created salesman')->count(),
            'updated_count' => Activity::where('subject_type', Salesman::class)
                ->where('description', 'updated salesman')->count(),
            'deleted_count' => Activity::where('subject_type', Salesman::class)
                ->where('description', 'deleted salesman')->count(),
        ];

        return view('livewire.salesman.salesman-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}