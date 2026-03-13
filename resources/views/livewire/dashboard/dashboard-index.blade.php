<div>
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                <p class="text-gray-600 dark:text-zinc-400 mt-2">Welcome back, {{ auth()->user()->name }}</p>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Here's your business overview for {{ now()->format('F Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Vehicles Sold This Month -->
        <div class="rounded-xl bg-white p-4 shadow-lg border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700 transition-shadow transition-transform duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Vehicles Sold</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->vehiclesSoldThisMonth }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">This month</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <flux:icon.shopping-cart class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>
        </div>

        <!-- Total Sales This Month -->
        <div class="rounded-xl bg-white p-4 shadow-lg border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700 transition-shadow transition-transform duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Total Sales</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($this->totalSalesThisMonth, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Revenue this month</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <flux:icon.currency-dollar class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <!-- Vehicles Ready for Sale -->
        <div class="rounded-xl bg-white p-4 shadow-lg border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700 transition-shadow transition-transform duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Ready for Sale</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->vehiclesReadyForSale }}</p>
                    {{-- <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Available vehicles</p> --}}
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">+{{ $this->newVehiclesThisMonth }} new this month</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                    <flux:icon.truck class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
        </div>

        <!-- Cash Balance -->
        <div class="rounded-xl bg-white p-4 shadow-lg border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700 transition-shadow transition-transform duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Cash Balance</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($this->finalCashBalance, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Todays cash balance</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <flux:icon.currency-dollar class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>
        </div>
    </div>

    <!-- Public catalog engagement stats -->
    @php
        $engagement = $this->publicCatalogEngagement;
        $totalEngagement = $engagement['page_views'] + $engagement['chat_whatsapp'] + $engagement['share_whatsapp'] + $engagement['link_copy'];
    @endphp
    <div class="mt-8">
        <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2.5 rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
                    <flux:icon.chat-bubble-left-right class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Engagement Katalog</h2>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">
                        Statistik interaksi pengunjung di katalog & halaman detail kendaraan.
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                <div class="flex items-center gap-4 rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-50/50 dark:bg-zinc-900/30 p-4 hover:shadow-md transition-shadow">
                    <div class="shrink-0 p-2.5 rounded-lg bg-violet-100 dark:bg-violet-900/30">
                        <flux:icon.eye class="h-5 w-5 text-violet-600 dark:text-violet-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Halaman dikunjungi</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums mt-0.5">{{ number_format($engagement['page_views']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Kunjungan halaman detail</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-50/50 dark:bg-zinc-900/30 p-4 hover:shadow-md transition-shadow">
                    <div class="shrink-0 p-2.5 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <img src="//upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" class="h-8 w-8" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Chat WhatsApp</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums mt-0.5">{{ number_format($engagement['chat_whatsapp']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Klik tombol hubungi WA</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-50/50 dark:bg-zinc-900/30 p-4 hover:shadow-md transition-shadow">
                    <div class="shrink-0 p-2.5 rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                        <flux:icon.paper-airplane class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Share ke WhatsApp</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums mt-0.5">{{ number_format($engagement['share_whatsapp']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Bagikan link iklan</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-50/50 dark:bg-zinc-900/30 p-4 hover:shadow-md transition-shadow">
                    <div class="shrink-0 p-2.5 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                        <flux:icon.link class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Link disalin</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums mt-0.5">{{ number_format($engagement['link_copy']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Salin link ke clipboard</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700 flex items-center justify-between flex-wrap gap-2">
                <p class="text-sm text-gray-500 dark:text-zinc-400">
                    Total kunjungan & interaksi: <span class="font-semibold text-gray-700 dark:text-zinc-200">{{ number_format($totalEngagement) }}</span>
                </p>
                @if($totalEngagement > 0 && $this->vehiclesReadyForSale > 0)
                    <p class="text-xs text-gray-500 dark:text-zinc-400">
                        Rata-rata per kendaraan tersedia: <span class="font-medium text-gray-700 dark:text-zinc-300">{{ number_format(round($totalEngagement / $this->vehiclesReadyForSale, 1)) }}</span>
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Monthly Sales Performance Chart -->
    <div class="mt-8">
        <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Monthly Sales Performance</h2>
                <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">
                    Grafik performa penjualan bulanan, membantu mengidentifikasi tren dan pola penjualan.
                </p>
            </div>
            <div class="h-80">
                <canvas id="salesPerformanceChart"></canvas>
            </div>
            <!-- Stats Below Chart -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">Total Sales</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-zinc-100 mt-1">Rp {{ number_format($this->totalSalesThisYear, 0, ',', '.') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">This Month</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-zinc-100 mt-1">Rp {{ number_format($this->totalSalesThisMonth, 0, ',', '.') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">Avg Monthly</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-zinc-100 mt-1">Rp {{ number_format($this->averageMonthlySales, 0, ',', '.') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500 dark:text-zinc-400">Avg Order Value</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-zinc-100 mt-1">Rp {{ number_format($this->averageOrderValue, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Vehicle & Cash Balance per Warehouse -->
    <div class="mt-8 grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Stock Available per Warehouse -->
        <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Stock Vehicle per Gudang</h2>
                <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">
                    Ringkasan stok kendaraan per gudang, memudahkan pemantauan stok kendaraan per lokasi.
                </p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
                <div>
                    <ul class="space-y-2">
                        @forelse ($this->availableStockByWarehouse as $warehouse)
                            <li class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-zinc-700 px-3 py-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-zinc-100">
                                    {{ $warehouse['label'] }}
                                </span>
                                <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                                    {{ $warehouse['value'] }} unit
                                </span>
                            </li>
                        @empty
                            <li class="text-sm text-gray-500 dark:text-zinc-400">
                                Belum ada stock vehicle yang tersedia.
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="h-64">
                    <canvas id="warehouseStockChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Cash Balance per Warehouse -->
        <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Cash Balance per Warehouse</h2>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">
                        Ringkasan saldo kas per gudang, memudahkan pemantauan arus kas per lokasi.
                    </p>
                </div>
                <div class="hidden sm:flex sm:flex-col items-end gap-1 text-xs text-gray-500 dark:text-zinc-400">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-800 whitespace-nowrap">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Positif = surplus
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-rose-50 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border border-rose-100 dark:border-rose-800 whitespace-nowrap">
                        <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                        Negatif = defisit
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-zinc-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-900/40">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                Gudang
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                Cash In
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                Cash Out
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                Balance
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-100 dark:divide-zinc-700">
                        @forelse ($this->cashBalanceByWarehouse as $item)
                            @php
                                $balance = $item['balance'];
                                $isPositive = $balance >= 0;
                            @endphp
                            <tr class="odd:bg-white even:bg-gray-50/60 dark:odd:bg-zinc-800 dark:even:bg-zinc-900/40 hover:bg-gray-100 dark:hover:bg-zinc-800/80 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-zinc-100 whitespace-nowrap">
                                    {{ $item['label'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right tabular-nums text-gray-700 dark:text-zinc-200 whitespace-nowrap">
                                    Rp {{ number_format($item['cash_in'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right tabular-nums text-gray-700 dark:text-zinc-200 whitespace-nowrap">
                                    Rp {{ number_format($item['costs'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                                    <span class="inline-flex items-center justify-end gap-1 px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $isPositive
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
                                            : 'bg-rose-50 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $isPositive ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        Rp {{ number_format($balance, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-zinc-400">
                                    Belum ada data cash flow per warehouse.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Saldo Kas Pajak per Warehouse -->
        <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Saldo Kas Pajak per Gudang</h2>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">
                        Ringkasan saldo kas pajak per gudang: Kas Pajak minus Pembayaran PKB.
                    </p>
                </div>
                <div class="hidden sm:flex sm:flex-col items-end gap-1 text-xs text-gray-500 dark:text-zinc-400">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-800 whitespace-nowrap">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Positif = surplus
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-rose-50 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border border-rose-100 dark:border-rose-800 whitespace-nowrap">
                        <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                        Negatif = defisit
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-zinc-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-900/40">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                Gudang
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                Cash In
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                Cash Out
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                Balance
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-100 dark:divide-zinc-700">
                        @forelse ($this->taxCashBalanceByWarehouse as $item)
                            @php
                                $balance = $item['balance'];
                                $isPositive = $balance >= 0;
                            @endphp
                            <tr class="odd:bg-white even:bg-gray-50/60 dark:odd:bg-zinc-800 dark:even:bg-zinc-900/40 hover:bg-gray-100 dark:hover:bg-zinc-800/80 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-zinc-100 whitespace-nowrap">
                                    {{ $item['label'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right tabular-nums text-gray-700 dark:text-zinc-200 whitespace-nowrap">
                                    Rp {{ number_format($item['cash_in'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right tabular-nums text-gray-700 dark:text-zinc-200 whitespace-nowrap">
                                    Rp {{ number_format($item['costs'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                                    <span class="inline-flex items-center justify-end gap-1 px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $isPositive
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
                                            : 'bg-rose-50 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $isPositive ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        Rp {{ number_format($balance, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-zinc-400">
                                    Belum ada data kas pajak per warehouse.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    @push('scripts')
    <script>
        // Initialize chart variables safely
        if (typeof window.salesChart === 'undefined') {
            window.salesChart = null;
        }
        if (typeof window.warehouseStockChart === 'undefined') {
            window.warehouseStockChart = null;
        }

        function initSalesChart() {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded');
                return;
            }

            const canvas = document.getElementById('salesPerformanceChart');
            if (!canvas) {
                console.log('Canvas element not found');
                return;
            }

            // Destroy existing chart if it exists
            if (window.salesChart && window.salesChart.canvas === canvas) {
                window.salesChart.destroy();
                window.salesChart = null;
            }

            let salesData = @json($this->monthlySalesPerformance);

            if (!salesData || salesData.length === 0) {
                // Create a placeholder chart with empty data
                salesData = [];
                for (let i = 11; i >= 0; i--) {
                    const date = new Date();
                    date.setMonth(date.getMonth() - i);
                    salesData.push({
                        month: date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' }),
                        sales: 0,
                        count: 0
                    });
                }
            }

            const ctx = canvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.1)');

            try {
                window.salesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: salesData.map(item => item.month),
                        datasets: [{
                            label: 'Sales Revenue (Rp)',
                            data: salesData.map(item => item.sales),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: 'rgb(59, 130, 246)',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: 'rgba(59, 130, 246, 0.5)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    title: function(context) {
                                        return context[0].label;
                                    },
                                    label: function(context) {
                                        return 'Revenue: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)',
                                    borderDash: [5, 5]
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                            notation: 'compact',
                                            maximumFractionDigits: 1
                                        }).format(value);
                                    },
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        animation: {
                            duration: 2000,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            } catch (error) {
                console.error('Error creating chart:', error);
            }
        }

        function initWarehouseStockChart() {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded');
                return;
            }

            const canvas = document.getElementById('warehouseStockChart');
            if (!canvas) {
                return;
            }

            if (window.warehouseStockChart && window.warehouseStockChart.canvas === canvas) {
                window.warehouseStockChart.destroy();
                window.warehouseStockChart = null;
            }

            const stockData = @json($this->availableStockByWarehouse);

            if (!stockData || stockData.length === 0) {
                return;
            }

            const ctx = canvas.getContext('2d');

            const labels = stockData.map(item => item.label);
            const values = stockData.map(item => item.value);

            const baseColors = [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(249, 115, 22, 0.8)',
                'rgba(236, 72, 153, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(234, 179, 8, 0.8)',
                'rgba(248, 113, 113, 0.8)',
            ];

            const backgroundColors = labels.map((_, index) => baseColors[index % baseColors.length]);

            try {
                window.warehouseStockChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: backgroundColors,
                            borderColor: '#ffffff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        return `${label}: ${value} unit`;
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error creating warehouse stock chart:', error);
            }
        }

        // Function to safely initialize charts with delay
        function safeInitChart() {
            setTimeout(function() {
                if (document.getElementById('salesPerformanceChart')) {
                    initSalesChart();
                }
                if (document.getElementById('warehouseStockChart')) {
                    initWarehouseStockChart();
                }
            }, 150);
        }

        // Initialize chart when page loads
        document.addEventListener('DOMContentLoaded', function() {
            safeInitChart();
        });

        // Re-initialize chart when Livewire updates the component
        document.addEventListener('livewire:updated', function() {
            safeInitChart();
        });

        // Handle Livewire navigation events
        document.addEventListener('livewire:loaded', function() {
            safeInitChart();
        });

        // Handle Livewire navigation completion
        document.addEventListener('livewire:navigated', function() {
            safeInitChart();
        });

        // Additional fallback for wire:navigate
        document.addEventListener('turbo:load', function() {
            safeInitChart();
        });

        // Fallback: try to initialize chart after a short delay
        setTimeout(function() {
            if (document.getElementById('salesPerformanceChart') && !window.salesChart) {
                console.log('Initializing chart via fallback timeout');
                initSalesChart();
            }
        }, 500);
    </script>
    @endpush
</div>
