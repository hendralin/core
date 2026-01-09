<?php

namespace App\Livewire\CashDisbursement;

use App\Models\Cost;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CostDisbursementExport;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Pengeluaran Kas')]
class CashDisbursementIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $costIdToDelete = null;
    public $search = '';
    public $sortField = 'cost_date';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $statusFilter = '';
    public $dateFrom;
    public $dateTo;
    public $selectedMonthYear; // Format: YYYY-MM

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage', 'statusFilter', 'dateFrom', 'dateTo', 'selectedMonthYear'])) {
            $this->resetPage();
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function setCostToDelete($costId)
    {
        $this->costIdToDelete = $costId;
    }

    public function delete()
    {
        try {
            if (!$this->costIdToDelete) {
                session()->flash('error', 'Tidak ada pengeluaran kas yang dipilih untuk dihapus.');
                return;
            }

            DB::transaction(function () {
                $cost = Cost::findOrFail($this->costIdToDelete);

                // Store cost data for logging before deletion
                $costData = [
                    'cost_date' => $cost->cost_date,
                    'cost_type' => $cost->cost_type,
                    'description' => $cost->description,
                    'total_price' => $cost->total_price,
                    'document' => $cost->document,
                    'created_by' => $cost->created_by,
                    'status' => $cost->status,
                ];

                $cost->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($cost)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $costData
                    ])
                    ->log('deleted cash disbursement record');
            });

            $this->reset(['costIdToDelete']);

            session()->flash('success', 'Pengeluaran kas berhasil dihapus.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Pengeluaran kas tidak ditemukan.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public $costIdToApprove = null;
    public $costIdToReject = null;

    public function setCostToApprove($costId)
    {
        $this->costIdToApprove = $costId;
    }

    public function setCostToReject($costId)
    {
        $this->costIdToReject = $costId;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter']);
        $this->selectedMonthYear = null;
        $this->updateDateRange();
        $this->resetPage();
    }

    public function updatedSelectedMonthYear()
    {
        $this->updateDateRange();
    }

    private function updateDateRange()
    {
        if ($this->selectedMonthYear) {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $this->selectedMonthYear);
            $this->dateFrom = $date->startOfMonth()->format('Y-m-d');
            $this->dateTo = $date->endOfMonth()->format('Y-m-d');
        } else {
            $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
            $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        }
    }

    public function approve()
    {
        try {
            if (!$this->costIdToApprove) {
                session()->flash('error', 'Tidak ada pengeluaran kas yang dipilih untuk disetujui.');
                return;
            }

            $cost = Cost::findOrFail($this->costIdToApprove);
            $cost->update(['status' => 'approved']);

            $this->reset(['costIdToApprove']);

            session()->flash('success', 'Pengeluaran kas berhasil disetujui.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function reject()
    {
        try {
            if (!$this->costIdToReject) {
                session()->flash('error', 'Tidak ada pengeluaran kas yang dipilih untuk ditolak.');
                return;
            }

            $cost = Cost::findOrFail($this->costIdToReject);
            $cost->update(['status' => 'rejected']);

            $this->reset(['costIdToReject']);

            session()->flash('success', 'Pengeluaran kas berhasil ditolak.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $costs = Cost::query()
            ->where('cost_type', 'showroom')
            ->whereNull('vehicle_id')
            ->whereNull('vendor_id')
            ->with(['createdBy'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('createdBy', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                })
            )
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Calculate total for current filters (always show for default period)
        $totalForFilters = Cost::query()
            ->where('cost_type', 'showroom')
            ->whereNull('vehicle_id')
            ->whereNull('vendor_id')
            ->when($this->search, fn($q) => $q->where(function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('createdBy', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
            }))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->sum('total_price');

        return view('livewire.cash-disbursement.cash-disbursement-index', compact('costs', 'totalForFilters'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new CostDisbursementExport($this->search, $this->sortField, $this->sortDirection, $this->statusFilter, null, $this->dateFrom, $this->dateTo),
            'cash_disbursements_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $costs = Cost::query()
            ->where('cost_type', 'showroom')
            ->whereNull('vehicle_id')
            ->whereNull('vendor_id')
            ->with(['createdBy'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('createdBy', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                })
            )
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $pdf = Pdf::loadView('exports.cash-disbursements-pdf', compact('costs'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'cash_disbursements_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }

    public function getStatusOptionsProperty()
    {
        return [
            '' => 'Pilih Status',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }

}
