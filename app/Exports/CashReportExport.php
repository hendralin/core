<?php

namespace App\Exports;

use App\Models\Cost;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CashReportExport implements FromView
{
    protected $search;
    protected $sortField;
    protected $sortDirection;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($search = '', $sortField = 'cost_date', $sortDirection = 'desc', $dateFrom = '', $dateTo = '')
    {
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function view(): View
    {
        $costs = Cost::query()
            ->with(['createdBy', 'vehicle', 'vendor'])
            ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->orderBy('cost_date', 'asc')
            ->get();

        // Calculate opening balance for export
        $openingBalanceExcel = Cost::query()
            ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '<', $this->dateFrom))
            ->get()->sum(function ($item) {
                return $item->cost_type === 'cash' ? $item->total_price : -$item->total_price;
            }) ?? 0;

        // Calculate running balance for export (using all data)
        $runningBalance = $openingBalanceExcel;
        $costsWithBalance = $costs->map(function ($cost) use (&$runningBalance) {
            $runningBalance += $cost->cost_type === 'cash' ? $cost->total_price : -$cost->total_price;
            $cost->running_balance = $runningBalance;
            return $cost;
        });

        return view('exports.cash-report', [
            'costs' => $costs,
            'costsWithBalance' => $costsWithBalance,
            'openingBalanceExcel' => $openingBalanceExcel,
        ]);
    }
}
