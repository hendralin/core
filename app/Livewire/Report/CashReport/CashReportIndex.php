<?php

namespace App\Livewire\Report\CashReport;

use App\Models\Cost;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CashReportExport;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Laporan Kas')]
class CashReportIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $perPage = 10;
    public $dateFrom;
    public $dateTo;
    public $selectedCostType;
    public $selectedMonthYear; // Format: YYYY-MM

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function updating($field)
    {
        if (in_array($field, ['perPage', 'dateFrom', 'dateTo', 'selectedCostType', 'selectedMonthYear'])) {
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
        $costs = Cost::query()
            ->with(['createdBy', 'vehicle', 'vendor'])
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->where('big_cash', '!=', 1) // Exclude big cash payments from display
            ->orderBy('cost_date', 'asc')
            ->paginate($this->perPage);

        // Calculate totals for current filters
        $totalDebet = Cost::query()
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->where('cost_type', '!=', 'cash')
            ->where('big_cash', '!=', 1) // Exclude big cash payments from expense calculation
            ->sum('total_price');

        $totalKredit = Cost::query()
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->where('cost_type', 'cash')
            ->sum('total_price');


            // Calculate opening balance (balance before the selected period)
        $openingBalance = Cost::query()
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '<', $this->dateFrom))
            ->get()->sum(function ($item) {
                if ($item->cost_type === 'cash') {
                    return $item->total_price;
                } elseif ($item->big_cash == 1) {
                    return 0; // Big cash payments don't affect balance
                } else {
                    return -$item->total_price;
                }
            }) ?? 0;

        $netBalance = $totalKredit - $totalDebet + $openingBalance;

        // Get all costs in the period for accurate running balance calculation
        $allCostsInPeriod = Cost::query()
            ->with(['vehicle', 'vendor'])
            ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->when($this->selectedCostType, fn($q) => $q->where('cost_type', $this->selectedCostType))
            ->where('big_cash', '!=', 1) // Exclude big cash payments from running balance calculation
            ->orderBy('cost_date', 'asc')
            ->get();

        // Calculate running balance for all costs in period
        $runningBalance = $openingBalance;
        $allCostsWithBalance = $allCostsInPeriod->map(function ($cost) use (&$runningBalance) {
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

        // Map running balance to paginated costs
        $costsWithBalance = $costs->map(function ($cost) use ($allCostsWithBalance) {
            $matchedCost = $allCostsWithBalance->firstWhere('id', $cost->id);
            $cost->running_balance = $matchedCost ? $matchedCost->running_balance : 0;
            return $cost;
        });

        // Calculate stats for each cost type
        $stats = [
            'service_parts' => [
                'label' => 'Service Parts',
                'total' => Cost::query()
                    ->where('cost_type', 'service_parts')
                    ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                    ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
                    ->where('big_cash', '!=', 1) // Exclude big cash payments
                    ->sum('total_price'),
                'count' => Cost::query()
                    ->where('cost_type', 'service_parts')
                    ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                    ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
                    ->where('big_cash', '!=', 1) // Exclude big cash payments
                    ->count(),
                'icon' => 'beaker',
                'color' => 'blue'
            ],
            'other_cost' => [
                'label' => 'Other Cost',
                'total' => Cost::query()
                    ->where('cost_type', 'other_cost')
                    ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                    ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
                    ->where('big_cash', '!=', 1) // Exclude big cash payments
                    ->sum('total_price'),
                'count' => Cost::query()
                    ->where('cost_type', 'other_cost')
                    ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                    ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
                    ->where('big_cash', '!=', 1) // Exclude big cash payments
                    ->count(),
                'icon' => 'wrench',
                'color' => 'orange'
            ],
            'showroom' => [
                'label' => 'Showroom',
                'total' => Cost::query()
                    ->where('cost_type', 'showroom')
                    ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                    ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
                    ->sum('total_price'),
                'count' => Cost::query()
                    ->where('cost_type', 'showroom')
                    ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                    ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
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
                    ->sum('total_price'),
                'count' => Cost::query()
                    ->where('cost_type', 'cash')
                    ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
                    ->when(!empty($this->dateTo), fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
                    ->count(),
                'icon' => 'currency-dollar',
                'color' => 'emerald'
            ]
        ];

        return view('livewire.report.cash-report.cash-report-index', compact('costs', 'totalDebet', 'totalKredit', 'netBalance', 'costsWithBalance', 'openingBalance', 'stats'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new CashReportExport(null, 'cost_date', 'asc', $this->dateFrom, $this->dateTo, $this->selectedCostType),
            'cash_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $costs = Cost::query()
            ->with(['createdBy', 'vehicle', 'vendor'])
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('cost_date', '<=', $this->dateTo))
            ->where('big_cash', '!=', 1) // Exclude big cash payments from PDF export
            ->orderBy('cost_date', 'asc')
            ->get();

        // Calculate opening balance for export
        $openingBalancePdf = Cost::query()
            ->when($this->dateFrom, fn($q) => $q->whereDate('cost_date', '<', $this->dateFrom))
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
        $runningBalance = $openingBalancePdf;
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

        // Calculate totals for PDF export
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
