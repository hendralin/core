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
    protected $paymentType;
    protected $salesmanId;
    protected $policeNumberSearch;

    public function __construct($search = '', $sortField = 'selling_date', $sortDirection = 'desc', $dateFrom = '', $dateTo = '', $paymentType = null, $salesmanId = null, $policeNumberSearch = '')
    {
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->paymentType = $paymentType;
        $this->salesmanId = $salesmanId;
        $this->policeNumberSearch = $policeNumberSearch;
    }

    public function view(): View
    {
        $vehicles = Vehicle::query()
            ->with(['brand', 'vehicle_model', 'type', 'category', 'salesman', 'warehouse', 'costs', 'commissions'])
            ->whereNotNull('selling_date')
            ->when(!empty($this->dateFrom), fn($q) => $q->whereDate('selling_date', '>=', $this->dateFrom))
            ->when(!empty($this->dateTo), fn($q) => $q->whereDate('selling_date', '<=', $this->dateTo))
            ->when(!empty($this->paymentType), fn($q) => $q->where('payment_type', $this->paymentType))
            ->when(!empty($this->salesmanId), fn($q) => $q->where('salesman_id', $this->salesmanId))
            ->when($this->policeNumberSearch !== '', fn($q) => $q->where('police_number', 'like', '%' . $this->policeNumberSearch . '%'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        // Calculate summary statistics
        $totalSales = $vehicles->sum('selling_price');
        $totalVehicles = $vehicles->count();

        $totalCost = $vehicles->sum(function ($vehicle) {
            $purchasePrice = $vehicle->purchase_price ?? 0;
            $totalCosts = $vehicle->costs->where('cost_type', '!=', 'sales_commission')->where('cost_type', '!=', 'purchase_commission')->sum('total_price');
            $purchaseCommissions = $vehicle->commissions->where('type', 2)->sum('amount');
            $roadsideAllowance = $vehicle->roadside_allowance ?? 0;
            return $purchasePrice + $totalCosts + $purchaseCommissions + $roadsideAllowance;
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
