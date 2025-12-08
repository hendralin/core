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

    public function __construct($search = '', $sortField = 'cost_date', $sortDirection = 'desc', $typeFilter = '', $dateFrom = '', $dateTo = '')
    {
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->typeFilter = $typeFilter;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function view(): View
    {
        $costs = Cost::query()
            ->where('cost_type', 'cash')
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
            ->when($this->typeFilter, fn($q) => $q->where('cost_type', $this->typeFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        return view('exports.cash-injects', [
            'costs' => $costs,
        ]);
    }
}
