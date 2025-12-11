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

        <!-- Total Cost This Month -->
        <div class="rounded-xl bg-white p-4 shadow-lg border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700 transition-shadow transition-transform duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Total Cost</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($this->totalCostThisMonth, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">Expenses this month</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/20">
                    <flux:icon.beaker class="h-5 w-5 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Sales Performance Chart -->
    <div class="mt-8">
        <div class="rounded-xl bg-white p-6 shadow-lg border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Monthly Sales Performance</h2>
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


    @push('scripts')
    <script>
        // Initialize chart variable safely
        if (typeof window.salesChart === 'undefined') {
            window.salesChart = null;
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

        // Function to safely initialize chart with delay
        function safeInitChart() {
            setTimeout(function() {
                if (document.getElementById('salesPerformanceChart')) {
                    console.log('Initializing chart via safeInitChart');
                    initSalesChart();
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
