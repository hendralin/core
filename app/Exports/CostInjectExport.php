<?php

namespace App\Exports;

use App\Models\Cost;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CostInjectExport implements FromView
{
    protected $search;
    protected $sortField;
    protected $sortDirection;
    protected $typeFilter;
    protected $dateFrom;
    protected $dateTo;
    protected $warehouseIdFilter;

    public function __construct($search = '', $sortField = 'cost_date', $sortDirection = 'desc', $typeFilter = '', $dateFrom = '', $dateTo = '', $warehouseIdFilter = '')
    {
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->typeFilter = $typeFilter;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->warehouseIdFilter = $warehouseIdFilter;
    }

    public function view(): View
    {
        $costs = Cost::query()
            ->whereIn('cost_type', ['cash', 'tax_cash'])
            ->whereNull('vehicle_id')
            ->whereNull('vendor_id')
            ->with(['createdBy', 'warehouse'])
            ->when($this->typeFilter, fn($q) => $q->where('cost_type', $this->typeFilter))
            ->when($this->warehouseIdFilter, fn($q) => $q->where('warehouse_id', $this->warehouseIdFilter))
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('createdBy', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                })
            )
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        return view('exports.cash-injects', [
            'costs' => $costs,
        ]);
    }
}
