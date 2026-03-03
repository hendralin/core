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
    protected $warehouseId;

    public function __construct($search = '', $sortField = 'cost_date', $sortDirection = 'desc', $dateFrom = '', $dateTo = '', $selectedCostType = '', $warehouseId = null)
    {
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->selectedCostType = $selectedCostType;
        $this->warehouseId = $warehouseId;
    }

    public function view(): View
    {
        // Exclude vehicle_tax (belongs to Laporan Kas Pajak)
        $costs = Cost::query()
            ->with(['createdBy', 'vehicle', 'vendor', 'warehouse'])
            ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->warehouseId, fn($q) => $q->where('warehouse_id', $this->warehouseId))
            ->where('big_cash', '!=', 1) // Exclude big cash payments from Excel export
            ->where(function ($q) {
                $q->where('cost_type', 'cash')
                    ->orWhere(function ($q) {
                        $q->where('cost_type', '!=', 'cash')
                            ->where('cost_type', '!=', 'vehicle_tax')
                            ->whereHas('payments');
                    });
            })
            ->orderBy('cost_date', 'asc')
            ->get();

        // Calculate opening balance for export. Exclude vehicle_tax (Laporan Kas Pajak).
        $openingBalanceExcel = Cost::query()
            ->with('payments')
            ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '<', $this->dateFrom))
            ->when($this->warehouseId, fn($q) => $q->where('warehouse_id', $this->warehouseId))
            ->where('cost_type', '!=', 'vehicle_tax')
            ->get()->sum(function ($item) {
                if ($item->cost_type === 'cash') {
                    return $item->total_price;
                } elseif ($item->big_cash == 1) {
                    return 0; // Big cash payments don't affect balance
                } else {
                    return $item->payments->isNotEmpty() ? -$item->total_price : 0;
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
