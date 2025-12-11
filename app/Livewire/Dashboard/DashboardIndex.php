<?php

namespace App\Livewire\Dashboard;

use App\Models\Vehicle;
use App\Models\Cost;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;

class DashboardIndex extends Component
{
    public function getVehiclesSoldThisMonthProperty()
    {
        return Vehicle::where('status', '0')
            ->whereYear('selling_date', Carbon::now()->year)
            ->whereMonth('selling_date', Carbon::now()->month)
            ->count();
    }

    public function getNewVehiclesThisMonthProperty()
    {
        return Vehicle::whereYear('purchase_date', Carbon::now()->year)
            ->whereMonth('purchase_date', Carbon::now()->month)
            ->count();
    }

    public function getVehiclesReadyForSaleProperty()
    {
        return Vehicle::where('status', '1')->count();
    }

    public function getTotalCostThisMonthProperty()
    {
        return Cost::whereYear('cost_date', Carbon::now()->year)
            ->whereMonth('cost_date', Carbon::now()->month)
            ->whereNotIn('cost_type', ['cash'])
            ->sum('total_price');
    }

    public function getTotalSalesThisYearProperty()
    {
        return Vehicle::where('status', '0')
            ->whereYear('selling_date', Carbon::now()->year)
            ->sum('selling_price') ?? 0;
    }

    public function getTotalSalesThisMonthProperty()
    {
        return Vehicle::where('status', '0')
            ->whereYear('selling_date', Carbon::now()->year)
            ->whereMonth('selling_date', Carbon::now()->month)
            ->sum('selling_price');
    }

    public function getMonthlySalesPerformanceProperty()
    {
        $data = [];

        // Ambil data penjualan per bulan yang memiliki penjualan (tidak 0)
        // Urutkan dari yang terlama ke terbaru, maksimal 12 bulan terakhir
        $salesData = Vehicle::selectRaw('
                YEAR(selling_date) as year,
                MONTH(selling_date) as month,
                SUM(selling_price) as total_sales,
                COUNT(*) as total_count
            ')
            ->where('status', '0')
            ->whereNotNull('selling_date')
            ->groupByRaw('YEAR(selling_date), MONTH(selling_date)')
            ->orderByRaw('YEAR(selling_date) DESC, MONTH(selling_date) DESC')
            ->limit(12)
            ->get();

        if ($salesData->isEmpty()) {
            return $data;
        }

        // Balik urutan agar dari terlama ke terbaru untuk chart
        $salesData = $salesData->reverse();

        foreach ($salesData as $sale) {
            $date = Carbon::createFromDate($sale->year, $sale->month, 1);

            $data[] = [
                'month' => $date->format('M Y'),
                'sales' => $sale->total_sales,
                'count' => $sale->total_count
            ];
        }

        return $data;
    }

    public function getAverageMonthlySalesProperty()
    {
        $monthlyData = $this->monthlySalesPerformance;
        $totalSales = collect($monthlyData)->sum('sales');
        $monthCount = count($monthlyData);

        return $monthCount > 0 ? $totalSales / $monthCount : 0;
    }

    public function getAverageOrderValueProperty()
    {
        $monthlyData = $this->monthlySalesPerformance;
        $totalSales = collect($monthlyData)->sum('sales');
        $totalVehiclesSold = collect($monthlyData)->sum('count');

        if ($totalVehiclesSold === 0) {
            return 0;
        }

        return $totalSales / $totalVehiclesSold;
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-index');
    }
}
