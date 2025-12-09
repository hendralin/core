<?php

namespace App\Exports;

use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesReportExport implements FromView
{
    protected $search;
    protected $sortField;
    protected $sortDirection;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($search = '', $sortField = 'selling_date', $sortDirection = 'desc', $dateFrom = '', $dateTo = '')
    {
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function view(): View
    {
        $vehicles = Vehicle::query()
            ->with(['brand', 'vehicle_model', 'type', 'category', 'salesman', 'warehouse', 'costs', 'commissions'])
            ->whereNotNull('selling_date')
            ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('selling_date', '>=', $this->dateFrom))
            ->when(!empty($this->dateTo), fn($q) => $q->whereDate('selling_date', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
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

        return view('exports.sales-report', [
            'vehicles' => $vehicles,
            'stats' => $stats,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ]);
    }
}
