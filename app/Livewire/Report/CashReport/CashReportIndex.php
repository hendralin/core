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
        // Total debet = non-cash recognized in period (by payment_date in range). Exclude vehicle_tax (Laporan Kas Pajak).
        $totalDebet = Cost::query()
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->where('cost_type', '!=', 'cash')
            ->where('cost_type', '!=', 'vehicle_tax')
            ->where('big_cash', '!=', 1)
            ->whereHas('payments', function ($q) {
                $q->whereDate('payment_date', '>=', $this->dateFrom)
                    ->whereDate('payment_date', '<=', $this->dateTo);
            })
            ->sum('total_price');

        $totalKredit = Cost::query()
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->where('cost_type', 'cash')
            ->sum('total_price');


            // Opening balance before the period: cash in (cost_date < dateFrom) minus non-cash out (payment_date < dateFrom). Exclude vehicle_tax.
        $cashInBefore = Cost::query()
            ->where('cost_type', 'cash')
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '<', $this->dateFrom))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->sum('total_price');
        $cashOutBefore = Cost::query()
            ->where('cost_type', '!=', 'cash')
            ->where('cost_type', '!=', 'vehicle_tax')
            ->where('big_cash', '!=', 1)
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->whereHas('payments', fn($q) => $q->whereDate('payment_date', '<', $this->dateFrom))
            ->sum('total_price');
        $openingBalance = $cashInBefore - $cashOutBefore;

        $netBalance = $totalKredit - $totalDebet + $openingBalance;

        // Same period filter: cash by cost_date, non-cash by payment_date in range. Exclude vehicle_tax (Laporan Kas Pajak).
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

        // Sort by effective date: cash by cost_date, non-cash by first payment_date (when money actually moves)
        $sortedCosts = $allCostsInPeriod->sortBy(function ($cost) {
            if ($cost->cost_type === 'cash') {
                return $cost->cost_date;
            }
            $first = $cost->payments->sortBy('payment_date')->first();
            return $first ? $first->payment_date : $cost->cost_date;
        })->values();

        // Calculate running balance in effective date order
        $runningBalance = $openingBalance;
        $allCostsWithBalance = $sortedCosts->map(function ($cost) use (&$runningBalance) {
            if ($cost->cost_type === 'cash') {
                $runningBalance += $cost->total_price;
            } elseif ($cost->big_cash == 1) {
                // no change
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
        $paymentInPeriod = fn($q) => $q->whereDate('payment_date', '>=', $this->dateFrom)->whereDate('payment_date', '<=', $this->dateTo);
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

        // Opening balance: cash in before dateFrom, minus non-cash paid before dateFrom. Exclude vehicle_tax.
        $cashInBefore = Cost::query()
            ->where('cost_type', 'cash')
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '<', $this->dateFrom))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->sum('total_price');
        $cashOutBefore = Cost::query()
            ->where('cost_type', '!=', 'cash')
            ->where('cost_type', '!=', 'vehicle_tax')
            ->where('big_cash', '!=', 1)
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->whereHas('payments', fn($q) => $q->whereDate('payment_date', '<', $this->dateFrom))
            ->sum('total_price');
        $openingBalancePdf = $cashInBefore - $cashOutBefore;

        // Sort by effective date (cash = cost_date, non-cash = first payment_date)
        $sortedCosts = $allCostsInPeriod->sortBy(function ($cost) {
            if ($cost->cost_type === 'cash') {
                return $cost->cost_date;
            }
            $first = $cost->payments->sortBy('payment_date')->first();
            return $first ? $first->payment_date : $cost->cost_date;
        })->values();

        $runningBalance = $openingBalancePdf;
        $costsWithBalance = $sortedCosts->map(function ($cost) use (&$runningBalance) {
            if ($cost->cost_type === 'cash') {
                $runningBalance += $cost->total_price;
            } elseif ($cost->big_cash == 1) {
                // no change
            } else {
                $runningBalance -= $cost->total_price;
            }
            $cost->running_balance = $runningBalance;
            return $cost;
        });

        $costs = $costsWithBalance; // PDF shows same ordered list with running balance

        $totalDebetPdf = $costs->where('cost_type', '!=', 'cash')->where('big_cash', '!=', 1)->sum('total_price') ?? 0;
        $totalKreditPdf = $costs->where('cost_type', 'cash')->sum('total_price') ?? 0;
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
