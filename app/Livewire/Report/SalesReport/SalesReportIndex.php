<?php

namespace App\Livewire\Report\SalesReport;

use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SalesReportExport;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Laporan Penjualan')]
class SalesReportIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $perPage = 10;
    public $dateFrom;
    public $dateTo;
    public $selectedMonthYear; // Format: YYYY-MM

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function updating($field)
    {
        if (in_array($field, ['perPage', 'dateFrom', 'dateTo', 'selectedMonthYear'])) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->selectedMonthYear = null;
        $this->updateDateRange();
        $this->resetPage();
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

    public function render()
    {
        $vehicles = Vehicle::query()
            ->with(['brand', 'vehicle_model', 'type', 'category', 'salesman', 'warehouse'])
            ->whereNotNull('selling_date')
            ->when($this->dateFrom, fn($q) => $q->whereDate('selling_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('selling_date', '<=', $this->dateTo))
            ->orderBy('selling_date', 'desc')
            ->paginate($this->perPage);

        // Calculate summary statistics
        $totalSales = Vehicle::query()
            ->whereNotNull('selling_date')
            ->when($this->dateFrom, fn($q) => $q->whereDate('selling_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('selling_date', '<=', $this->dateTo))
            ->sum('selling_price');

        $totalVehicles = Vehicle::query()
            ->whereNotNull('selling_date')
            ->when($this->dateFrom, fn($q) => $q->whereDate('selling_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('selling_date', '<=', $this->dateTo))
            ->count();

        $averagePrice = $totalVehicles > 0 ? $totalSales / $totalVehicles : 0;

        // Calculate total profit = total sales - total cost (purchase + costs + commissions)
        $soldVehicles = Vehicle::query()
            ->with(['costs', 'commissions'])
            ->whereNotNull('selling_date')
            ->when($this->dateFrom, fn($q) => $q->whereDate('selling_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('selling_date', '<=', $this->dateTo))
            ->get();

        $totalCost = $soldVehicles->sum(function ($vehicle) {
            $purchasePrice = $vehicle->purchase_price ?? 0;
            $totalCosts = $vehicle->costs->sum('total_price');
            $purchaseCommissions = $vehicle->commissions->where('type', 2)->sum('amount');
            return $purchasePrice + $totalCosts + $purchaseCommissions;
        });

        $totalProfit = $totalSales - $totalCost;

        // Calculate average profit margin
        $averageProfitMargin = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;

        $stats = [
            'total_sales' => [
                'label' => 'Total Penjualan',
                'value' => $totalSales,
                'formatted' => 'Rp ' . number_format($totalSales, 0),
                'icon' => 'currency-dollar',
                'color' => 'green'
            ],
            'total_vehicles' => [
                'label' => 'Total Kendaraan Terjual',
                'value' => $totalVehicles,
                'formatted' => number_format($totalVehicles, 0) . ' unit',
                'icon' => 'truck',
                'color' => 'blue'
            ],
            'total_profit' => [
                'label' => 'Total Keuntungan',
                'value' => $totalProfit,
                'formatted' => 'Rp ' . number_format($totalProfit, 0),
                'icon' => 'banknotes',
                'color' => 'emerald'
            ],
            'profit_margin' => [
                'label' => '% Margin Keuntungan',
                'value' => $averageProfitMargin,
                'formatted' => number_format($averageProfitMargin, 1) . '%',
                'icon' => 'percent-badge',
                'color' => 'purple'
            ]
        ];

        return view('livewire.report.sales-report.sales-report-index', compact('vehicles', 'stats'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new SalesReportExport(null, 'selling_date', 'desc', $this->dateFrom, $this->dateTo),
            'sales_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $vehicles = Vehicle::query()
            ->with(['brand', 'vehicle_model', 'type', 'category', 'salesman', 'warehouse', 'costs', 'commissions'])
            ->whereNotNull('selling_date')
            ->when($this->dateFrom, fn($q) => $q->whereDate('selling_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('selling_date', '<=', $this->dateTo))
            ->orderBy('selling_date', 'desc')
            ->get();

        // Calculate summary statistics
        $totalSales = $vehicles->sum('selling_price');
        $totalVehicles = $vehicles->count();

        $totalCost = $vehicles->sum(function ($vehicle) {
            $purchasePrice = $vehicle->purchase_price ?? 0;
            $totalCosts = $vehicle->costs->sum('total_price');
            $purchaseCommissions = $vehicle->commissions->where('type', 2)->sum('amount');
            return $purchasePrice + $totalCosts + $purchaseCommissions;
        });

        $totalProfit = $totalSales - $totalCost;
        $averageProfitMargin = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;

        $stats = [
            'total_sales' => $totalSales,
            'total_vehicles' => $totalVehicles,
            'total_profit' => $totalProfit,
            'profit_margin' => $averageProfitMargin,
        ];

        $pdf = Pdf::loadView('exports.sales-report-pdf', compact('vehicles', 'stats'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'sales_report_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
