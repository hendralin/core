<?php

namespace App\Exports;

use App\Models\Cost;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CostExport implements FromView
{
    protected $search;
    protected $sortField;
    protected $sortDirection;
    protected $statusFilter;
    protected $vehicleFilter;
    protected $vendorFilter;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($search = '', $sortField = 'cost_date', $sortDirection = 'desc', $statusFilter = '', $vehicleFilter = '', $vendorFilter = '', $dateFrom = '', $dateTo = '')
    {
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->statusFilter = $statusFilter;
        $this->vehicleFilter = $vehicleFilter;
        $this->vendorFilter = $vendorFilter;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function view(): View
    {
        $costs = Cost::query()
            ->with(['vehicle.brand', 'vehicle.vehicle_model', 'vendor', 'createdBy'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('vehicle', fn($q) => $q->where('police_number', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('vendor', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
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
            ->get();

        return view('exports.costs', [
            'costs' => $costs,
        ]);
    }
}
