<div class="min-h-screen" data-stock-detail>
    <!-- TradingView Style Header -->
    <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('stock-summary.index') }}" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white transition-colors" wire:navigate>
                        <flux:icon.arrow-left class="size-5" />
                    </a>

                    <div class="flex items-center gap-3">
                        @if($company && $company->logo_url)
                            <div class="relative w-10 h-10">
                                {{-- Fallback selalu ada di belakang --}}
                                <div class="absolute inset-0 rounded-full bg-blue-600 flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ substr($stockCode, 0, 2) }}</span>
                                </div>
                                {{-- Image di depan, akan hilang jika error --}}
                                <img
                                    src="{{ $company->logo_url }}"
                                    alt="{{ $stockCode }}"
                                    class="absolute inset-0 w-10 h-10 rounded-full object-contain bg-gray-100 dark:bg-zinc-800 p-1"
                                    onerror="this.style.display='none'"
                                />
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ substr($stockCode, 0, 2) }}</span>
                            </div>
                        @endif

                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $stockCode }}</h1>
                                <span class="text-xs px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-400 font-medium">
                                    {{ $company->papan_pencatatan ?? 'IDX' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-zinc-400">{{ $company?->nama_emiten ?? 'Unknown Company' }}</p>
                        </div>
                    </div>
                </div>

                @if($latestTrading)
                    <div class="flex items-center gap-6">
                        <!-- Current Price -->
                        <div class="text-right">
                            <div class="text-2xl font-bold {{ $latestTrading->change >= 0 ? 'text-teal-600 dark:text-teal-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ number_format($latestTrading->close, 0, ',', '.') }}
                            </div>
                            <div class="flex items-center justify-end gap-2 text-sm {{ $latestTrading->change >= 0 ? 'text-teal-600 dark:text-teal-400' : 'text-red-600 dark:text-red-400' }}">
                                @if($latestTrading->change >= 0)
                                    <span>+{{ number_format($latestTrading->change, 0, ',', '.') }}</span>
                                    <span>(+{{ number_format($this->changePercent, 2) }}%)</span>
                                @else
                                    <span>{{ number_format($latestTrading->change, 0, ',', '.') }}</span>
                                    <span>({{ number_format($this->changePercent, 2) }}%)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($latestTrading)
        <!-- Quick Stats Bar -->
        <div class="bg-gray-50 dark:bg-zinc-800/50 border-s border-r border-gray-200 dark:border-zinc-700">
            <div class="px-4 py-2 flex items-center gap-6 overflow-x-auto text-xs">
                <div class="flex items-center gap-2 whitespace-nowrap">
                    <span class="text-gray-500 dark:text-zinc-500">O</span>
                    <span class="text-gray-900 dark:text-white font-medium">{{ number_format($latestTrading->open_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-2 whitespace-nowrap">
                    <span class="text-gray-500 dark:text-zinc-500">H</span>
                    <span class="text-teal-600 dark:text-teal-400 font-medium">{{ number_format($latestTrading->high, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-2 whitespace-nowrap">
                    <span class="text-gray-500 dark:text-zinc-500">L</span>
                    <span class="text-red-600 dark:text-red-400 font-medium">{{ number_format($latestTrading->low, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-2 whitespace-nowrap">
                    <span class="text-gray-500 dark:text-zinc-500">C</span>
                    <span class="text-gray-900 dark:text-white font-medium">{{ number_format($latestTrading->close, 0, ',', '.') }}</span>
                </div>
                <div class="w-px h-4 bg-gray-300 dark:bg-zinc-600"></div>
                <div class="flex items-center gap-2 whitespace-nowrap">
                    <span class="text-gray-500 dark:text-zinc-500">Vol</span>
                    <span class="text-gray-900 dark:text-white font-medium">
                        @if($latestTrading->volume >= 1000000000)
                            {{ number_format($latestTrading->volume / 1000000000, 2) }}B
                        @elseif($latestTrading->volume >= 1000000)
                            {{ number_format($latestTrading->volume / 1000000, 2) }}M
                        @else
                            {{ number_format($latestTrading->volume, 0, ',', '.') }}
                        @endif
                    </span>
                </div>
                <div class="flex items-center gap-2 whitespace-nowrap">
                    <span class="text-gray-500 dark:text-zinc-500">Val</span>
                    <span class="text-gray-900 dark:text-white font-medium">
                        @if($latestTrading->value >= 1000000000)
                            {{ number_format($latestTrading->value / 1000000000, 2) }}B
                        @elseif($latestTrading->value >= 1000000)
                            {{ number_format($latestTrading->value / 1000000, 2) }}M
                        @else
                            {{ number_format($latestTrading->value, 0, ',', '.') }}
                        @endif
                    </span>
                </div>
                <div class="flex items-center gap-2 whitespace-nowrap">
                    <span class="text-gray-500 dark:text-zinc-500">Freq</span>
                    <span class="text-gray-900 dark:text-white font-medium">{{ number_format($latestTrading->frequency, 0, ',', '.') }}</span>
                </div>
                <div class="w-px h-4 bg-gray-300 dark:bg-zinc-600"></div>
                <div class="flex items-center gap-2 whitespace-nowrap">
                    <span class="text-gray-500 dark:text-zinc-500">{{ $latestTrading->date->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="border flex flex-col lg:flex-row dark:border-zinc-700">
            <!-- Chart Area (Main) -->
            <div class="flex-1 lg:w-80 space-y-1">
                <!-- Chart Container -->
                <div class="bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <!-- Chart Header -->
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-zinc-700 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="text-gray-900 dark:text-white font-medium">Price Chart</span>
                            <div class="flex items-center gap-1 text-xs">
                                @foreach(['7' => '1W', '14' => '2W', '30' => '1M', '60' => '2M', '90' => '3M', 'ytd' => 'YTD', '365' => '1Y', '1095' => '3Y', '1825' => '5Y'] as $value => $label)
                                    <button
                                        wire:click="$set('period', '{{ $value }}')"
                                        class="px-3 py-1 rounded {{ !$isCustomRange && $period == $value ? 'bg-teal-400 text-white' : 'text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-900 dark:hover:text-white' }} transition-colors">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-zinc-400">
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 bg-teal-500 rounded-sm"></span> Bullish
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 bg-red-500 rounded-sm"></span> Bearish
                            </span>
                        </div>
                    </div>
                    <!-- TradingView Chart -->
                    <div
                        wire:ignore
                        class="relative"
                        style="height: 450px;"
                    >
                        {{-- Legend at top --}}
                        <div id="chart-legend" class="absolute top-2 left-2 z-50 text-xs font-mono" style="display: none;">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 px-3 py-2 rounded-md bg-white/90 dark:bg-zinc-900/90 border border-gray-200 dark:border-zinc-700 shadow-sm backdrop-blur-sm">
                                <span id="legend-date" class="font-semibold text-gray-900 dark:text-white"></span>
                                <span class="text-gray-400 dark:text-zinc-500">O:</span><span id="legend-open" class="text-gray-700 dark:text-zinc-300"></span>
                                <span class="text-gray-400 dark:text-zinc-500">H:</span><span id="legend-high" class="text-teal-600 dark:text-teal-400"></span>
                                <span class="text-gray-400 dark:text-zinc-500">L:</span><span id="legend-low" class="text-red-600 dark:text-red-400"></span>
                                <span class="text-gray-400 dark:text-zinc-500">C:</span><span id="legend-close" class="font-semibold text-gray-900 dark:text-white"></span>
                                <span id="legend-change"></span>
                                <span class="text-gray-400 dark:text-zinc-500">Vol:</span><span id="legend-volume" class="text-gray-700 dark:text-zinc-300"></span>
                            </div>
                        </div>
                        <div id="chart-container" class="w-full h-full"></div>
                    </div>
                </div>

                <!-- Trading History -->
                <div class="bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-zinc-700 flex items-center justify-between">
                        <span class="text-gray-900 dark:text-white font-medium text-sm">Trading History</span>
                        @if(!$isCustomRange)
                            <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $this->periodLabel }}</span>
                        @endif
                    </div>
                    <div class="overflow-x-auto overflow-y-auto max-h-96">
                        <table class="w-full text-xs">
                            <thead class="sticky top-0 bg-gray-50 dark:bg-zinc-800">
                                <tr class="text-gray-500 dark:text-zinc-400 border-b border-gray-200 dark:border-zinc-700">
                                    <th class="px-4 py-2 text-left font-medium">Date</th>
                                    <th class="px-4 py-2 text-right font-medium">Open</th>
                                    <th class="px-4 py-2 text-right font-medium">High</th>
                                    <th class="px-4 py-2 text-right font-medium">Low</th>
                                    <th class="px-4 py-2 text-right font-medium">Close</th>
                                    <th class="px-4 py-2 text-right font-medium">Chg</th>
                                    <th class="px-4 py-2 text-right font-medium">Vol</th>
                                    <th class="px-4 py-2 text-right font-medium">Val</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tradingHistory as $trading)
                                    <tr class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <td class="px-4 py-2 text-gray-900 dark:text-zinc-200">{{ $trading->date->format('d M') }}</td>
                                        <td class="px-4 py-2 text-right text-gray-600 dark:text-zinc-400">{{ number_format($trading->open_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-right text-teal-600 dark:text-teal-400">{{ number_format($trading->high, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-right text-red-600 dark:text-red-400">{{ number_format($trading->low, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-right text-gray-900 dark:text-white font-medium">{{ number_format($trading->close, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-right font-medium {{ $trading->change >= 0 ? 'text-teal-600 dark:text-teal-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $trading->change >= 0 ? '+' : '' }}{{ number_format($trading->change, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-2 text-right text-gray-600 dark:text-zinc-400">
                                            @if($trading->volume >= 1000000)
                                                {{ number_format($trading->volume / 1000000, 1) }}M
                                            @else
                                                {{ number_format($trading->volume / 1000, 1) }}K
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right text-gray-600 dark:text-zinc-400">
                                            @if($trading->value >= 1000000000)
                                                {{ number_format($trading->value / 1000000000, 1) }}B
                                            @elseif($trading->value >= 1000000)
                                                {{ number_format($trading->value / 1000000, 1) }}M
                                            @else
                                                {{ number_format($trading->value / 1000, 1) }}K
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="lg:w-80 lg:border-l border-gray-200 dark:border-zinc-700 space-y-1">
                <!-- Order Book -->
                <div class="bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <div class="px-4 py-2 border-gray-200 dark:border-zinc-700">
                        <span class="text-gray-900 dark:text-white font-medium text-sm">Order Book</span>
                    </div>
                    <div class="p-4 space-y-3">
                        <!-- Bid -->
                        <div class="relative">
                            <div class="absolute inset-0 bg-teal-500/10 rounded" style="width: {{ min(($latestTrading->bid_volume / max($latestTrading->bid_volume + $latestTrading->offer_volume, 1)) * 100, 100) }}%"></div>
                            <div class="relative flex items-center justify-between py-2 px-3">
                                <div>
                                    <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase">Bid</div>
                                    <div class="text-lg font-bold text-teal-600 dark:text-teal-400">{{ number_format($latestTrading->bid, 0, ',', '.') }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] text-gray-500 dark:text-zinc-500">Volume</div>
                                    <div class="text-sm text-gray-700 dark:text-zinc-300">{{ number_format($latestTrading->bid_volume, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Spread -->
                        <div class="text-center py-1">
                            <span class="text-[10px] text-gray-500 dark:text-zinc-500">Spread</span>
                            <div class="text-sm font-medium text-gray-700 dark:text-zinc-300">{{ number_format($latestTrading->offer - $latestTrading->bid, 0, ',', '.') }}</div>
                        </div>

                        <!-- Offer -->
                        <div class="relative">
                            <div class="absolute inset-0 bg-red-500/10 rounded" style="width: {{ min(($latestTrading->offer_volume / max($latestTrading->bid_volume + $latestTrading->offer_volume, 1)) * 100, 100) }}%"></div>
                            <div class="relative flex items-center justify-between py-2 px-3">
                                <div>
                                    <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase">Offer</div>
                                    <div class="text-lg font-bold text-red-600 dark:text-red-400">{{ number_format($latestTrading->offer, 0, ',', '.') }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] text-gray-500 dark:text-zinc-500">Volume</div>
                                    <div class="text-sm text-gray-700 dark:text-zinc-300">{{ number_format($latestTrading->offer_volume, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Statistics -->
                <div class="bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <div class="px-4 py-2 border-gray-200 dark:border-zinc-700">
                        <span class="text-gray-900 dark:text-white font-medium text-sm">Key Statistics</span>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-zinc-500">Previous Close</span>
                            <span class="text-xs text-gray-700 dark:text-zinc-300 font-medium">{{ number_format($latestTrading->previous, 0, ',', '.') }}</span>
                        </div>
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-zinc-500">{{ $isCustomRange ? '' : $this->periodLabel . ' ' }}High</span>
                            <span class="text-xs text-teal-600 dark:text-teal-400 font-medium">{{ number_format($this->highestPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-zinc-500">{{ $isCustomRange ? '' : $this->periodLabel . ' ' }}Low</span>
                            <span class="text-xs text-red-600 dark:text-red-400 font-medium">{{ number_format($this->lowestPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-zinc-500">Avg Volume</span>
                            <span class="text-xs text-gray-700 dark:text-zinc-300 font-medium">
                                @if($this->avgVolume >= 1000000)
                                    {{ number_format($this->avgVolume / 1000000, 2) }}M
                                @else
                                    {{ number_format($this->avgVolume / 1000, 2) }}K
                                @endif
                            </span>
                        </div>
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-zinc-500">Listed Shares</span>
                            <span class="text-xs text-gray-700 dark:text-zinc-300 font-medium">
                                @if($latestTrading->listed_shares >= 1000000000)
                                    {{ number_format($latestTrading->listed_shares / 1000000000, 2) }}B
                                @else
                                    {{ number_format($latestTrading->listed_shares / 1000000, 2) }}M
                                @endif
                            </span>
                        </div>
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-zinc-500">Index Individual</span>
                            <span class="text-xs text-gray-700 dark:text-zinc-300 font-medium">{{ number_format($latestTrading->index_individual, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Foreign Flow -->
                <div class="bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <div class="px-4 py-2 border-gray-200 dark:border-zinc-700">
                        <span class="text-gray-900 dark:text-white font-medium text-sm">Foreign Flow</span>
                    </div>
                    <div class="p-4">
                        <div class="text-center mb-4">
                            <div class="text-2xl font-bold {{ $this->netForeign >= 0 ? 'text-teal-600 dark:text-teal-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $this->netForeign >= 0 ? '+' : '' }}{{ number_format($this->netForeign, 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase">Net Foreign</div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-teal-50 dark:bg-teal-500/10 rounded-lg">
                                <div class="text-sm font-bold text-teal-600 dark:text-teal-400">{{ number_format($latestTrading->foreign_buy, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-gray-500 dark:text-zinc-500">Buy</div>
                            </div>
                            <div class="text-center p-3 bg-red-50 dark:bg-red-500/10 rounded-lg">
                                <div class="text-sm font-bold text-red-600 dark:text-red-400">{{ number_format($latestTrading->foreign_sell, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-gray-500 dark:text-zinc-500">Sell</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($company)
                    <!-- Company Info -->
                    <div class="bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 overflow-hidden">
                        <div class="px-4 py-2 border-gray-200 dark:border-zinc-700">
                            <span class="text-gray-900 dark:text-white font-medium text-sm">Company Info</span>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                            <div class="px-4 py-2">
                                <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-1">Sector</div>
                                <div class="text-xs text-gray-700 dark:text-zinc-300">{{ $company->sektor ?? '-' }}</div>
                            </div>
                            <div class="px-4 py-2">
                                <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-1">Industry</div>
                                <div class="text-xs text-gray-700 dark:text-zinc-300">{{ $company->industri ?? '-' }}</div>
                            </div>
                            <div class="px-4 py-2">
                                <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-1">Listing Date</div>
                                <div class="text-xs text-gray-700 dark:text-zinc-300">{{ $company->tanggal_pencatatan?->format('d M Y') ?? '-' }}</div>
                            </div>
                            @if($company->website_url)
                                <div class="px-4 py-2">
                                    <a href="{{ $company->website_url }}" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                        <flux:icon.globe-alt class="size-3" />
                                        {{ $company->website }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- No Data State -->
        <div class="flex items-center justify-center min-h-[60vh]">
            <div class="text-center">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center mx-auto mb-4">
                    <flux:icon.chart-bar class="size-10 text-gray-400 dark:text-zinc-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Trading Data</h3>
                <p class="text-gray-500 dark:text-zinc-400 mb-6">
                    No data available for <span class="text-gray-900 dark:text-white font-medium">{{ $stockCode }}</span>
                </p>
                <a href="{{ route('stock-summary.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <flux:icon.arrow-left class="size-4" />
                    Back to List
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://unpkg.com/lightweight-charts@5.1.0/dist/lightweight-charts.standalone.production.js"></script>
<script>
(function() {
    // Use window object to persist state across wire:navigate
    if (!window.StockChart) {
        window.StockChart = {
            chart: null,
            candlestickSeries: null,
            volumeSeries: null,
            candlestickData: null,
            volumeData: null,
            resizeObserver: null,
            themeObserver: null,
            retryCount: 0,
            maxRetries: 15
        };
    }

    const SC = window.StockChart;

    function isOnStockDetailPage() {
        return document.querySelector('[data-stock-detail]') !== null;
    }

    function getStockDetailComponent() {
        const el = document.querySelector('[data-stock-detail][wire\\:id]');
        if (el) {
            const componentId = el.getAttribute('wire:id');
            if (componentId && typeof Livewire !== 'undefined') {
                return Livewire.find(componentId);
            }
        }
        return null;
    }

    function getChartData() {
        const component = getStockDetailComponent();
        return component ? component.chartData : null;
    }

    function cleanupChart() {
        if (SC.chart) {
            try { SC.chart.remove(); } catch(e) {}
            SC.chart = null;
        }
        if (SC.resizeObserver) {
            SC.resizeObserver.disconnect();
            SC.resizeObserver = null;
        }
    }

    function initChart(chartData, isRetry = false) {
        // Check if we're on the stock detail page
        if (!isOnStockDetailPage()) {
            console.log('Not on stock detail page, skipping chart init');
            return;
        }

        const container = document.getElementById('chart-container');
        if (!container) {
            if (SC.retryCount < SC.maxRetries) {
                SC.retryCount++;
                console.log('Chart container not found, retrying...', SC.retryCount);
                setTimeout(() => initChart(chartData, true), 100);
            }
            return;
        }

        // Use provided data, or stored data (for theme changes), or fetch from component
        let data = chartData;
        if (!data && SC.candlestickData && SC.candlestickData.length > 0) {
            // Use stored data for theme change reinit
            data = {
                candlestick: SC.candlestickData,
                volume: SC.volumeData || []
            };
        }
        if (!data) {
            data = getChartData();
        }
        if (!data || !data.candlestick || data.candlestick.length === 0) {
            // console.log('No chart data available');
            return;
        }

        if (typeof LightweightCharts === 'undefined') {
            if (SC.retryCount < SC.maxRetries) {
                SC.retryCount++;
                console.log('LightweightCharts not loaded, retrying...', SC.retryCount);
                setTimeout(() => initChart(chartData, true), 100);
            }
            return;
        }

        // Reset retry count on success
        SC.retryCount = 0;

        // Check dark mode - use class on <html> element (most reliable)
        const isDark = document.documentElement.classList.contains('dark');

        // Clean up existing chart
        cleanupChart();

        // Create chart - v5.1 API
        SC.chart = LightweightCharts.createChart(container, {
            width: container.clientWidth,
            height: container.clientHeight,
            layout: {
                background: { type: 'solid', color: isDark ? '#18181b' : '#ffffff' },
                textColor: isDark ? '#a1a1aa' : '#71717a',
            },
            grid: {
                vertLines: { color: isDark ? '#27272a' : '#f4f4f5' },
                horzLines: { color: isDark ? '#27272a' : '#f4f4f5' },
            },
            crosshair: {
                mode: LightweightCharts.CrosshairMode.Normal,
                vertLine: {
                    color: isDark ? '#6b7280' : '#9ca3af',
                    width: 1,
                    style: LightweightCharts.LineStyle.Dashed,
                },
                horzLine: {
                    color: isDark ? '#6b7280' : '#9ca3af',
                    width: 1,
                    style: LightweightCharts.LineStyle.Dashed,
                },
            },
            rightPriceScale: {
                borderColor: isDark ? '#27272a' : '#e4e4e7',
                scaleMargins: { top: 0.1, bottom: 0.2 },
            },
            timeScale: {
                borderColor: isDark ? '#27272a' : '#e4e4e7',
                timeVisible: true,
                secondsVisible: false,
            },
            // Disable zoom via mouse scroll
            // handleScroll.mouseWheel: false - Disable scroll dengan mouse wheel
            // handleScale.mouseWheel: false - Disable zoom dengan mouse wheel
            // handleScale.pinch: false - Disable pinch zoom (touch)
            // handleScale.axisPressedMouseMove: false - Disable zoom via drag di axis
            // handleScroll: {
            //     mouseWheel: false,
            //     pressedMouseMove: true,
            //     horzTouchDrag: true,
            //     vertTouchDrag: false,
            // },
            // handleScale: {
            //     mouseWheel: false,
            //     pinch: false,
            //     axisPressedMouseMove: false,
            //     axisDoubleClickReset: false,
            // },
        });

        // Candlestick Series - v5.1 API
        SC.candlestickSeries = SC.chart.addSeries(LightweightCharts.CandlestickSeries, {
            upColor: '#26a69a',
            downColor: '#ef5350',
            borderUpColor: '#26a69a',
            borderDownColor: '#ef5350',
            wickUpColor: '#26a69a',
            wickDownColor: '#ef5350',
        });

        if (data.candlestick && data.candlestick.length > 0) {
            SC.candlestickSeries.setData(data.candlestick);
            SC.candlestickData = data.candlestick; // Store for legend change calculation
        }

        // Volume Series - v5.1 API
        SC.volumeSeries = SC.chart.addSeries(LightweightCharts.HistogramSeries, {
            priceFormat: { type: 'volume' },
            priceScaleId: 'volume',
        });

        SC.chart.priceScale('volume').applyOptions({
            scaleMargins: { top: 0.85, bottom: 0 },
        });

        if (data.volume && data.volume.length > 0) {
            SC.volumeSeries.setData(data.volume);
            SC.volumeData = data.volume; // Store for theme change reinit
        }

        SC.chart.timeScale().fitContent();

        // Handle resize
        if (SC.resizeObserver) SC.resizeObserver.disconnect();
        SC.resizeObserver = new ResizeObserver(entries => {
            if (entries.length === 0 || entries[0].target !== container) return;
            const { width, height } = entries[0].contentRect;
            if (SC.chart && width > 0 && height > 0) {
                SC.chart.applyOptions({ width, height });
            }
        });
        SC.resizeObserver.observe(container);

        // Setup legend
        setupLegend();

        // Setup infinite scroll (load more data on pan)
        setupInfiniteScroll(data);

        console.log('Chart initialized');
    }

    function setupInfiniteScroll(initialData) {
        if (!SC.chart || !initialData.candlestick || initialData.candlestick.length === 0) return;

        // Track loaded data range
        SC.loadedDataRange = {
            from: initialData.candlestick[0].time,
            to: initialData.candlestick[initialData.candlestick.length - 1].time,
        };
        SC.isLoadingMore = false;
        SC.noMoreData = false;
        SC.initialLoadComplete = false;

        // Delay enabling infinite scroll to prevent triggering on initial fitContent
        setTimeout(() => {
            SC.initialLoadComplete = true;
        }, 500);

        // Subscribe to visible time range changes
        SC.chart.timeScale().subscribeVisibleLogicalRangeChange((logicalRange) => {
            // Skip if initial load not complete, loading, or no more data
            if (!SC.initialLoadComplete || !logicalRange || SC.isLoadingMore || SC.noMoreData) return;

            // Check if user panned to the left edge (older data)
            if (logicalRange.from < 5) {
                loadMoreHistoricalData();
            }
        });
    }

    function loadMoreHistoricalData() {
        if (SC.isLoadingMore || SC.noMoreData || !SC.loadedDataRange) return;

        SC.isLoadingMore = true;
        const beforeDate = SC.loadedDataRange.from;

        console.log('Loading more data before:', beforeDate);

        const component = getStockDetailComponent();
        if (component) {
            // Mark as custom range (removes period highlight)
            component.call('setCustomRange');
            component.call('loadMoreData', beforeDate, 30);
        }
    }

    function setupLegend() {
        const legend = document.getElementById('chart-legend');
        const legendDate = document.getElementById('legend-date');
        const legendOpen = document.getElementById('legend-open');
        const legendHigh = document.getElementById('legend-high');
        const legendLow = document.getElementById('legend-low');
        const legendClose = document.getElementById('legend-close');
        const legendChange = document.getElementById('legend-change');
        const legendVolume = document.getElementById('legend-volume');

        if (!legend || !SC.chart) {
            console.warn('Legend or chart not found');
            return;
        }

        SC.chart.subscribeCrosshairMove(param => {
            if (param.point === undefined || !param.time || param.point.x < 0 || param.point.y < 0) {
                legend.style.display = 'none';
                return;
            }

            const candleData = param.seriesData.get(SC.candlestickSeries);
            if (!candleData) {
                legend.style.display = 'none';
                return;
            }

            const volumeData = param.seriesData.get(SC.volumeSeries);

            const open = candleData.open || 0;
            const high = candleData.high || 0;
            const low = candleData.low || 0;
            const close = candleData.close || 0;

            // Get previous candle's close price for change calculation
            let previous = open; // Fallback to open if no previous data
            if (SC.candlestickData && SC.candlestickData.length > 0) {
                const currentTime = param.time;
                const currentIndex = SC.candlestickData.findIndex(c => c.time === currentTime);
                if (currentIndex > 0) {
                    previous = SC.candlestickData[currentIndex - 1].close || open;
                }
            }

            const change = close - previous;
            const changePercent = previous !== 0 ? ((change / previous) * 100).toFixed(2) : '0.00';
            const volValue = volumeData ? formatVolume(volumeData.value) : '-';

            legend.style.display = 'block';
            legendDate.textContent = param.time;
            legendOpen.textContent = open.toLocaleString('id-ID');
            legendHigh.textContent = high.toLocaleString('id-ID');
            legendLow.textContent = low.toLocaleString('id-ID');
            legendClose.textContent = close.toLocaleString('id-ID');
            legendVolume.textContent = volValue;

            const changeText = (change >= 0 ? '+' : '') + change.toLocaleString('id-ID') + ' (' + changePercent + '%)';
            legendChange.textContent = changeText;
            legendChange.className = change >= 0
                ? 'text-teal-600 dark:text-teal-400 font-medium'
                : 'text-red-600 dark:text-red-400 font-medium';
        });

        console.log('Legend setup complete');
    }

    function formatVolume(value) {
        if (value >= 1e9) return (value / 1e9).toFixed(2) + 'B';
        if (value >= 1e6) return (value / 1e6).toFixed(2) + 'M';
        if (value >= 1e3) return (value / 1e3).toFixed(2) + 'K';
        return value.toLocaleString('id-ID');
    }

    // Set period to 1M (30 days) via Livewire
    function setPeriodTo1M() {
        const component = getStockDetailComponent();
        if (component) {
            component.set('period', '30');
            return true;
        }
        return false;
    }

    // Initialize chart with retry
    function startInit() {
        if (!isOnStockDetailPage()) return;

        SC.retryCount = 0;
        cleanupChart();

        // Try to set period first, then init chart
        setTimeout(() => {
            setPeriodTo1M();
            setTimeout(initChart, 150);
        }, 50);
    }

    // Initial load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startInit);
    } else {
        startInit();
    }

    // Listen for Livewire events (only once)
    if (!window.stockChartEventsRegistered) {
        window.stockChartEventsRegistered = true;

        // document.addEventListener('livewire:initialized', () => {
        //     // Period change updates chart
        //     Livewire.on('chart-data-updated', (data) => {
        //         console.log('Chart data updated:', data.period);
        //         SC.retryCount = 0;
        //         SC.noMoreData = false;
        //         initChart(data.chartData);
        //     });

        //     // More data loaded (infinite scroll)
        //     Livewire.on('more-data-loaded', (eventData) => {
        //         const data = eventData[0] || eventData;
        //         console.log('More data loaded:', data.candlestick?.length, 'candles');

        //         if (SC.candlestickSeries && data.candlestick && data.candlestick.length > 0) {
        //             // Get current data
        //             const currentData = SC.candlestickSeries.data() || [];

        //             // Prepend new data (older data comes before)
        //             const mergedCandlestick = [...data.candlestick, ...currentData];
        //             SC.candlestickSeries.setData(mergedCandlestick);

        //             // Update loaded data range
        //             if (SC.loadedDataRange && data.candlestick.length > 0) {
        //                 SC.loadedDataRange.from = data.candlestick[0].time;
        //             }
        //         }

        //         if (SC.volumeSeries && data.volume && data.volume.length > 0) {
        //             const currentVolume = SC.volumeSeries.data() || [];
        //             const mergedVolume = [...data.volume, ...currentVolume];
        //             SC.volumeSeries.setData(mergedVolume);
        //         }

        //         SC.isLoadingMore = false;
        //     });

        //     // No more data available
        //     Livewire.on('no-more-data', () => {
        //         console.log('No more historical data available');
        //         SC.noMoreData = true;
        //         SC.isLoadingMore = false;
        //     });
        // });

        // Re-init on Livewire navigation
        document.addEventListener('livewire:navigated', () => {
            // console.log('Livewire navigated, checking if on stock detail page...');
            if (isOnStockDetailPage()) {
                console.log('On stock detail page, initializing chart...');
                // startInit();
                // Period change updates chart
                Livewire.on('chart-data-updated', (data) => {
                    console.log('Chart data updated:', data.period);
                    SC.retryCount = 0;
                    SC.noMoreData = false;
                    SC.initialLoadComplete = false; // Reset to prevent immediate load on period change
                    initChart(data.chartData);
                });

                // More data loaded (infinite scroll)
                Livewire.on('more-data-loaded', (eventData) => {
                    const data = eventData[0] || eventData;
                    console.log('More data loaded:', data.candlestick?.length, 'candles');

                    if (SC.candlestickSeries && data.candlestick && data.candlestick.length > 0) {
                        // Get current data
                        const currentData = SC.candlestickSeries.data() || [];

                        // Prepend new data (older data comes before)
                        const mergedCandlestick = [...data.candlestick, ...currentData];
                        SC.candlestickSeries.setData(mergedCandlestick);
                        SC.candlestickData = mergedCandlestick; // Update stored data for legend

                        // Update loaded data range
                        if (SC.loadedDataRange && data.candlestick.length > 0) {
                            SC.loadedDataRange.from = data.candlestick[0].time;
                        }
                    }

                    if (SC.volumeSeries && data.volume && data.volume.length > 0) {
                        const currentVolume = SC.volumeSeries.data() || [];
                        const mergedVolume = [...data.volume, ...currentVolume];
                        SC.volumeSeries.setData(mergedVolume);
                        SC.volumeData = mergedVolume; // Update stored data for theme change
                    }

                    SC.isLoadingMore = false;
                });

                // No more data available
                Livewire.on('no-more-data', () => {
                    console.log('No more historical data available');
                    SC.noMoreData = true;
                    SC.isLoadingMore = false;
                });
            }
        });

        // Theme change handler - watch for class changes on <html> element
        let lastDarkState = document.documentElement.classList.contains('dark');

        SC.themeObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class' && isOnStockDetailPage()) {
                    const currentDarkState = document.documentElement.classList.contains('dark');
                    // Only reinit if dark state actually changed
                    if (currentDarkState !== lastDarkState) {
                        lastDarkState = currentDarkState;
                        console.log('Theme changed to:', currentDarkState ? 'dark' : 'light');
                        SC.retryCount = 0;
                        // Use longer delay to ensure Flux has updated
                        setTimeout(() => {
                            if (isOnStockDetailPage()) {
                                initChart();
                            }
                        }, 100);
                    }
                }
            });
        });
        SC.themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    }
})();
</script>
@endpush

