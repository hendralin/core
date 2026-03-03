<?php

namespace App\Livewire\Report\TaxCashReport;

use App\Models\Cost;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TaxCashReportExport;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;

#[Title('Laporan Kas Pajak')]
class TaxCashReportIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $perPage = 10;
    public $dateFrom;
    public $dateTo;
    public $selectedCostType;
    public $selectedMonthYear;
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

    private function baseCostQuery()
    {
        return Cost::query()->whereIn('cost_type', ['tax_cash', 'vehicle_tax']);
    }

    public function render()
    {
        // Debet = vehicle_tax (pengeluaran PKB) dengan payment_date dalam periode
        $totalDebet = $this->baseCostQuery()
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->where('cost_type', 'vehicle_tax')
            ->whereHas('payments', function ($q) {
                $q->whereDate('payment_date', '>=', $this->dateFrom)
                    ->whereDate('payment_date', '<=', $this->dateTo);
            })
            ->sum('total_price');

        // Kredit = tax_cash (inject kas pajak) dengan cost_date dalam periode
        $totalKredit = $this->baseCostQuery()
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->where('cost_type', 'tax_cash')
            ->sum('total_price');

        // Opening balance: tax_cash in before dateFrom - vehicle_tax paid before dateFrom
        $cashInBefore = $this->baseCostQuery()
            ->where('cost_type', 'tax_cash')
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '<', $this->dateFrom))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->sum('total_price');
        $cashOutBefore = $this->baseCostQuery()
            ->where('cost_type', 'vehicle_tax')
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->whereHas('payments', fn($q) => $q->whereDate('payment_date', '<', $this->dateFrom))
            ->sum('total_price');
        $openingBalance = $cashInBefore - $cashOutBefore;

        $netBalance = $totalKredit - $totalDebet + $openingBalance;

        // Costs in period: tax_cash by cost_date, vehicle_tax by payment_date in range
        $allCostsInPeriod = $this->baseCostQuery()
            ->with(['createdBy', 'vehicle', 'vendor', 'warehouse', 'payments'])
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('cost_type', 'tax_cash')
                        ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                        ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo));
                })->orWhere(function ($q) {
                    $q->where('cost_type', 'vehicle_tax')
                        ->whereHas('payments', fn($q) => $q->whereDate('payment_date', '>=', $this->dateFrom)->whereDate('payment_date', '<=', $this->dateTo));
                });
            })
            ->get();

        $sortedCosts = $allCostsInPeriod->sortBy(function ($cost) {
            if ($cost->cost_type === 'tax_cash') {
                return $cost->cost_date;
            }
            $first = $cost->payments->sortBy('payment_date')->first();
            return $first ? $first->payment_date : $cost->cost_date;
        })->values();

        $runningBalance = $openingBalance;
        $allCostsWithBalance = $sortedCosts->map(function ($cost) use (&$runningBalance) {
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

        $total = $allCostsWithBalance->count();
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $costs = new LengthAwarePaginator(
            $allCostsWithBalance->forPage($page, $this->perPage)->values(),
            $total,
            $this->perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'page']
        );
        $costsWithBalance = $costs->getCollection();

        $paymentInPeriod = fn($q) => $q->whereDate('payment_date', '>=', $this->dateFrom)->whereDate('payment_date', '<=', $this->dateTo);
        $stats = [
            'tax_cash' => [
                'label' => 'Kas Pajak (In)',
                'total' => $this->baseCostQuery()
                    ->where('cost_type', 'tax_cash')
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                    ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
                    ->sum('total_price'),
                'count' => $this->baseCostQuery()
                    ->where('cost_type', 'tax_cash')
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                    ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
                    ->count(),
                'icon' => 'currency-dollar',
                'color' => 'emerald'
            ],
            'vehicle_tax' => [
                'label' => 'Pembayaran PKB (Out)',
                'total' => $this->baseCostQuery()
                    ->where('cost_type', 'vehicle_tax')
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->whereHas('payments', $paymentInPeriod)
                    ->sum('total_price'),
                'count' => $this->baseCostQuery()
                    ->where('cost_type', 'vehicle_tax')
                    ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
                    ->whereHas('payments', $paymentInPeriod)
                    ->count(),
                'icon' => 'banknotes',
                'color' => 'blue'
            ]
        ];

        $warehouses = Warehouse::orderBy('name')->get();
        $selectedWarehouseName = $this->selectedWarehouseId
            ? optional($warehouses->firstWhere('id', $this->selectedWarehouseId))->name
            : null;

        return view('livewire.report.tax-cash-report.tax-cash-report-index', compact(
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
            new TaxCashReportExport('cost_date', 'asc', $this->dateFrom, $this->dateTo, $this->selectedCostType, $this->selectedWarehouseId),
            'laporan_kas_pajak_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $allCostsInPeriod = $this->baseCostQuery()
            ->with(['createdBy', 'vehicle', 'warehouse', 'payments'])
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('cost_type', 'tax_cash')
                        ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                        ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo));
                })->orWhere(function ($q) {
                    $q->where('cost_type', 'vehicle_tax')
                        ->whereHas('payments', fn($q) => $q->whereDate('payment_date', '>=', $this->dateFrom)->whereDate('payment_date', '<=', $this->dateTo));
                });
            })
            ->get();

        $cashInBefore = $this->baseCostQuery()
            ->where('cost_type', 'tax_cash')
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '<', $this->dateFrom))
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->sum('total_price');
        $cashOutBefore = $this->baseCostQuery()
            ->where('cost_type', 'vehicle_tax')
            ->when($this->selectedWarehouseId, fn($q) => $q->where('warehouse_id', $this->selectedWarehouseId))
            ->whereHas('payments', fn($q) => $q->whereDate('payment_date', '<', $this->dateFrom))
            ->sum('total_price');
        $openingBalancePdf = $cashInBefore - $cashOutBefore;

        $sortedCosts = $allCostsInPeriod->sortBy(function ($cost) {
            if ($cost->cost_type === 'tax_cash') {
                return $cost->cost_date;
            }
            $first = $cost->payments->sortBy('payment_date')->first();
            return $first ? $first->payment_date : $cost->cost_date;
        })->values();

        $runningBalance = $openingBalancePdf;
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

        $costs = $costsWithBalance;
        $totalDebetPdf = $costs->where('cost_type', 'vehicle_tax')->sum('total_price') ?? 0;
        $totalKreditPdf = $costs->where('cost_type', 'tax_cash')->sum('total_price') ?? 0;
        $netBalancePdf = $totalKreditPdf - $totalDebetPdf + $openingBalancePdf;

        $pdf = Pdf::loadView('exports.tax-cash-report-pdf', compact('costs', 'costsWithBalance', 'totalDebetPdf', 'totalKreditPdf', 'netBalancePdf', 'openingBalancePdf'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan_kas_pajak_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
