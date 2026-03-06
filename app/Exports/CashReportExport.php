<?php

namespace App\Exports;

use App\Models\Cost;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CashReportExport implements FromView
{
    /**
     * Cost types that increase cash balance in this report.
     */
    private const CASH_IN_TYPES = ['cash', 'loan_payment'];

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
        // Same logic as report:
        // - cash by cost_date
        // - non-cash (including loan_payment) by payment_date
        // Exclude vehicle_tax (belongs to Laporan Kas Pajak)
        $costs = Cost::query()
            ->with(['createdBy', 'vehicle', 'vendor', 'warehouse', 'payments'])
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->warehouseId, fn($q) => $q->where('warehouse_id', $this->warehouseId))
            ->where('big_cash', '!=', 1)
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('cost_type', 'cash')
                        ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                        ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo));
                })->orWhere(function ($q) {
                    $q->where('cost_type', '!=', 'cash')
                        ->where('cost_type', '!=', 'vehicle_tax')
                        ->whereHas('payments', function ($p) {
                            if (!empty($this->dateFrom)) {
                                $p->whereDate('payment_date', '>=', $this->dateFrom);
                            }
                            if (!empty($this->dateTo)) {
                                $p->whereDate('payment_date', '<=', $this->dateTo);
                            }
                        });
                });
            })
            ->get()
            ->sortBy(function ($cost) {
                $effectiveDate = $cost->cost_type === 'cash'
                    ? $cost->cost_date
                    : ($cost->payments->sortBy('payment_date')->first()?->payment_date ?? $cost->cost_date);
                $dateKey = \Carbon\Carbon::parse($effectiveDate)->format('Y-m-d');
                $timeKey = $cost->created_at?->format('Y-m-d H:i:s') ?? $cost->created_at;
                return [$dateKey, $timeKey];
            })
            ->values();

        // Opening balance before dateFrom:
        // (cash in by cost_date) + (loan_payment in by payment_date) - (cash out by payment_date). Exclude vehicle_tax.
        $cashInBefore = Cost::query()
            ->where('cost_type', 'cash')
            ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '<', $this->dateFrom))
            ->when($this->warehouseId, fn($q) => $q->where('warehouse_id', $this->warehouseId))
            ->sum('total_price');

        $loanPaymentInBefore = Cost::query()
            ->where('cost_type', 'loan_payment')
            ->where('big_cash', '!=', 1)
            ->when($this->warehouseId, fn($q) => $q->where('warehouse_id', $this->warehouseId))
            ->when(!empty($this->dateFrom), fn($q) => $q->whereHas('payments', fn($p) => $p->whereDate('payment_date', '<', $this->dateFrom)))
            ->sum('total_price');

        $cashOutBefore = Cost::query()
            ->whereNotIn('cost_type', array_merge(self::CASH_IN_TYPES, ['vehicle_tax']))
            ->where('big_cash', '!=', 1)
            ->when($this->warehouseId, fn($q) => $q->where('warehouse_id', $this->warehouseId))
            ->when(!empty($this->dateFrom), fn($q) => $q->whereHas('payments', fn($p) => $p->whereDate('payment_date', '<', $this->dateFrom)))
            ->sum('total_price');

        $openingBalanceExcel = ($cashInBefore + $loanPaymentInBefore - $cashOutBefore) ?? 0;

        // Calculate running balance for export (using all data)
        $runningBalance = $openingBalanceExcel;
        $costsWithBalance = $costs->map(function ($cost) use (&$runningBalance) {
            if (in_array($cost->cost_type, self::CASH_IN_TYPES, true)) {
                $runningBalance += $cost->total_price;
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
