<?php

namespace App\Livewire\Cost;

use App\Models\Vendor;
use App\Models\Vehicle;
use Livewire\Component;
use App\Models\Cost;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CostExport;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Pembukuan Modal')]
class CostIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $costIdToDelete = null;
    public $search = '';
    public $sortField = 'cost_date';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $statusFilter = '';
    public $vehicleFilter = '';
    public $vehicle_search = '';
    public $vendorFilter = '';
    public $vendor_search = '';
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
        if (in_array($field, ['search', 'perPage', 'statusFilter', 'vehicleFilter', 'vendorFilter', 'dateFrom', 'dateTo', 'selectedMonthYear'])) {
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
                session()->flash('error', 'Tidak ada pembukuan modal yang dipilih untuk dihapus.');
                return;
            }

            DB::transaction(function () {
                $cost = Cost::with('vendor')->findOrFail($this->costIdToDelete);

                // Store cost data for logging before deletion
                $costData = [
                    'vehicle_id' => $cost->vehicle_id,
                    'cost_date' => $cost->cost_date,
                    'vendor_name' => $cost->vendor?->name,
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
                    ->log('deleted cost record');
            });

            $this->reset(['costIdToDelete']);

            session()->flash('success', 'Pembukuan modal berhasil dihapus.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Pembukuan modal tidak ditemukan.');
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

    public function setVehicleFilter($id)
    {
        $this->vehicleFilter = $id;
        $this->vehicle_search = '';
    }

    public function clearVehicleFilter()
    {
        $this->vehicleFilter = '';
        $this->vehicle_search = '';
    }

    public function setVendorFilter($id)
    {
        $this->vendorFilter = $id;
        $this->vendor_search = '';
    }

    public function clearVendorFilter()
    {
        $this->vendorFilter = '';
        $this->vendor_search = '';
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'vehicleFilter', 'vehicle_search', 'vendorFilter', 'vendor_search']);
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
                session()->flash('error', 'Tidak ada pembukuan modal yang dipilih untuk disetujui.');
                return;
            }

            $cost = Cost::findOrFail($this->costIdToApprove);
            $cost->update(['status' => 'approved']);

            $this->reset(['costIdToApprove']);

            session()->flash('success', 'Pembukuan modal berhasil disetujui.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function reject()
    {
        try {
            if (!$this->costIdToReject) {
                session()->flash('error', 'Tidak ada pembukuan modal yang dipilih untuk ditolak.');
                return;
            }

            $cost = Cost::findOrFail($this->costIdToReject);
            $cost->update(['status' => 'rejected']);

            $this->reset(['costIdToReject']);

            session()->flash('success', 'Pembukuan modal berhasil ditolak.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $costs = Cost::query()
            ->with(['vehicle', 'vendor', 'createdBy', 'payments'])
            ->whereIn('cost_type', ['service_parts', 'other_cost']) // Exclude cash disbursements (vehicle_id NULL)
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->orWhereHas('vendor', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('vehicle', fn($q) => $q->where('police_number', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('createdBy', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                })
            )
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->vehicleFilter, fn($q) => $q->where('vehicle_id', $this->vehicleFilter))
            ->when($this->vendorFilter, fn($q) => $q->where('vendor_id', $this->vendorFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->when($this->sortField === 'vendor.name', function ($q) {
                return $q->join('vendors', 'costs.vendor_id', '=', 'vendors.id')
                    ->orderBy('vendors.name', $this->sortDirection)
                    ->select('costs.*');
            }, function ($q) {
                return $q->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate($this->perPage);

        // Calculate total for current filters (always show for default period)
        $totalForFilters = Cost::query()
            ->whereIn('cost_type', ['service_parts', 'other_cost']) // Exclude cash disbursements (vehicle_id NULL)
            ->when($this->search, fn($q) => $q->where(function ($query) {
                $query->orWhereHas('vendor', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('vehicle', fn($q) => $q->where('police_number', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('createdBy', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
            }))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->vehicleFilter, fn($q) => $q->where('vehicle_id', $this->vehicleFilter))
            ->when($this->vendorFilter, fn($q) => $q->where('vendor_id', $this->vendorFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->sum('total_price');

        $vehicles = Vehicle::with(['brand', 'type'])
            ->when($this->vehicle_search !== '', function ($query) {
                $term = '%' . trim($this->vehicle_search) . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('police_number', 'like', $term)
                        ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', $term))
                        ->orWhereHas('type', fn ($t) => $t->where('name', 'like', $term));
                });
            })
            ->orderBy('police_number')
            ->limit(50)
            ->get();

        $selectedVehicle = $this->vehicleFilter
            ? Vehicle::with(['brand', 'type'])->find($this->vehicleFilter)
            : null;

        $vendors = Vendor::query()
            ->when($this->vendor_search !== '', fn ($q) => $q->where('name', 'like', '%' . trim($this->vendor_search) . '%'))
            ->orderBy('name')
            ->limit(50)
            ->get();

        $selectedVendor = $this->vendorFilter ? Vendor::find($this->vendorFilter) : null;

        return view('livewire.cost.cost-index', compact('costs', 'totalForFilters', 'vehicles', 'vendors', 'selectedVehicle', 'selectedVendor'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new CostExport($this->search, $this->sortField, $this->sortDirection, $this->statusFilter, $this->vehicleFilter, $this->vendorFilter, $this->dateFrom, $this->dateTo),
            'costs_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $costs = Cost::query()
            ->with(['vehicle', 'vendor', 'createdBy'])
            ->whereIn('cost_type', ['service_parts', 'other_cost']) // Exclude cash disbursements (vehicle_id NULL)
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->orWhereHas('vendor', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('vehicle', fn($q) => $q->where('police_number', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('createdBy', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                })
            )
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->vehicleFilter, fn($q) => $q->where('vehicle_id', $this->vehicleFilter))
            ->when($this->vendorFilter, fn($q) => $q->where('vendor_id', $this->vendorFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $pdf = Pdf::loadView('exports.costs-pdf', compact('costs'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'costs_' . now()->format('Y-m-d_H-i-s') . '.pdf');
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
