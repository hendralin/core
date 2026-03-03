<?php

namespace App\Livewire\VehicleTaxPayment;

use App\Exports\VehicleTaxPaymentExport;
use App\Models\Cost;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Pembayaran PKB')]
class VehicleTaxPaymentIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $costIdToDelete = null;
    public $search = '';
    public $sortField = 'cost_date';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $statusFilter = '';
    public $warehouseFilter = '';
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
        if (in_array($field, ['search', 'perPage', 'statusFilter', 'warehouseFilter', 'dateFrom', 'dateTo', 'selectedMonthYear'])) {
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
                session()->flash('error', 'Tidak ada pembayaran PKB yang dipilih untuk dihapus.');
                return;
            }

            DB::transaction(function () {
                $cost = Cost::findOrFail($this->costIdToDelete);

                $costData = [
                    'cost_date' => $cost->cost_date,
                    'cost_type' => $cost->cost_type,
                    'vehicle_id' => $cost->vehicle_id,
                    'warehouse_id' => $cost->warehouse_id,
                    'description' => $cost->description,
                    'total_price' => $cost->total_price,
                    'document' => $cost->document,
                    'created_by' => $cost->created_by,
                    'status' => $cost->status,
                ];

                $cost->delete();

                activity()
                    ->performedOn($cost)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $costData
                    ])
                    ->log('deleted pembayaran pkb record');
            });

            $this->reset(['costIdToDelete']);

            session()->flash('success', 'Pembayaran PKB berhasil dihapus.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Pembayaran PKB tidak ditemukan.');
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
        $this->reset(['search', 'statusFilter', 'warehouseFilter']);
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
                session()->flash('error', 'Tidak ada pembayaran PKB yang dipilih untuk disetujui.');
                return;
            }

            $cost = Cost::findOrFail($this->costIdToApprove);
            $cost->update(['status' => 'approved']);

            $this->reset(['costIdToApprove']);

            session()->flash('success', 'Pembayaran PKB berhasil disetujui.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function reject()
    {
        try {
            if (!$this->costIdToReject) {
                session()->flash('error', 'Tidak ada pembayaran PKB yang dipilih untuk ditolak.');
                return;
            }

            $cost = Cost::findOrFail($this->costIdToReject);
            $cost->update(['status' => 'rejected']);

            $this->reset(['costIdToReject']);

            session()->flash('success', 'Pembayaran PKB berhasil ditolak.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $costs = Cost::query()
            ->where('cost_type', 'vehicle_tax')
            ->whereNull('vendor_id')
            ->with(['createdBy', 'warehouse', 'vehicle'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('createdBy', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('vehicle', fn($q) => $q->where('police_number', 'like', '%' . $this->search . '%'));
                })
            )
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->warehouseFilter, fn($q) => $q->where('warehouse_id', $this->warehouseFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $totalForFilters = Cost::query()
            ->where('cost_type', 'vehicle_tax')
            ->whereNull('vendor_id')
            ->when($this->search, fn($q) => $q->where(function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('createdBy', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('vehicle', fn($q) => $q->where('police_number', 'like', '%' . $this->search . '%'));
            }))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->warehouseFilter, fn($q) => $q->where('warehouse_id', $this->warehouseFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->sum('total_price');

        $warehouses = Warehouse::orderBy('name')->get();

        return view('livewire.vehicle-tax-payment.vehicle-tax-payment-index', compact('costs', 'totalForFilters', 'warehouses'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new VehicleTaxPaymentExport(
                $this->search,
                $this->sortField,
                $this->sortDirection,
                $this->statusFilter,
                $this->dateFrom,
                $this->dateTo,
                $this->warehouseFilter ?: null
            ),
            'pembayaran_pkb_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $costs = Cost::query()
            ->where('cost_type', 'vehicle_tax')
            ->whereNull('vendor_id')
            ->with(['createdBy', 'warehouse', 'vehicle'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('createdBy', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('vehicle', fn($q) => $q->where('police_number', 'like', '%' . $this->search . '%'));
                })
            )
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->warehouseFilter, fn($q) => $q->where('warehouse_id', $this->warehouseFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $pdf = Pdf::loadView('exports.vehicle-tax-payments-pdf', compact('costs'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'pembayaran_pkb_' . now()->format('Y-m-d_H-i-s') . '.pdf');
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
