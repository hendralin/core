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
    protected $selectedCostType;

    public function __construct($search = '', $sortField = 'cost_date', $sortDirection = 'desc', $dateFrom = '', $dateTo = '', $selectedCostType = '')
    {
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->selectedCostType = $selectedCostType;
    }

    public function view(): View
    {
        $costs = Cost::query()
            ->with(['createdBy', 'vehicle', 'vendor'])
            ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->where('big_cash', '!=', 1) // Exclude big cash payments from Excel export
            ->orderBy('cost_date', 'asc')
            ->get();

        // Calculate opening balance for export
        $openingBalanceExcel = Cost::query()
            ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '<', $this->dateFrom))
            ->get()->sum(function ($item) {
                if ($item->cost_type === 'cash') {
                    return $item->total_price;
                } elseif ($item->big_cash == 1) {
                    return 0; // Big cash payments don't affect balance
                } else {
                    return -$item->total_price;
                }
            }) ?? 0;

        // Calculate running balance for export (using all data)
        $runningBalance = $openingBalanceExcel;
        $costsWithBalance = $costs->map(function ($cost) use (&$runningBalance) {
            if ($cost->cost_type === 'cash') {
                $runningBalance += $cost->total_price;
            } elseif ($cost->big_cash == 1) {
                // Big cash payments don't affect running balance
                $runningBalance += 0;
            } else {
                $runningBalance -= $cost->total_price;
            }
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
