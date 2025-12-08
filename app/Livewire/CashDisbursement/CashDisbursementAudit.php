<?php

namespace App\Livewire\CashDisbursement;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Cost;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Str;

#[Title('Audit Trail Pengeluaran Kas')]
class CashDisbursementAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedCost = null;


    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedCost'])) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedCost']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Cost::class)
            ->whereRaw("EXISTS (
                SELECT 1 FROM costs c
                WHERE c.id = activity_log.subject_id
                AND c.cost_type = 'other_cost'
                AND c.vehicle_id IS NULL
                AND c.vendor_id IS NULL
            )", [])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            $subQuery->whereRaw("EXISTS (
                                SELECT 1 FROM costs c
                                WHERE c.id = activity_log.subject_id
                                AND c.description LIKE ?
                                AND c.vehicle_id IS NULL
                                AND c.vendor_id IS NULL
                            )", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedCost, function ($query) {
                $query->where('subject_id', $this->selectedCost);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get cash disbursements for dropdown
        $costs = Cost::whereIn('cost_type', ['cash', 'other_cost'])
            ->whereNull('vehicle_id')
            ->whereNull('vendor_id')
            ->orderBy('cost_date', 'desc')
            ->get();

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Cost::class)
                ->whereRaw("EXISTS (
                    SELECT 1 FROM costs c
                    WHERE c.id = activity_log.subject_id
                    AND c.cost_type = 'other_cost'
                    AND c.vehicle_id IS NULL
                    AND c.vendor_id IS NULL
                )", [])
                ->count(),
            'today_activities' => Activity::where('subject_type', Cost::class)
                ->whereRaw("EXISTS (
                    SELECT 1 FROM costs c
                    WHERE c.id = activity_log.subject_id
                    AND c.cost_type = 'other_cost'
                    AND c.vehicle_id IS NULL
                    AND c.vendor_id IS NULL
                )", [])
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Cost::class)
                ->where('description', 'created cash disbursement record')
                ->whereRaw("EXISTS (
                    SELECT 1 FROM costs c
                    WHERE c.id = activity_log.subject_id
                    AND c.cost_type = 'other_cost'
                    AND c.vehicle_id IS NULL
                    AND c.vendor_id IS NULL
                )", [])
                ->count(),
            'updated_count' => Activity::where('subject_type', Cost::class)
                ->where('description', 'updated cash disbursement record')
                ->whereRaw("EXISTS (
                    SELECT 1 FROM costs c
                    WHERE c.id = activity_log.subject_id
                    AND c.cost_type = 'other_cost'
                    AND c.vehicle_id IS NULL
                    AND c.vendor_id IS NULL
                )", [])
                ->count(),
            'deleted_count' => Activity::where('subject_type', Cost::class)
                ->where('description', 'deleted cash disbursement record')
                ->whereRaw("EXISTS (
                    SELECT 1 FROM costs c
                    WHERE c.id = activity_log.subject_id
                    AND c.cost_type = 'other_cost'
                    AND c.vehicle_id IS NULL
                    AND c.vendor_id IS NULL
                )", [])
                ->count(),
        ];

        return view('livewire.cash-disbursement.cash-disbursement-audit', compact('activities', 'costs', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }


}
