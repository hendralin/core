<?php

namespace App\Exports;

use App\Models\Cost;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TaxCashReportExport implements FromView
{
    protected $sortField;
    protected $sortDirection;
    protected $dateFrom;
    protected $dateTo;
    protected $selectedCostType;
    protected $warehouseId;

    public function __construct($sortField = 'cost_date', $sortDirection = 'asc', $dateFrom = '', $dateTo = '', $selectedCostType = '', $warehouseId = null)
    {
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->selectedCostType = $selectedCostType;
        $this->warehouseId = $warehouseId;
    }

    private function baseCostQuery()
    {
        return Cost::query()->whereIn('cost_type', ['tax_cash', 'vehicle_tax']);
    }

    public function view(): View
    {
        $allCostsInPeriod = $this->baseCostQuery()
            ->with(['createdBy', 'vehicle', 'vendor', 'warehouse', 'payments'])
            ->when($this->selectedCostType, function ($q) {
                return $q->where('cost_type', $this->selectedCostType);
            })
            ->when($this->warehouseId, function ($q) {
                return $q->where('warehouse_id', $this->warehouseId);
            })
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('cost_type', 'tax_cash')
                        ->when(!empty($this->dateFrom), function ($q) {
                            return $q->whereDate('cost_date', '>=', $this->dateFrom);
                        })
                        ->when(!empty($this->dateTo), function ($q) {
                            return $q->whereDate('cost_date', '<=', $this->dateTo);
                        });
                })->orWhere(function ($q) {
                    $q->where('cost_type', 'vehicle_tax')
                        ->whereHas('payments', function ($q) {
                            return $q->whereDate('payment_date', '>=', $this->dateFrom)
                                ->whereDate('payment_date', '<=', $this->dateTo);
                        });
                });
            })
            ->get();

        $sortedCosts = $allCostsInPeriod->sortBy(function ($cost) {
            if ($cost->cost_type === 'tax_cash') {
                return $cost->cost_date;
            }
            $first = $cost->payments->sortBy('payment_date')->first();
            return $first ? $first->payment_date : $cost->cost_date;
        }, SORT_REGULAR, $this->sortDirection === 'desc')->values();

        $cashInBefore = $this->baseCostQuery()
            ->where('cost_type', 'tax_cash')
            ->when($this->dateFrom, function ($q) {
                return $q->whereDate('cost_date', '<', $this->dateFrom);
            })
            ->when($this->warehouseId, function ($q) {
                return $q->where('warehouse_id', $this->warehouseId);
            })
            ->sum('total_price');
        $cashOutBefore = $this->baseCostQuery()
            ->where('cost_type', 'vehicle_tax')
            ->when($this->warehouseId, function ($q) {
                return $q->where('warehouse_id', $this->warehouseId);
            })
            ->whereHas('payments', function ($q) {
                return $q->whereDate('payment_date', '<', $this->dateFrom);
            })
            ->sum('total_price');
        $openingBalanceExcel = $cashInBefore - $cashOutBefore;

        $runningBalance = $openingBalanceExcel;
        $costsWithBalance = $sortedCosts->map(function ($cost) use (&$runningBalance) {
            if ($cost->cost_type === 'tax_cash') {
                $runningBalance += $cost->total_price;
                $cost->report_date = $cost->cost_date;
            } else {
                $runningBalance -= $cost->total_price;
                $first = $cost->payments->sortBy('payment_date')->first();
                $cost->report_date = $first ? $first->payment_date : $cost->cost_date;
            }
            $cost->running_balance = $runningBalance;
            return $cost;
        });

        return view('exports.tax-cash-report', [
            'costsWithBalance' => $costsWithBalance,
            'openingBalanceExcel' => $openingBalanceExcel,
        ]);
    }
}
