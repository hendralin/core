<?php

namespace App\Livewire\Report\CashReport;

use App\Models\Cost;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CashReportExport;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;

#[Title('Laporan Kas')]
class CashReportIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    /**
     * Cost types that increase cash balance in this report.
     * - cash: inject kas kecil
     * - loan_payment: pembayaran pinjaman karyawan ke kas kecil (inflow)
     */
    private const CASH_IN_TYPES = ['cash', 'loan_payment'];

    public $perPage = 10;
    public $dateFrom;
    public $dateTo;
    public $selectedCostType;
    public $selectedMonthYear; // Format: YYYY-MM
    public $selectedWarehouseId;

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function updating($field)
    {
        if (in_array($field, ['perPage', 'dateFrom', 'dateTo', 'selectedCostType', 'selectedMonthYear', 'selectedWarehouseId'])) {
            $this->resetPage();
        }
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


    public function clearFilters()
    {
        $this->selectedCostType = null;
        $this->selectedMonthYear = null;
        $this->selectedWarehouseId = null;
        $this->updateDateRange();
        $this->resetPage();
    }

    public function filterByCostType($costType)
    {
        $this->selectedCostType = $this->selectedCostType === $costType ? null : $costType;
        $this->resetPage();
    }




    public function render()
    {
        $paymentInPeriod = function ($q) {
            $q->whereDate('payment_date', '>=', $this->dateFrom)
                ->whereDate('payment_date', '<=', $this->dateTo);
        };

        // Total debet = cash out recognized in period (by payment_date in range). Exclude vehicle_tax and inflow types (cash, loan_payment).
        $totalDebet = Cost::query()
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->whereNotIn('cost_type', array_merge(self::CASH_IN_TYPES, ['vehicle_tax']))
            ->where('big_cash', '!=', 1)
            ->whereHas('payments', $paymentInPeriod)
            ->sum('total_price');

        // Total kredit = cash in (by cost_date) + inflow recognized by payment_date (loan_payment).
        $totalKreditCash = Cost::query()
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->where('cost_type', 'cash')
            ->sum('total_price');

        $totalKreditLoanPayment = Cost::query()
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->where('cost_type', 'loan_payment')
            ->where('big_cash', '!=', 1)
            ->whereHas('payments', $paymentInPeriod)
            ->sum('total_price');

        $totalKredit = $totalKreditCash + $totalKreditLoanPayment;


        // Opening balance before the period:
        // (cash in by cost_date) + (loan_payment in by payment_date) - (cash out by payment_date). Exclude vehicle_tax.
        $cashInBefore = Cost::query()
            ->where('cost_type', 'cash')
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '<', $this->dateFrom))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->sum('total_price');

        $loanPaymentInBefore = Cost::query()
            ->where('cost_type', 'loan_payment')
            ->where('big_cash', '!=', 1)
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->whereHas('payments', fn($q) => $q->whereDate('payment_date', '<', $this->dateFrom))
            ->sum('total_price');

        $cashOutBefore = Cost::query()
            ->whereNotIn('cost_type', array_merge(self::CASH_IN_TYPES, ['vehicle_tax']))
            ->where('big_cash', '!=', 1)
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->whereHas('payments', fn($q) => $q->whereDate('payment_date', '<', $this->dateFrom))
            ->sum('total_price');

        $openingBalance = $cashInBefore + $loanPaymentInBefore - $cashOutBefore;

        $netBalance = $totalKredit - $totalDebet + $openingBalance;

        // Same period filter:
        // - cash by cost_date
        // - non-cash (including loan_payment) by payment_date
        // Exclude vehicle_tax (Laporan Kas Pajak).
        $allCostsInPeriod = Cost::query()
            ->with(['createdBy', 'vehicle', 'vendor', 'warehouse', 'payments'])
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->where('big_cash', '!=', 1)
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('cost_type', 'cash')
                        ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                        ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo));
                })->orWhere(function ($q) {
                    $q->where('cost_type', '!=', 'cash')
                        ->where('cost_type', '!=', 'vehicle_tax')
                        ->whereHas('payments', fn($q) => $q->whereDate('payment_date', '>=', $this->dateFrom)->whereDate('payment_date', '<=', $this->dateTo));
                });
            })
            ->get();

        // Sort by effective date (cost_date / first payment_date), then by created_at (date + time)
        $sortedCosts = $allCostsInPeriod->sortBy(function ($cost) {
            $effectiveDate = $cost->cost_type === 'cash'
                ? $cost->cost_date
                : ($cost->payments->sortBy('payment_date')->first()?->payment_date ?? $cost->cost_date);
            $dateKey = \Carbon\Carbon::parse($effectiveDate)->format('Y-m-d');
            $timeKey = $cost->created_at?->format('Y-m-d H:i:s') ?? $cost->created_at;
            return [$dateKey, $timeKey];
        })->values();

        // Calculate running balance in effective date order
        $runningBalance = $openingBalance;
        $allCostsWithBalance = $sortedCosts->map(function ($cost) use (&$runningBalance) {
            if (in_array($cost->cost_type, self::CASH_IN_TYPES, true)) {
                $runningBalance += $cost->total_price;
            } else {
                $runningBalance -= $cost->total_price;
            }
            $cost->running_balance = $runningBalance;
            return $cost;
        });

        // Paginate the sorted list (so table order matches running balance order)
        $total = $allCostsWithBalance->count();
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $costs = new LengthAwarePaginator(
            $allCostsWithBalance->forPage($page, $this->perPage)->values(),
            $total,
            $this->perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'page']
        );
        $costsWithBalance = $costs->getCollection(); // same items, already have running_balance

        // Stats: non-cash by payment_date in period, cash by cost_date in period (same as report totals)
        $stats = [
            'service_parts' => [
                'label' => 'Service Parts',
                'total' => Cost::query()
                    ->where('cost_type', 'service_parts')
                    ->where('big_cash', '!=', 1)
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->whereHas('payments', $paymentInPeriod)
                    ->sum('total_price'),
                'count' => Cost::query()
                    ->where('cost_type', 'service_parts')
                    ->where('big_cash', '!=', 1)
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->whereHas('payments', $paymentInPeriod)
                    ->count(),
                'icon' => 'beaker',
                'color' => 'blue'
            ],
            'other_cost' => [
                'label' => 'Other Cost',
                'total' => Cost::query()
                    ->where('cost_type', 'other_cost')
                    ->where('big_cash', '!=', 1)
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->whereHas('payments', $paymentInPeriod)
                    ->sum('total_price'),
                'count' => Cost::query()
                    ->where('cost_type', 'other_cost')
                    ->where('big_cash', '!=', 1)
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->whereHas('payments', $paymentInPeriod)
                    ->count(),
                'icon' => 'wrench',
                'color' => 'orange'
            ],
            'showroom' => [
                'label' => 'Showroom',
                'total' => Cost::query()
                    ->where('cost_type', 'showroom')
                    ->where('big_cash', '!=', 1)
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->whereHas('payments', $paymentInPeriod)
                    ->sum('total_price'),
                'count' => Cost::query()
                    ->where('cost_type', 'showroom')
                    ->where('big_cash', '!=', 1)
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->whereHas('payments', $paymentInPeriod)
                    ->count(),
                'icon' => 'building-storefront',
                'color' => 'green'
            ],
            'loan' => [
                'label' => 'Loan (Out)',
                'total' => Cost::query()
                    ->where('cost_type', 'loan')
                    ->where('big_cash', '!=', 1)
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->whereHas('payments', $paymentInPeriod)
                    ->sum('total_price'),
                'count' => Cost::query()
                    ->where('cost_type', 'loan')
                    ->where('big_cash', '!=', 1)
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->whereHas('payments', $paymentInPeriod)
                    ->count(),
                'icon' => 'lifebuoy',
                'color' => 'rose'
            ],
            'loan_payment' => [
                'label' => 'Loan Payment (In)',
                'total' => Cost::query()
                    ->where('cost_type', 'loan_payment')
                    ->where('big_cash', '!=', 1)
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->whereHas('payments', $paymentInPeriod)
                    ->sum('total_price'),
                'count' => Cost::query()
                    ->where('cost_type', 'loan_payment')
                    ->where('big_cash', '!=', 1)
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->whereHas('payments', $paymentInPeriod)
                    ->count(),
                'icon' => 'receipt-refund',
                'color' => 'emerald'
            ],
            'cash' => [
                'label' => 'Cash In',
                'total' => Cost::query()
                    ->where('cost_type', 'cash')
                    ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                    ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->sum('total_price'),
                'count' => Cost::query()
                    ->where('cost_type', 'cash')
                    ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                    ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->count(),
                'icon' => 'currency-dollar',
                'color' => 'emerald'
            ]
        ];
        $warehouses = Warehouse::where('has_cash', true)->orderBy('name')->get();
        $selectedWarehouseName = $this->selectedWarehouseId
            ? optional($warehouses->firstWhere('id', $this->selectedWarehouseId))->name
            : null;

        return view('livewire.report.cash-report.cash-report-index', compact(
            'costs',
            'totalDebet',
            'totalKredit',
            'netBalance',
            'costsWithBalance',
            'openingBalance',
            'stats',
            'warehouses',
            'selectedWarehouseName'
        ));
    }

    public function exportExcel()
    {
        return Excel::download(
            new CashReportExport(null, 'cost_date', 'asc', $this->dateFrom, $this->dateTo, $this->selectedCostType, $this->selectedWarehouseId),
            'cash_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        // Same period filter as render: cash by cost_date, non-cash by payment_date in range. Exclude vehicle_tax.
        $allCostsInPeriod = Cost::query()
            ->with(['createdBy', 'vehicle', 'vendor', 'warehouse', 'payments'])
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->where('big_cash', '!=', 1)
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('cost_type', 'cash')
                        ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                        ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo));
                })->orWhere(function ($q) {
                    $q->where('cost_type', '!=', 'cash')
                        ->where('cost_type', '!=', 'vehicle_tax')
                        ->whereHas('payments', fn($q) => $q->whereDate('payment_date', '>=', $this->dateFrom)->whereDate('payment_date', '<=', $this->dateTo));
                });
            })
            ->get();

        // Opening balance before dateFrom:
        // (cash in by cost_date) + (loan_payment in by payment_date) - (cash out by payment_date). Exclude vehicle_tax.
        $cashInBefore = Cost::query()
            ->where('cost_type', 'cash')
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '<', $this->dateFrom))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->sum('total_price');

        $loanPaymentInBefore = Cost::query()
            ->where('cost_type', 'loan_payment')
            ->where('big_cash', '!=', 1)
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->whereHas('payments', fn($q) => $q->whereDate('payment_date', '<', $this->dateFrom))
            ->sum('total_price');

        $cashOutBefore = Cost::query()
            ->whereNotIn('cost_type', array_merge(self::CASH_IN_TYPES, ['vehicle_tax']))
            ->where('big_cash', '!=', 1)
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->whereHas('payments', fn($q) => $q->whereDate('payment_date', '<', $this->dateFrom))
            ->sum('total_price');

        $openingBalancePdf = $cashInBefore + $loanPaymentInBefore - $cashOutBefore;

        // Sort by effective date (cost_date / first payment_date), then by created_at (date + time)
        $sortedCosts = $allCostsInPeriod->sortBy(function ($cost) {
            $effectiveDate = $cost->cost_type === 'cash'
                ? $cost->cost_date
                : ($cost->payments->sortBy('payment_date')->first()?->payment_date ?? $cost->cost_date);
            $dateKey = \Carbon\Carbon::parse($effectiveDate)->format('Y-m-d');
            $timeKey = $cost->created_at?->format('Y-m-d H:i:s') ?? $cost->created_at;
            return [$dateKey, $timeKey];
        })->values();

        $runningBalance = $openingBalancePdf;
        $costsWithBalance = $sortedCosts->map(function ($cost) use (&$runningBalance) {
            if (in_array($cost->cost_type, self::CASH_IN_TYPES, true)) {
                $runningBalance += $cost->total_price;
            } else {
                $runningBalance -= $cost->total_price;
            }
            $cost->running_balance = $runningBalance;
            return $cost;
        });

        $costs = $costsWithBalance; // PDF shows same ordered list with running balance

        $totalDebetPdf = $costs
            ->whereNotIn('cost_type', self::CASH_IN_TYPES)
            ->where('big_cash', '!=', 1)
            ->sum('total_price') ?? 0;
        $totalKreditPdf = $costs
            ->whereIn('cost_type', self::CASH_IN_TYPES)
            ->sum('total_price') ?? 0;
        $netBalancePdf = $totalKreditPdf - $totalDebetPdf + $openingBalancePdf;

        $pdf = Pdf::loadView('exports.cash-report-pdf', compact('costs', 'costsWithBalance', 'totalDebetPdf', 'totalKreditPdf', 'netBalancePdf', 'openingBalancePdf'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'cash_report_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
