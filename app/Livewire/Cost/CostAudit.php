<?php

namespace App\Livewire\Cost;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Cost;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Str;

#[Title('Cost Records Audit Trail')]
class CostAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedCost = null;
    public $statusFilter = '';
    public $dateFrom = '';
    public $dateTo = '';

    public function mount()
    {
        // Set default date range to last 30 days
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedCost', 'statusFilter', 'dateFrom', 'dateTo'])) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedCost', 'statusFilter']);
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Cost::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            $subQuery->whereRaw("EXISTS (
                                SELECT 1 FROM costs c
                                LEFT JOIN vendors v ON c.vendor_id = v.id
                                WHERE c.id = activity_log.subject_id
                                AND (v.name LIKE ? OR c.description LIKE ?)
                            )", ['%' . $this->search . '%', '%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedCost, function ($query) {
                $query->where('subject_id', $this->selectedCost);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get costs for dropdown
        $costs = Cost::with('vehicle')->orderBy('cost_date', 'desc')->get();

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Cost::class)->count(),
            'today_activities' => Activity::where('subject_type', Cost::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Cost::class)
                ->where('description', 'created cost record')->count(),
            'updated_count' => Activity::where('subject_type', Cost::class)
                ->where('description', 'updated cost record')->count(),
            'deleted_count' => Activity::where('subject_type', Cost::class)
                ->where('description', 'deleted cost record')->count(),
        ];

        return view('livewire.cost.cost-audit', compact('activities', 'costs', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }

    public function getStatusOptionsProperty()
    {
        return [
            '' => 'All Actions',
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
        ];
    }
}
