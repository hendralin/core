<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use App\Models\Cost;
use App\Models\Vehicle;
use App\Models\Warehouse;
use Livewire\Component;

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

    public function getFinalCashBalanceProperty()
    {
        // Cash in: all cost_type = 'cash'
        $totalCashIn = Cost::where('cost_type', 'cash')->sum('total_price');

        // Other costs: only recognized when they have payment (payment_date)
        $totalCosts = Cost::query()
            ->where('cost_type', '!=', 'cash')
            ->where('cost_type', '!=', 'vehicle_tax')
            ->where('big_cash', 0)
            ->whereHas('payments')
            ->sum('total_price');

        return $totalCashIn - $totalCosts;
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

    /**
     * Total engagement from public vehicle catalog (all vehicles).
     */
    public function getPublicCatalogEngagementProperty()
    {
        return [
            'page_views' => (int) Vehicle::sum('public_page_view_count'),
            'chat_whatsapp' => (int) Vehicle::sum('chat_whatsapp_count'),
            'share_whatsapp' => (int) Vehicle::sum('whatsapp_share_count'),
            'link_copy' => (int) Vehicle::sum('link_copy_count'),
        ];
    }

    public function getAvailableStockByWarehouseProperty()
    {
        return Warehouse::withCount([
            'vehicles as available_count' => function ($query) {
                $query->where('status', '1');
            },
        ])
            ->get()
            ->filter(function ($warehouse) {
                return $warehouse->available_count > 0;
            })
            ->map(function ($warehouse) {
                return [
                    'label' => $warehouse->name,
                    'value' => $warehouse->available_count,
                ];
            })
            ->values()
            ->toArray();
    }

    public function getCashBalanceByWarehouseProperty()
    {
        // Cash in per warehouse (cost_type = cash)
        $cashInPerWarehouse = Cost::selectRaw('warehouse_id, SUM(total_price) as total_cash_in')
            ->where('cost_type', 'cash')
            ->groupBy('warehouse_id')
            ->pluck('total_cash_in', 'warehouse_id');

        // Other costs per warehouse (same logic as global finalCashBalance, but grouped)
        $costsPerWarehouse = Cost::selectRaw('warehouse_id, SUM(total_price) as total_costs')
            ->where('cost_type', '!=', 'cash')
            ->where('cost_type', '!=', 'vehicle_tax')
            ->where('big_cash', 0)
            ->whereHas('payments')
            ->groupBy('warehouse_id')
            ->pluck('total_costs', 'warehouse_id');

        $warehouses = Warehouse::orderBy('name')->get();

        return $warehouses->map(function ($warehouse) use ($cashInPerWarehouse, $costsPerWarehouse) {
            $cashIn = (float) ($cashInPerWarehouse[$warehouse->id] ?? 0);
            $costs = (float) ($costsPerWarehouse[$warehouse->id] ?? 0);
            $balance = $cashIn - $costs;

            return [
                'label' => $warehouse->name,
                'cash_in' => $cashIn,
                'costs' => $costs,
                'balance' => $balance,
            ];
        })
            // tampilkan hanya gudang yang punya aktivitas keuangan
            ->filter(function ($item) {
                return $item['cash_in'] != 0 || $item['costs'] != 0 || $item['balance'] != 0;
            })
            ->values()
            ->toArray();
    }

    /**
     * Saldo kas pajak per gudang: tax_cash (Kas Pajak In) - vehicle_tax dengan payment (Pembayaran PKB Out).
     */
    public function getTaxCashBalanceByWarehouseProperty()
    {
        $taxCashInPerWarehouse = Cost::selectRaw('warehouse_id, SUM(total_price) as total')
            ->where('cost_type', 'tax_cash')
            ->groupBy('warehouse_id')
            ->pluck('total', 'warehouse_id');

        $vehicleTaxOutPerWarehouse = Cost::selectRaw('warehouse_id, SUM(total_price) as total')
            ->where('cost_type', 'vehicle_tax')
            ->whereHas('payments')
            ->groupBy('warehouse_id')
            ->pluck('total', 'warehouse_id');

        $warehouses = Warehouse::orderBy('name')->get();

        return $warehouses->map(function ($warehouse) use ($taxCashInPerWarehouse, $vehicleTaxOutPerWarehouse) {
            $cashIn = (float) ($taxCashInPerWarehouse[$warehouse->id] ?? 0);
            $costs = (float) ($vehicleTaxOutPerWarehouse[$warehouse->id] ?? 0);
            $balance = $cashIn - $costs;

            return [
                'label' => $warehouse->name,
                'cash_in' => $cashIn,
                'costs' => $costs,
                'balance' => $balance,
            ];
        })
            ->filter(function ($item) {
                return $item['cash_in'] != 0 || $item['costs'] != 0 || $item['balance'] != 0;
            })
            ->values()
            ->toArray();
    }

    public function render()
    {
        if (auth()->user()->hasRole('salesman')) {
            return view('livewire.dashboard.dashboard-salesman');
        }

        if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('finance')) {
            return view('livewire.dashboard.dashboard-index');
        }

        return view('livewire.dashboard.dashboard-empty');
    }
}
