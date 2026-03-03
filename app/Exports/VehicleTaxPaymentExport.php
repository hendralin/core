<?php

namespace App\Exports;

use App\Models\Cost;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VehicleTaxPaymentExport implements FromView
{
    protected $search;
    protected $sortField;
    protected $sortDirection;
    protected $statusFilter;
    protected $dateFrom;
    protected $dateTo;
    protected $warehouseIdFilter;

    public function __construct($search = '', $sortField = 'cost_date', $sortDirection = 'desc', $statusFilter = '', $dateFrom = '', $dateTo = '', $warehouseIdFilter = '')
    {
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->statusFilter = $statusFilter;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->warehouseIdFilter = $warehouseIdFilter;
    }

    public function view(): View
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
            ->when($this->warehouseIdFilter, fn($q) => $q->where('warehouse_id', $this->warehouseIdFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        return view('exports.vehicle-tax-payments', [
            'costs' => $costs,
        ]);
    }
}

