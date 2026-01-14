<div class="min-h-screen" data-dashboard>
    @if($latestTrading)
        <!-- Main Content -->
        <div class="flex flex-col lg:flex-row dark:border-zinc-700">
            <div class="lg:w-80 lg:border-r border-gray-200 dark:border-zinc-700 space-y-1 order-2 md:order-1">
                <!-- Technical Indicators -->
                <div class="bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <div class="px-4 py-2 border-gray-200 dark:border-zinc-700">
                        <span class="text-gray-900 dark:text-white font-medium text-sm">Technical Indicators</span>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                        <!-- Moving Averages -->
                        <div class="px-4 py-3">
                            <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-2">Moving Averages</div>
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">SMA 20</span>
                                    <span class="text-xs font-medium {{ $technicalIndicators['sma_20'] ? 'text-gray-700 dark:text-zinc-300' : 'text-gray-400 dark:text-zinc-600' }}">
                                        {{ $technicalIndicators['sma_20'] ? number_format($technicalIndicators['sma_20'], 0, ',', '.') : '-' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">SMA 50</span>
                                    <span class="text-xs font-medium {{ $technicalIndicators['sma_50'] ? 'text-gray-700 dark:text-zinc-300' : 'text-gray-400 dark:text-zinc-600' }}">
                                        {{ $technicalIndicators['sma_50'] ? number_format($technicalIndicators['sma_50'], 0, ',', '.') : '-' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">EMA 20</span>
                                    <span class="text-xs font-medium {{ $technicalIndicators['ema_20'] ? 'text-gray-700 dark:text-zinc-300' : 'text-gray-400 dark:text-zinc-600' }}">
                                        {{ $technicalIndicators['ema_20'] ? number_format($technicalIndicators['ema_20'], 0, ',', '.') : '-' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">EMA 50</span>
                                    <span class="text-xs font-medium {{ $technicalIndicators['ema_50'] ? 'text-gray-700 dark:text-zinc-300' : 'text-gray-400 dark:text-zinc-600' }}">
                                        {{ $technicalIndicators['ema_50'] ? number_format($technicalIndicators['ema_50'], 0, ',', '.') : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- RSI -->
                        <div class="px-4 py-3">
                            <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-2">RSI (14)</div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-zinc-500">Value</span>
                                <span class="text-xs font-medium {{ $technicalIndicators['rsi'] ? ($technicalIndicators['rsi'] > 70 ? 'text-red-600 dark:text-red-400' : ($technicalIndicators['rsi'] < 30 ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-zinc-300')) : 'text-gray-400 dark:text-zinc-600' }}">
                                    {{ $technicalIndicators['rsi'] ? number_format($technicalIndicators['rsi'], 2) : '-' }}
                                    @if($technicalIndicators['rsi'])
                                        @if($technicalIndicators['rsi'] > 70)
                                            <span class="text-[10px] text-red-600 dark:text-red-400 ml-1">(Overbought)</span>
                                        @elseif($technicalIndicators['rsi'] < 30)
                                            <span class="text-[10px] text-green-600 dark:text-green-400 ml-1">(Oversold)</span>
                                        @endif
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- MACD -->
                        <div class="px-4 py-3">
                            <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-2">MACD (12,26,9)</div>
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">MACD</span>
                                    <span class="text-xs font-medium {{ $technicalIndicators['macd']['macd'] ? ($technicalIndicators['macd']['macd'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400') : 'text-gray-400 dark:text-zinc-600' }}">
                                        {{ $technicalIndicators['macd']['macd'] ? number_format($technicalIndicators['macd']['macd'], 2) : '-' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">Signal</span>
                                    <span class="text-xs font-medium {{ $technicalIndicators['macd']['signal'] ? 'text-gray-700 dark:text-zinc-300' : 'text-gray-400 dark:text-zinc-600' }}">
                                        {{ $technicalIndicators['macd']['signal'] ? number_format($technicalIndicators['macd']['signal'], 2) : '-' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">Histogram</span>
                                    <span class="text-xs font-medium {{ $technicalIndicators['macd']['histogram'] ? ($technicalIndicators['macd']['histogram'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400') : 'text-gray-400 dark:text-zinc-600' }}">
                                        {{ $technicalIndicators['macd']['histogram'] ? number_format($technicalIndicators['macd']['histogram'], 2) : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Bollinger Bands -->
                        <div class="px-4 py-3">
                            <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-2">Bollinger Bands (20,2)</div>
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">Upper</span>
                                    <span class="text-xs font-medium {{ $technicalIndicators['bollinger_bands']['upper'] ? 'text-red-600 dark:text-red-400' : 'text-gray-400 dark:text-zinc-600' }}">
                                        {{ $technicalIndicators['bollinger_bands']['upper'] ? number_format($technicalIndicators['bollinger_bands']['upper'], 0, ',', '.') : '-' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">Middle (SMA)</span>
                                    <span class="text-xs font-medium {{ $technicalIndicators['bollinger_bands']['middle'] ? 'text-gray-700 dark:text-zinc-300' : 'text-gray-400 dark:text-zinc-600' }}">
                                        {{ $technicalIndicators['bollinger_bands']['middle'] ? number_format($technicalIndicators['bollinger_bands']['middle'], 0, ',', '.') : '-' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">Lower</span>
                                    <span class="text-xs font-medium {{ $technicalIndicators['bollinger_bands']['lower'] ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-zinc-600' }}">
                                        {{ $technicalIndicators['bollinger_bands']['lower'] ? number_format($technicalIndicators['bollinger_bands']['lower'], 0, ',', '.') : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Stochastic Oscillator -->
                        <div class="px-4 py-3">
                            <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-2">Stochastic (14)</div>
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">%K</span>
                                    <span class="text-xs font-medium {{ $technicalIndicators['stochastic']['k'] ? ($technicalIndicators['stochastic']['k'] > 80 ? 'text-red-600 dark:text-red-400' : ($technicalIndicators['stochastic']['k'] < 20 ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-zinc-300')) : 'text-gray-400 dark:text-zinc-600' }}">
                                        {{ $technicalIndicators['stochastic']['k'] ? number_format($technicalIndicators['stochastic']['k'], 2) : '-' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">%D</span>
                                    <span class="text-xs font-medium {{ $technicalIndicators['stochastic']['d'] ? ($technicalIndicators['stochastic']['d'] > 80 ? 'text-red-600 dark:text-red-400' : ($technicalIndicators['stochastic']['d'] < 20 ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-zinc-300')) : 'text-gray-400 dark:text-zinc-600' }}">
                                        {{ $technicalIndicators['stochastic']['d'] ? number_format($technicalIndicators['stochastic']['d'], 2) : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Volume SMA -->
                        <div class="px-4 py-3">
                            <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-2">Volume</div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-zinc-500">SMA 20</span>
                                <span class="text-xs font-medium {{ $technicalIndicators['volume_sma'] ? 'text-gray-700 dark:text-zinc-300' : 'text-gray-400 dark:text-zinc-600' }}">
                                    @if($technicalIndicators['volume_sma'])
                                        @if($technicalIndicators['volume_sma'] >= 1000000000)
                                            {{ number_format($technicalIndicators['volume_sma'] / 1000000000, 2) }}B
                                        @elseif($technicalIndicators['volume_sma'] >= 1000000)
                                            {{ number_format($technicalIndicators['volume_sma'] / 1000000, 2) }}M
                                        @else
                                            {{ number_format($technicalIndicators['volume_sma'], 0, ',', '.') }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @if($financialRatio)
                    <!-- Financial Ratio -->
                    <div class="bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 overflow-hidden">
                        <div class="px-4 py-2 border-gray-200 dark:border-zinc-700 flex items-center justify-between">
                            <span class="text-gray-900 dark:text-white font-medium text-sm">Financial Ratio</span>
                            <div class="flex items-center gap-1">
                                @if($financialRatio->isSharia())
                                    <span class="px-1.5 py-0.5 bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 text-[10px] font-medium rounded">Sharia</span>
                                @endif
                                <span class="px-1.5 py-0.5 {{ $financialRatio->isAudited() ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400' : 'bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400' }} text-[10px] font-medium rounded">
                                    {{ $financialRatio->audit_label }}
                                </span>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                            <!-- Report Date -->
                            <div class="px-4 py-1 flex items-center justify-between bg-gray-50 dark:bg-zinc-800/50">
                                <span class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase">Report Date</span>
                                <span class="text-xs text-gray-700 dark:text-zinc-300 font-medium">{{ $financialRatio->fs_date?->format('d M Y') ?? '-' }}</span>
                            </div>

                            <!-- Valuation Metrics -->
                            <div class="px-4 py-1 flex items-center justify-between">
                                <flux:tooltip content="Price to Book Value" position="right">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">PBV</span>
                                </flux:tooltip>
                                <span class="text-xs font-medium {{ $financialRatio->price_bv && $financialRatio->price_bv < 1 ? 'text-green-600 dark:text-green-400' : ($financialRatio->price_bv && $financialRatio->price_bv > 3 ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-zinc-300') }}">
                                    {{ $financialRatio->price_bv ? number_format($financialRatio->price_bv, 2, ',', '.') . 'x' : '-' }}
                                </span>
                            </div>
                            <div class="px-4 py-1 flex items-center justify-between">
                                <flux:tooltip content="Price to Earnings Ratio" position="right">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">PER</span>
                                </flux:tooltip>
                                <span class="text-xs font-medium {{ $financialRatio->per && $financialRatio->per < 15 ? 'text-green-600 dark:text-green-400' : ($financialRatio->per && $financialRatio->per > 25 ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-zinc-300') }}">
                                    {{ $financialRatio->per ? number_format($financialRatio->per, 2, ',', '.') . 'x' : '-' }}
                                </span>
                            </div>
                            <div class="px-4 py-1 flex items-center justify-between">
                                <flux:tooltip content="Earnings Per Share" position="right">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">EPS</span>
                                </flux:tooltip>
                                <span class="text-xs text-gray-700 dark:text-zinc-300 font-medium">
                                    {{ $financialRatio->eps ? number_format($financialRatio->eps, 2, ',', '.') : '-' }}
                                </span>
                            </div>
                            <div class="px-4 py-1 flex items-center justify-between">
                                <flux:tooltip content="Book Value" position="right">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">Book Value</span>
                                </flux:tooltip>
                                <span class="text-xs text-gray-700 dark:text-zinc-300 font-medium">
                                    {{ $financialRatio->book_value ? number_format($financialRatio->book_value, 2, ',', '.') : '-' }}
                                </span>
                            </div>

                            <!-- Profitability Metrics -->
                            <div class="px-4 py-1 flex items-center justify-between">
                                <flux:tooltip content="Return on Equity" position="right">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">ROE</span>
                                </flux:tooltip>
                                <span class="text-xs font-medium {{ $financialRatio->roe && $financialRatio->roe > 15 ? 'text-green-600 dark:text-green-400' : ($financialRatio->roe && $financialRatio->roe < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-zinc-300') }}">
                                    {{ $financialRatio->roe ? number_format($financialRatio->roe, 2, ',', '.') . '%' : '-' }}
                                </span>
                            </div>
                            <div class="px-4 py-1 flex items-center justify-between">
                                <flux:tooltip content="Return on Assets" position="right">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">ROA</span>
                                </flux:tooltip>
                                <span class="text-xs font-medium {{ $financialRatio->roa && $financialRatio->roa > 10 ? 'text-green-600 dark:text-green-400' : ($financialRatio->roa && $financialRatio->roa < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-zinc-300') }}">
                                    {{ $financialRatio->roa ? number_format($financialRatio->roa, 2, ',', '.') . '%' : '-' }}
                                </span>
                            </div>
                            <div class="px-4 py-1 flex items-center justify-between">
                                <flux:tooltip content="Net Profit Margin" position="right">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">NPM</span>
                                </flux:tooltip>
                                <span class="text-xs font-medium {{ $financialRatio->npm && $financialRatio->npm > 10 ? 'text-green-600 dark:text-green-400' : ($financialRatio->npm && $financialRatio->npm < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-zinc-300') }}">
                                    {{ $financialRatio->npm ? number_format($financialRatio->npm, 2, ',', '.') . '%' : '-' }}
                                </span>
                            </div>

                            <!-- Leverage -->
                            <div class="px-4 py-1 flex items-center justify-between">
                                <flux:tooltip content="Debt to Equity Ratio" position="right">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">DER</span>
                                </flux:tooltip>
                                <span class="text-xs font-medium {{ $financialRatio->de_ratio && $financialRatio->de_ratio < 1 ? 'text-green-600 dark:text-green-400' : ($financialRatio->de_ratio && $financialRatio->de_ratio > 2 ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-zinc-300') }}">
                                    {{ $financialRatio->de_ratio ? number_format($financialRatio->de_ratio, 2, ',', '.') . 'x' : '-' }}
                                </span>
                            </div>

                            <!-- Financial Position (in Billions) -->
                            <div class="px-4 py-1 flex items-center justify-between">
                                <flux:tooltip content="Assets" position="right">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">Assets</span>
                                </flux:tooltip>
                                <span class="text-xs text-gray-700 dark:text-zinc-300 font-medium">
                                    {{ $financialRatio->assets ? number_format($financialRatio->assets, 2, ',', '.') . ' B' : '-' }}
                                </span>
                            </div>
                            <div class="px-4 py-1 flex items-center justify-between">
                                <flux:tooltip content="Equity" position="right">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">Equity</span>
                                </flux:tooltip>
                                <span class="text-xs text-gray-700 dark:text-zinc-300 font-medium">
                                    {{ $financialRatio->equity ? number_format($financialRatio->equity, 2, ',', '.') . ' B' : '-' }}
                                </span>
                            </div>
                            <div class="px-4 py-1 flex items-center justify-between">
                                <flux:tooltip content="Sales" position="right">
                                    <span class="text-xs text-gray-500 dark:text-zinc-500">Sales</span>
                                </flux:tooltip>
                                <span class="text-xs text-gray-700 dark:text-zinc-300 font-medium">
                                    {{ $financialRatio->sales ? number_format($financialRatio->sales, 2, ',', '.') . ' B' : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Chart Area (Main) -->
            <div class="flex-1 space-y-1 order-1 md:order-2">
                <!-- Chart Container -->
                <div class="bg-white dark:bg-zinc-900 border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <!-- TradingView Style Header -->
                    <div class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-700"
                         wire:poll.120s.keep-alive="refreshStockPrice">
                        <div class="px-4 py-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="relative w-12 h-12 shrink-0"
                                            x-data="{ imageLoaded: false, imageError: false }"
                                            x-init="imageLoaded = false; imageError = false"
                                            wire:key="logo-{{ $company->kode_emiten }}"
                                        >
                                            {{-- Fallback initials - always visible until image loads --}}
                                            <div
                                                class="absolute inset-0 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center"
                                                x-show="!imageLoaded || imageError"
                                                x-cloak
                                            >
                                                <span class="text-gray-600 dark:text-zinc-400 font-bold text-xs">{{ substr($company->kode_emiten, 0, 2) }}</span>
                                            </div>
                                            {{-- Logo image --}}
                                            @if($company->logo_url)
                                                <img
                                                    src="//s3.goapi.io/logo/{{ $company->kode_emiten }}.jpg"
                                                    alt="{{ $company->kode_emiten }}"
                                                    class="absolute inset-0 w-12 h-12 rounded-full object-contain bg-white dark:bg-zinc-800 p-0.5"
                                                    x-show="imageLoaded && !imageError"
                                                    x-cloak
                                                    x-on:load="imageLoaded = true"
                                                    x-on:error="imageError = true"
                                                />
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-3">
                                                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $stockCode }}</h1>
                                                <span class="text-xs px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-400 font-medium">
                                                    {{ $company->papan_pencatatan ?? 'IDX' }}
                                                </span>
                                                <flux:tooltip content="View Details">
                                                    <flux:button
                                                        wire:click="openCompanyModal"
                                                        variant="ghost"
                                                        icon="eye"
                                                        size="xs"
                                                    >
                                                    </flux:button>
                                                </flux:tooltip>
                                                <flux:tooltip content="Change Stock">
                                                    <flux:button
                                                        wire:click="openStockPickerModal"
                                                        variant="ghost"
                                                        icon="arrows-right-left"
                                                        size="xs"
                                                    >
                                                    </flux:button>
                                                </flux:tooltip>
                                            </div>
                                            <p class="text-sm text-gray-500 dark:text-zinc-400">{{ $company?->nama_emiten ?? 'Unknown Company' }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($stockPriceSnapshot || $latestTrading)
                                    <div class="flex items-center gap-6">
                                        <!-- Current Price -->
                                        <div class="text-right">
                                            @php
                                                // Prioritize API data over database data
                                                $currentPrice = $stockPriceSnapshot ? $stockPriceSnapshot['close'] : $latestTrading->close;
                                                $currentChange = $stockPriceSnapshot ? $stockPriceSnapshot['change'] : $latestTrading->change;
                                                $currentChangePercent = $stockPriceSnapshot ? $stockPriceSnapshot['change_pct'] : $this->changePercent;
                                            @endphp
                                            <div class="text-2xl font-bold {{ $currentChange >= 0 ? 'text-teal-600 dark:text-teal-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ number_format($currentPrice, 0, ',', '.') }}
                                            </div>
                                            <div class="flex items-center justify-end gap-2 text-sm {{ $currentChange >= 0 ? 'text-teal-600 dark:text-teal-400' : 'text-red-600 dark:text-red-400' }}">
                                                @if($currentChange >= 0)
                                                    <span>+{{ number_format($currentChange, 0, ',', '.') }}</span>
                                                    <span>(+{{ number_format($currentChangePercent, 2) }}%)</span>
                                                @else
                                                    <span>{{ number_format($currentChange, 0, ',', '.') }}</span>
                                                    <span>({{ number_format($currentChangePercent, 2) }}%)</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- Quick Stats Bar -->
                    <div class="bg-gray-50 dark:bg-zinc-800/50 border-b border-gray-200 dark:border-zinc-700">
                        <div class="px-4 py-2 flex items-center gap-6 overflow-x-auto text-xs">
                            @php
                                // Use API data if available and from API, otherwise use database data
                                $useApiData = $stockPriceSnapshot && $this->isStockPriceFromApi;
                                $statsData = $useApiData ? $stockPriceSnapshot : $latestTrading;

                                // Calculate value (volume * close for API data, direct value for DB data)
                                $calculatedValue = $useApiData ? ($statsData['volume'] * $statsData['close']) : ($latestTrading ? $latestTrading->value : 0);

                                // Format values
                                $openValue = $useApiData ? $statsData['open'] : ($latestTrading ? $latestTrading->open_price : 0);
                                $highValue = $useApiData ? $statsData['high'] : ($latestTrading ? $latestTrading->high : 0);
                                $lowValue = $useApiData ? $statsData['low'] : ($latestTrading ? $latestTrading->low : 0);
                                $closeValue = $useApiData ? $statsData['close'] : ($latestTrading ? $latestTrading->close : 0);
                                $volumeValue = $useApiData ? $statsData['volume'] : ($latestTrading ? $latestTrading->volume : 0);
                                $frequencyValue = $useApiData ? 1 : ($latestTrading ? $latestTrading->frequency : 0);
                                $dateValue = $useApiData ? $statsData['date'] : ($latestTrading ? $latestTrading->date->format('Y-m-d') : null);
                            @endphp
                            <div class="flex items-center gap-2 whitespace-nowrap">
                                <span class="text-gray-500 dark:text-zinc-500">O</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ number_format($openValue, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center gap-2 whitespace-nowrap">
                                <span class="text-gray-500 dark:text-zinc-500">H</span>
                                <span class="text-teal-600 dark:text-teal-400 font-medium">{{ number_format($highValue, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center gap-2 whitespace-nowrap">
                                <span class="text-gray-500 dark:text-zinc-500">L</span>
                                <span class="text-red-600 dark:text-red-400 font-medium">{{ number_format($lowValue, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center gap-2 whitespace-nowrap">
                                <span class="text-gray-500 dark:text-zinc-500">C</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ number_format($closeValue, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-px h-4 bg-gray-300 dark:bg-zinc-600"></div>
                            <div class="flex items-center gap-2 whitespace-nowrap">
                                <span class="text-gray-500 dark:text-zinc-500">Vol</span>
                                <span class="text-gray-900 dark:text-white font-medium">
                                    @if($volumeValue >= 1000000000)
                                        {{ number_format($volumeValue / 1000000000, 2) }}B
                                    @elseif($volumeValue >= 1000000)
                                        {{ number_format($volumeValue / 1000000, 2) }}M
                                    @else
                                        {{ number_format($volumeValue, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-2 whitespace-nowrap">
                                <span class="text-gray-500 dark:text-zinc-500">Val</span>
                                <span class="text-gray-900 dark:text-white font-medium">
                                    @if($calculatedValue >= 1000000000)
                                        {{ number_format($calculatedValue / 1000000000, 2) }}B
                                    @elseif($calculatedValue >= 1000000)
                                        {{ number_format($calculatedValue / 1000000, 2) }}M
                                    @else
                                        {{ number_format($calculatedValue, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                            @if (!$useApiData)
                                <div class="flex items-center gap-2 whitespace-nowrap">
                                    <span class="text-gray-500 dark:text-zinc-500">Freq</span>
                                    <span class="text-gray-900 dark:text-white font-medium">{{ number_format($frequencyValue, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="w-px h-4 bg-gray-300 dark:bg-zinc-600"></div>
                            <div class="flex items-center gap-2 whitespace-nowrap">
                                <span class="text-gray-500 dark:text-zinc-500">
                                    @if($useApiData)
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $dateValue)->format('d M Y') }}
                                    @else
                                        {{ $latestTrading ? $latestTrading->date->format('d M Y') : '-' }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Chart Header -->
                    <div class="px-4 py-1 border-b border-gray-200 dark:border-zinc-700 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="text-gray-900 dark:text-white font-medium text-sm">Time</span>
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
                            <!-- Indicator Selector Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button
                                    @click="open = !open"
                                    class="px-3 py-1 bg-gray-100 dark:bg-zinc-700 hover:bg-gray-200 dark:hover:bg-zinc-600 text-gray-700 dark:text-zinc-300 rounded transition-colors flex items-center gap-1"
                                    title="Select Technical Indicators"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Indicators
                                    <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div
                                    x-show="open"
                                    @click.away="open = false"
                                    x-transition
                                    class="absolute top-full mt-1 right-0 z-50 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow-lg py-2 min-w-48"
                                    x-cloak
                                >
                                    <!-- Moving Averages -->
                                    <div class="px-3 py-1 text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Moving Averages</div>
                                    <div class="space-y-1">
                                        <label class="flex items-center px-3 py-1 hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                id="toggle-sma20"
                                                class="mr-2 w-3 h-3 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-zinc-700 dark:border-zinc-600"
                                            >
                                            <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-zinc-300">
                                                <span class="w-3 h-3 bg-blue-500 rounded-sm"></span>
                                                SMA 20
                                            </span>
                                        </label>
                                        <label class="flex items-center px-3 py-1 hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                id="toggle-sma50"
                                                class="mr-2 w-3 h-3 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-zinc-700 dark:border-zinc-600"
                                            >
                                            <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-zinc-300">
                                                <span class="w-3 h-3 bg-orange-500 rounded-sm"></span>
                                                SMA 50
                                            </span>
                                        </label>
                                        <label class="flex items-center px-3 py-1 hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                id="toggle-ema20"
                                                class="mr-2 w-3 h-3 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-zinc-700 dark:border-zinc-600"
                                            >
                                            <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-zinc-300">
                                                <span class="w-3 h-3 bg-green-500 rounded-sm"></span>
                                                EMA 20
                                            </span>
                                        </label>
                                        <label class="flex items-center px-3 py-1 hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                id="toggle-ema50"
                                                class="mr-2 w-3 h-3 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-zinc-700 dark:border-zinc-600"
                                            >
                                            <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-zinc-300">
                                                <span class="w-3 h-3 bg-purple-500 rounded-sm"></span>
                                                EMA 50
                                            </span>
                                        </label>
                                    </div>

                                    <div class="border-t border-gray-200 dark:border-zinc-700 my-2"></div>

                                    <!-- Bollinger Bands -->
                                    <div class="px-3 py-1 text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Bollinger Bands</div>
                                    <div class="space-y-1">
                                        <label class="flex items-center px-3 py-1 hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                id="toggle-bollinger"
                                                class="mr-2 w-3 h-3 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-zinc-700 dark:border-zinc-600"
                                            >
                                            <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-zinc-300">
                                                <span class="w-3 h-3 bg-red-500 rounded-sm"></span>
                                                Bollinger Bands
                                            </span>
                                        </label>
                                    </div>

                                    <div class="border-t border-gray-200 dark:border-zinc-700 my-2"></div>

                                    <!-- Oscillators -->
                                    <div class="px-3 py-1 text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Oscillators</div>
                                    <div class="space-y-1">
                                        <label class="flex items-center px-3 py-1 hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                id="toggle-rsi"
                                                class="mr-2 w-3 h-3 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-zinc-700 dark:border-zinc-600"
                                            >
                                            <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-zinc-300">
                                                <span class="w-3 h-3 bg-red-500 rounded-sm"></span>
                                                RSI
                                            </span>
                                        </label>
                                        <label class="flex items-center px-3 py-1 hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                id="toggle-macd"
                                                class="mr-2 w-3 h-3 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-zinc-700 dark:border-zinc-600"
                                            >
                                            <span class="flex items-center gap-2 text-sm text-gray-700 dark:text-zinc-300">
                                                <span class="w-3 h-3 bg-teal-500 rounded-sm"></span>
                                                MACD
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- TradingView Chart -->
                    <div
                        wire:ignore
                        class="relative"
                        style="height: 380px;"
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
                                @foreach($this->combinedTradingHistory as $trading)
                                    <tr class="border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors
                                        @if(isset($trading->_isFromApi) && isset($trading->_marketStatus))
                                            @php
                                                switch($trading->_marketStatus) {
                                                    case 'LIVE':
                                                        echo 'border-green-200 dark:border-green-800 bg-green-50/50 dark:bg-green-900/20';
                                                        break;
                                                    case 'BREAK':
                                                        echo 'border-yellow-200 dark:border-yellow-800 bg-yellow-50/50 dark:bg-yellow-900/20';
                                                        break;
                                                    case 'CLOSED':
                                                        echo 'border-orange-200 dark:border-orange-800 bg-orange-50/50 dark:bg-orange-900/20';
                                                        break;
                                                    case 'WEEKEND':
                                                        echo 'border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/20';
                                                        break;
                                                }
                                            @endphp
                                        @endif
                                    ">
                                        <td class="px-4 py-2 text-gray-900 dark:text-zinc-200 whitespace-nowrap">
                                            {{ $trading->date->format('d M') }}
                                            @if(isset($trading->_isFromApi) && isset($trading->_marketStatus))
                                                @php
                                                    $badgeClass = '';
                                                    $badgeText = '';
                                                    $statusColor = '';

                                                    switch($trading->_marketStatus) {
                                                        case 'LIVE':
                                                            $badgeClass = 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300';
                                                            $badgeText = 'LIVE';
                                                            $statusColor = 'border-green-200 dark:border-green-800 bg-green-50/50 dark:bg-green-900/20';
                                                            break;
                                                        case 'BREAK':
                                                            $badgeClass = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300';
                                                            $badgeText = 'BREAK';
                                                            $statusColor = 'border-yellow-200 dark:border-yellow-800 bg-yellow-50/50 dark:bg-yellow-900/20';
                                                            break;
                                                        case 'CLOSED':
                                                            $badgeClass = 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300';
                                                            $badgeText = 'CLOSED';
                                                            $statusColor = 'border-orange-200 dark:border-orange-800 bg-orange-50/50 dark:bg-orange-900/20';
                                                            break;
                                                        case 'WEEKEND':
                                                            $badgeClass = 'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300';
                                                            $badgeText = 'WEEKEND';
                                                            $statusColor = 'border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/20';
                                                            break;
                                                    }
                                                @endphp
                                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $badgeClass }}">
                                                    {{ $badgeText }}
                                                </span>
                                            @endif
                                        </td>
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

                <!-- News -->
                <div class="bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 overflow-hidden" style="display: none;">
                    <div class="px-4 py-2 border-gray-200 dark:border-zinc-700">
                        <span class="text-gray-900 dark:text-white font-medium text-sm">Latest News</span>
                    </div>
                    @if($news->isNotEmpty())
                        <div class="divide-y divide-gray-100 dark:divide-zinc-800 max-h-96 overflow-y-auto">
                            @foreach($news as $newsItem)
                                <div wire:click="openNewsModal('{{ $newsItem->item_id }}')" class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors cursor-pointer">
                                    <div class="flex gap-3">
                                        @if($newsItem->image_url)
                                            <div class="shrink-0">
                                                <img
                                                    src="//idx.co.id{{ $newsItem->image_url }}"
                                                    alt="{{ $newsItem->title }}"
                                                    class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-zinc-700"
                                                    onerror="this.style.display='none'"
                                                />
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2">
                                                    {{ $newsItem->title }}
                                                </h4>
                                                @if($newsItem->is_headline)
                                                    <span class="shrink-0 px-2 py-0.5 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 text-xs font-medium rounded">
                                                        Headline
                                                    </span>
                                                @endif
                                            </div>
                                            @if($newsItem->summary)
                                                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1 line-clamp-2">
                                                    {{ $newsItem->summary }}
                                                </p>
                                            @endif
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-xs text-gray-400 dark:text-zinc-500">
                                                    {{ $newsItem->published_date->format('d M Y, H:i') }}
                                                </span>
                                                @if($newsItem->tags)
                                                    <div class="flex flex-wrap gap-1">
                                                        @php
                                                            $tags = explode(',', $newsItem->tags);
                                                        @endphp
                                                        @foreach($tags as $tag)
                                                            @if(trim($tag) === $stockCode)
                                                                <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-400 text-xs font-medium rounded">
                                                                    {{ trim($tag) }}
                                                                </span>
                                                            @else
                                                                <span class="px-2 py-0.5 bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-zinc-400 text-xs rounded">
                                                                    {{ trim($tag) }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="px-4 py-8 text-center">
                            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center mx-auto mb-3">
                                <flux:icon.newspaper class="size-6 text-gray-400 dark:text-zinc-600" />
                            </div>
                            <p class="text-sm text-gray-500 dark:text-zinc-400">
                                No news available for {{ $stockCode }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="lg:w-80 lg:border-l border-gray-200 dark:border-zinc-700 space-y-1 order-3 md:order-3">
                <!-- Stock Price Snapshot -->
                @if($latestTrading)
                <!-- Stock Price Snapshot Card -->
                <div class="bg-white dark:bg-zinc-900 border-t md:border-t-0 mt-1 md:mt-0 border-gray-200 dark:border-zinc-700 overflow-hidden relative"
                     @if($this->isStockPriceCardVisible) wire:poll.120s.keep-alive="refreshStockPrice" @endif>

                    <div class="px-4 py-2 border-gray-200 dark:border-zinc-700">
                        <span class="text-gray-900 dark:text-white font-medium text-sm">Stock Price Snapshot</span>
                        @if($this->isStockPriceCardVisible && $stockPriceSnapshot)
                            <flux:badge color="green" icon="signal" class="ml-2 text-xs align-middle animate-pulse">Live</flux:badge>
                        @else
                            @if($this->isMarketBreak)
                                <flux:badge color="yellow" icon="clock" size="sm" class="text-xs">Break Time</flux:badge>
                            @else
                                <flux:badge color="orange" icon="lock-closed" size="sm" class="text-xs">Market Closed</flux:badge>
                            @endif
                        @endif
                    </div>
                    <div class="p-4 space-y-3">
                        <!-- Price Info -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-zinc-500">Last Price</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stockPriceSnapshot['close'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-zinc-500">Change</span>
                                <div class="flex items-center gap-1">
                                    @if($stockPriceSnapshot['change'] > 0)
                                        <flux:icon.arrow-up class="size-3 text-green-600 dark:text-green-400" />
                                        <span class="text-sm font-medium text-green-600 dark:text-green-400">+{{ number_format($stockPriceSnapshot['change'], 0, ',', '.') }}</span>
                                    @elseif($stockPriceSnapshot['change'] < 0)
                                        <flux:icon.arrow-down class="size-3 text-red-600 dark:text-red-400" />
                                        <span class="text-sm font-medium text-red-600 dark:text-red-400">{{ number_format($stockPriceSnapshot['change'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-sm font-medium text-gray-600 dark:text-zinc-400">{{ number_format($stockPriceSnapshot['change'], 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-zinc-500">Change %</span>
                                <div class="flex items-center gap-1">
                                    @if($stockPriceSnapshot['change_pct'] > 0)
                                        <span class="text-sm font-medium text-green-600 dark:text-green-400">(+{{ number_format($stockPriceSnapshot['change_pct'], 2) }}%)</span>
                                    @elseif($stockPriceSnapshot['change_pct'] < 0)
                                        <span class="text-sm font-medium text-red-600 dark:text-red-400">({{ number_format($stockPriceSnapshot['change_pct'], 2) }}%)</span>
                                    @else
                                        <span class="text-sm font-medium text-gray-600 dark:text-zinc-400">({{ number_format($stockPriceSnapshot['change_pct'], 2) }}%)</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- OHLC -->
                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100 dark:border-zinc-800">
                            <div>
                                <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-1">Open</div>
                                <div class="text-sm font-medium text-gray-700 dark:text-zinc-300">{{ number_format($stockPriceSnapshot['open'], 0, ',', '.') }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-1">High</div>
                                <div class="text-sm font-medium text-green-600 dark:text-green-400">{{ number_format($stockPriceSnapshot['high'], 0, ',', '.') }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-1">Low</div>
                                <div class="text-sm font-medium text-red-600 dark:text-red-400">{{ number_format($stockPriceSnapshot['low'], 0, ',', '.') }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase mb-1">Volume</div>
                                <div class="text-sm font-medium text-gray-700 dark:text-zinc-300">{{ number_format($stockPriceSnapshot['volume'], 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="text-center pt-2 border-t border-gray-100 dark:border-zinc-800">
                            <div class="text-[10px] text-gray-500 dark:text-zinc-500 uppercase">Last Updated</div>
                            <div class="text-xs text-gray-600 dark:text-zinc-400">
                                @if($stockPriceLastUpdated)
                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $stockPriceLastUpdated)->format('d M Y, H:i:s') }}
                                @else
                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->lastTradingSessionTime)->format('d M Y, H:i:s') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Key Statistics -->
                <div class="bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <div class="px-4 py-2 border-gray-200 dark:border-zinc-700">
                        <span class="text-gray-900 dark:text-white font-medium text-sm">Key Statistics</span>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-zinc-500">Previous Close</span>
                            <span class="text-xs text-gray-700 dark:text-zinc-300 font-medium">{{ number_format($this->previousClose, 0, ',', '.') }}</span>
                        </div>
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-zinc-500">{{ $isCustomRange ? '' : $this->periodLabel . ' ' }}High</span>
                            <span class="text-xs text-teal-600 dark:text-teal-400 font-medium">
                                @php
                                    $displayHigh = $stockPriceSnapshot && $this->isStockPriceFromApi ? max($stockPriceSnapshot['high'], $this->highestPrice) : $this->highestPrice;
                                @endphp
                                {{ number_format($displayHigh, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-zinc-500">{{ $isCustomRange ? '' : $this->periodLabel . ' ' }}Low</span>
                            <span class="text-xs text-red-600 dark:text-red-400 font-medium">
                                @php
                                    $displayLow = $stockPriceSnapshot && $this->isStockPriceFromApi ? min($stockPriceSnapshot['low'], $this->lowestPrice) : $this->lowestPrice;
                                @endphp
                                {{ number_format($displayLow, 0, ',', '.') }}
                            </span>
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
                        @php
                            $indexLabel = 'Index Individual';
                            $indexValue = $this->estimatedIndexIndividual;

                            // Jika menggunakan estimasi, beri label yang jelas
                            if ($stockPriceSnapshot && $this->isStockPriceFromApi) {
                                $indexLabel = 'Index Individual (Est)';
                            }
                        @endphp

                        <div class="px-4 py-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-zinc-500">{{ $indexLabel }}</span>
                            <span class="text-xs text-gray-700 dark:text-zinc-300 font-medium">{{ number_format($indexValue, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Book -->
                <div class="bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <div class="px-4 py-2 border-gray-200 dark:border-zinc-700">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-900 dark:text-white font-medium text-sm">Order Book (H-1)</span>
                            <span class="text-xs text-gray-500 dark:text-zinc-500">{{ $latestTrading ? $latestTrading->date->format('d M Y') : '-' }}</span>
                        </div>
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

                <!-- Foreign Flow -->
                <div class="bg-white dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <div class="px-4 py-2 border-gray-200 dark:border-zinc-700">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-900 dark:text-white font-medium text-sm">Foreign Flow (H-1)</span>
                            <span class="text-xs text-gray-500 dark:text-zinc-500">{{ $latestTrading ? $latestTrading->date->format('d M Y') : '-' }}</span>
                        </div>
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
                <button
                    wire:click="openStockPickerModal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors"
                >
                    <flux:icon.arrows-right-left class="size-4" />
                    Change Stock
                </button>
            </div>
        </div>
    @endif

    <!-- News Detail Modal -->
    <flux:modal wire:model.self="showNewsModal" class="max-w-4xl max-h-[90vh] overflow-y-auto">
        @if($selectedNews)
            <div class="space-y-2">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                            {{ $selectedNews->title }}
                        </h1>
                        @if($selectedNews->is_headline)
                            <span class="inline-block mt-2 px-3 py-1 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 text-xs font-medium rounded-full">
                                Headline News
                            </span>
                        @endif
                    </div>
                    @if($selectedNews->image_url)
                        <div class="shrink-0">
                            <img
                                src="https://idx.co.id{{ $selectedNews->image_url }}"
                                alt="{{ $selectedNews->title }}"
                                class="w-24 h-24 object-cover rounded-lg border border-gray-200 dark:border-zinc-700"
                                onerror="this.style.display='none'"
                            />
                        </div>
                    @endif
                </div>

                <!-- Meta Information -->
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-zinc-400">
                    <span class="flex items-center gap-1">
                        <flux:icon.calendar class="size-4" />
                        {{ $selectedNews->published_date->format('l, d F Y \a\t H:i') }}
                    </span>
                    @if($selectedNews->tags)
                        <div class="flex flex-wrap gap-1">
                            <span class="text-gray-400 dark:text-zinc-500">Tags:</span>
                            @php
                                $tags = explode(',', $selectedNews->tags);
                            @endphp
                            @foreach($tags as $tag)
                                @if(trim($tag) === $stockCode)
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-400 text-xs font-medium rounded">
                                        {{ trim($tag) }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-zinc-400 text-xs rounded">
                                        {{ trim($tag) }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Full Content -->
                @if($selectedNews->contents)
                    <div class="prose prose-sm dark:prose-invert max-w-none">
                        <div class="text-gray-700 dark:text-zinc-300 leading-relaxed">
                            {!! $selectedNews->contents !!}
                        </div>
                    </div>
                @endif

                <!-- Footer Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-zinc-700">
                    <flux:modal.close>
                        <flux:button variant="ghost">Close</flux:button>
                    </flux:modal.close>
                    @if($selectedNews->path_base && $selectedNews->path_file)
                        <a
                            href="https://idx.co.id{{ $selectedNews->path_base }}{{ $selectedNews->path_file }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex"
                        >
                            <flux:button variant="primary">
                                <flux:icon.arrow-up-right class="size-4 mr-2" />
                                View Original
                            </flux:button>
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- Company Detail Modal -->
    <flux:modal wire:model.self="showCompanyModal" class="max-w-4xl max-h-[90vh] overflow-y-auto">
        @if($company)
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4">
                    <!-- Company Logo -->
                    <div
                        class="relative w-16 h-16 shrink-0"
                        x-data="{ imageLoaded: false, imageError: false }"
                        x-init="imageLoaded = false; imageError = false"
                        wire:key="modal-logo-{{ $company->kode_emiten }}"
                    >
                        {{-- Fallback initials - always visible until image loads --}}
                        <div
                            class="absolute inset-0 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center"
                            x-show="!imageLoaded || imageError"
                            x-cloak
                        >
                            <span class="text-gray-600 dark:text-zinc-400 font-bold text-sm">{{ substr($company->kode_emiten, 0, 2) }}</span>
                        </div>
                        {{-- Logo image --}}
                        @if($company->logo_url)
                            <img
                                src="//s3.goapi.io/logo/{{ $company->kode_emiten }}.jpg"
                                alt="{{ $company->kode_emiten }}"
                                class="absolute inset-0 w-16 h-16 rounded-full object-contain bg-white dark:bg-zinc-800 p-0.5"
                                x-show="imageLoaded && !imageError"
                                x-cloak
                                x-on:load="imageLoaded = true"
                                x-on:error="imageError = true"
                            />
                        @endif
                    </div>
                    <div class="flex-1">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                            {{ $company->nama_emiten }}
                        </h1>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-400 text-sm font-medium rounded">
                                {{ $company->kode_emiten }}
                            </span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-zinc-400">
                                {{ $company->papan_pencatatan ?? 'IDX' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Company Information Tabs -->
                <div class="w-full" x-data="{ activeTab: 'overview' }">
                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 dark:border-zinc-700 mb-6">
                        <nav class="flex space-x-8">
                            <button
                                @click="activeTab = 'overview'"
                                :class="activeTab === 'overview'
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-zinc-400 dark:hover:text-zinc-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                                Overview
                            </button>
                            <button
                                @click="activeTab = 'management'"
                                :class="activeTab === 'management'
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-zinc-400 dark:hover:text-zinc-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                                Management
                            </button>
                            <button
                                @click="activeTab = 'ownership'"
                                :class="activeTab === 'ownership'
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-zinc-400 dark:hover:text-zinc-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                                Ownership
                            </button>
                            <button
                                @click="activeTab = 'subsidiaries'"
                                :class="activeTab === 'subsidiaries'
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-zinc-400 dark:hover:text-zinc-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                                Subsidiaries
                            </button>
                            <button
                                @click="activeTab = 'financial'"
                                :class="activeTab === 'financial'
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-zinc-400 dark:hover:text-zinc-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                                Financial
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="min-h-[400px]">
                        <!-- Overview Tab Content -->
                        <div x-show="activeTab === 'overview'" x-transition class="space-y-6">
                            <!-- Basic Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-500 dark:text-zinc-400 uppercase mb-2">Company Details</div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-800">
                                                <span class="text-sm text-gray-500 dark:text-zinc-400">Full Name</span>
                                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $company->nama_emiten }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-800">
                                                <span class="text-sm text-gray-500 dark:text-zinc-400">Stock Code</span>
                                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $company->kode_emiten }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-800">
                                                <span class="text-sm text-gray-500 dark:text-zinc-400">Board</span>
                                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $company->papan_pencatatan ?? 'IDX' }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-800">
                                                <span class="text-sm text-gray-500 dark:text-zinc-400">Sector</span>
                                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $company->sektor ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-800">
                                                <span class="text-sm text-gray-500 dark:text-zinc-400">Industry</span>
                                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $company->industri ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-800">
                                                <span class="text-sm text-gray-500 dark:text-zinc-400">Sub Industry</span>
                                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $company->sub_industri ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-800">
                                                <span class="text-sm text-gray-500 dark:text-zinc-400">Listing Date</span>
                                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $company->tanggal_pencatatan?->format('d M Y') ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-500 dark:text-zinc-400 uppercase mb-2">Contact & Website</div>
                                        <div class="space-y-3">
                                            @if($company->website_url)
                                                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-800">
                                                    <span class="text-sm text-gray-500 dark:text-zinc-400">Website</span>
                                                    <a href="{{ $company->website_url }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0 9c-1.657 0-3-4.03-3-9s1.343-9 3-9m0 18c1.657 0 3-4.03 3-9s-1.343-9-3-9"></path>
                                                        </svg>
                                                        {{ $company->website }}
                                                    </a>
                                                </div>
                                            @endif
                                            @if($company->email)
                                                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-800">
                                                    <span class="text-sm text-gray-500 dark:text-zinc-400">Email</span>
                                                    <a href="mailto:{{ $company->email }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                                        {{ $company->email }}
                                                    </a>
                                                </div>
                                            @endif
                                            @if($company->phone)
                                                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-800">
                                                    <span class="text-sm text-gray-500 dark:text-zinc-400">Phone</span>
                                                    <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $company->phone }}</span>
                                                </div>
                                            @endif
                                            @if($company->fax)
                                                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-zinc-800">
                                                    <span class="text-sm text-gray-500 dark:text-zinc-400">Fax</span>
                                                    <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $company->fax }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Index Membership -->
                                    @if($this->isLq45)
                                        <div>
                                            <div class="text-sm font-medium text-gray-500 dark:text-zinc-400 uppercase mb-2">Index Membership</div>
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 text-sm font-medium rounded-full">
                                                    <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    LQ45
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Address -->
                            @if($company->alamat || $company->kota || $company->provinsi || $company->kode_pos)
                                <div>
                                    <div class="text-sm font-medium text-gray-500 dark:text-zinc-400 uppercase mb-2">Address</div>
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        @if($company->alamat)
                                            <p>{{ $company->alamat }}</p>
                                        @endif
                                        @if($company->kota || $company->provinsi || $company->kode_pos)
                                            <p class="mt-1">
                                                @if($company->kota){{ $company->kota }}@endif
                                                @if($company->provinsi), {{ $company->provinsi }}@endif
                                                @if($company->kode_pos) {{ $company->kode_pos }}@endif
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Business Description -->
                            @if($company->deskripsi)
                                <div>
                                    <div class="text-sm font-medium text-gray-500 dark:text-zinc-400 uppercase mb-2">Business Description</div>
                                    <div class="text-sm text-gray-700 dark:text-zinc-300 leading-relaxed">
                                        {!! $company->deskripsi !!}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Management Tab Content -->
                        <div x-show="activeTab === 'management'" x-transition class="space-y-6">
                            @if($directors->isNotEmpty())
                                <div>
                                    <div class="text-sm font-medium text-gray-500 dark:text-zinc-400 uppercase mb-2">Board of Directors</div>
                                    <div class="space-y-2">
                                        @foreach($directors as $director)
                                            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-zinc-800/50 rounded-lg">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $director->nama }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $director->jabatan }}</div>
                                                </div>
                                                @if($director->afiliasi)
                                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-xs font-medium rounded">
                                                        Affiliated
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center mx-auto mb-3">
                                        <svg class="size-6 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-zinc-400">No director information available</p>
                                </div>
                            @endif
                        </div>

                        <!-- Ownership Tab Content -->
                        <div x-show="activeTab === 'ownership'" x-transition class="space-y-6">
                            @if($shareholders->isNotEmpty())
                                <div>
                                    <div class="text-sm font-medium text-gray-500 dark:text-zinc-400 uppercase mb-2">Major Shareholders</div>
                                    <div class="space-y-2">
                                        @foreach($shareholders as $shareholder)
                                            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-zinc-800/50 rounded-lg">
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $shareholder->nama }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $shareholder->kategori }}</div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($shareholder->persentase, 4, ',', '.') }}%</div>
                                                    <div class="text-xs text-gray-500 dark:text-zinc-400">{{ number_format($shareholder->jumlah, 0, ',', '.') }} shares</div>
                                                </div>
                                                @if($shareholder->pengendali)
                                                    <span class="ml-2 px-2 py-1 bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 text-xs font-medium rounded">
                                                        Controller
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center mx-auto mb-3">
                                        <svg class="size-6 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-zinc-400">No shareholder information available</p>
                                </div>
                            @endif
                        </div>

                        <!-- Subsidiaries Tab Content -->
                        <div x-show="activeTab === 'subsidiaries'" x-transition class="space-y-6">
                            @if($subsidiaries->isNotEmpty())
                                <div class="space-y-2">
                                    @foreach($subsidiaries as $subsidiary)
                                        <div class="py-2 px-3 bg-gray-50 dark:bg-zinc-800/50 rounded-lg">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $subsidiary->nama }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $subsidiary->bidang_usaha }}</div>
                                                    <div class="text-xs text-gray-400 dark:text-zinc-500">{{ $subsidiary->lokasi }}</div>
                                                </div>
                                                <div class="text-right">
                                                    @if($subsidiary->persentase)
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($subsidiary->persentase, 2, ',', '.') }}%</div>
                                                    @endif
                                                    @if($subsidiary->formatted_aset)
                                                        <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $subsidiary->formatted_aset }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-2 flex items-center gap-2">
                                                @if($subsidiary->isActive())
                                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 text-xs font-medium rounded">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 text-xs font-medium rounded">
                                                        {{ $subsidiary->status_operasi ?? 'Inactive' }}
                                                    </span>
                                                @endif
                                                @if($subsidiary->tahun_komersil)
                                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-xs font-medium rounded">
                                                        Commercial: {{ $subsidiary->tahun_komersil }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center mx-auto mb-3">
                                        <svg class="size-6 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-zinc-400">No subsidiary information available</p>
                                </div>
                            @endif
                        </div>

                        <!-- Financial Tab Content -->
                        <div x-show="activeTab === 'financial'" x-transition class="space-y-6">
                            <!-- Dividends -->
                            @if($dividends->isNotEmpty())
                                <div>
                                    <div class="text-sm font-medium text-gray-500 dark:text-zinc-400 uppercase mb-2">Dividend History</div>
                                    <div class="space-y-2">
                                        @foreach($dividends as $dividend)
                                            <div class="py-2 px-3 bg-gray-50 dark:bg-zinc-800/50 rounded-lg">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $dividend->jenis_label }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $dividend->tahun_buku }}</div>
                                                    </div>
                                                    <div class="text-right">
                                                        @if($dividend->isCashDividend() && $dividend->formatted_dps)
                                                            <div class="text-sm font-medium text-green-600 dark:text-green-400">{{ $dividend->formatted_dps }}</div>
                                                            <div class="text-xs text-gray-500 dark:text-zinc-400">per share</div>
                                                        @elseif($dividend->isStockDividend() && $dividend->total_saham_bonus)
                                                            <div class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ number_format($dividend->total_saham_bonus, 0, ',', '.') }}</div>
                                                            <div class="text-xs text-gray-500 dark:text-zinc-400">bonus shares</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mt-2 grid grid-cols-2 gap-2 text-xs">
                                                    @if($dividend->tanggal_cum)
                                                        <div>
                                                            <span class="text-gray-500 dark:text-zinc-400">Cum Date:</span>
                                                            <span class="text-gray-900 dark:text-white">{{ $dividend->tanggal_cum->format('d M Y') }}</span>
                                                        </div>
                                                    @endif
                                                    @if($dividend->tanggal_pembayaran)
                                                        <div>
                                                            <span class="text-gray-500 dark:text-zinc-400">Payment Date:</span>
                                                            <span class="text-gray-900 dark:text-white">{{ $dividend->tanggal_pembayaran->format('d M Y') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Bonds -->
                            @if($bonds->isNotEmpty())
                                <div>
                                    <div class="text-sm font-medium text-gray-500 dark:text-zinc-400 uppercase mb-2">Corporate Bonds</div>
                                    <div class="space-y-2">
                                        @foreach($bonds as $bond)
                                            <div class="py-2 px-3 bg-gray-50 dark:bg-zinc-800/50 rounded-lg">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $bond->nama_emisi }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $bond->isin_code }}</div>
                                                        @if($bond->rating)
                                                            <div class="text-xs text-yellow-600 dark:text-yellow-400">{{ $bond->rating }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="text-right">
                                                        @if($bond->formatted_nominal)
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $bond->formatted_nominal }}</div>
                                                        @endif
                                                        <div class="text-xs text-gray-500 dark:text-zinc-400">
                                                            @if($bond->isActive())
                                                                <span class="text-green-600 dark:text-green-400">Active</span>
                                                            @else
                                                                <span class="text-red-600 dark:text-red-400">Matured</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-2 grid grid-cols-2 gap-2 text-xs">
                                                    @if($bond->listing_date)
                                                        <div>
                                                            <span class="text-gray-500 dark:text-zinc-400">Listing:</span>
                                                            <span class="text-gray-900 dark:text-white">{{ $bond->listing_date->format('d M Y') }}</span>
                                                        </div>
                                                    @endif
                                                    @if($bond->mature_date)
                                                        <div>
                                                            <span class="text-gray-500 dark:text-zinc-400">Maturity:</span>
                                                            <span class="text-gray-900 dark:text-white">{{ $bond->mature_date->format('d M Y') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if($bond->wali_amanat)
                                                    <div class="mt-2 text-xs">
                                                        <span class="text-gray-500 dark:text-zinc-400">Trustee:</span>
                                                        <span class="text-gray-900 dark:text-white">{{ $bond->wali_amanat }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($dividends->isEmpty() && $bonds->isEmpty())
                                <div class="text-center py-8">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center mx-auto mb-3">
                                        <svg class="size-6 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-zinc-400">No financial information available</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-zinc-700">
                    <flux:modal.close>
                        <flux:button variant="ghost">Close</flux:button>
                    </flux:modal.close>
                    @if($company->website_url)
                        <a
                            href="{{ $company->website_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex"
                        >
                            <flux:button variant="primary">
                                <svg class="size-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Visit Website
                            </flux:button>
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- Stock Picker Modal -->
    <flux:modal wire:model.self="showStockPickerModal" class="md:w-xl max-h-[80vh]">
        <div class="space-y-4">
            <!-- Header -->
            <div>
                <flux:heading size="lg">Pilih Saham</flux:heading>
                <flux:text class="mt-1">Cari dan pilih kode saham untuk ditampilkan di dashboard</flux:text>
            </div>

            <!-- Search Input -->
            <div class="relative">
                <flux:input
                    wire:model.live.debounce.300ms="stockSearch"
                    placeholder="Cari kode saham, nama perusahaan, atau sektor..."
                    icon="magnifying-glass" clearable
                />
            </div>

            <!-- Stock List -->
            <div class="border border-gray-200 dark:border-zinc-700 rounded-lg overflow-hidden">
                <div
                    class="max-h-80 overflow-y-auto"
                    x-data="{
                        loadMore() {
                            if ($wire.hasMoreStocks) {
                                $wire.loadMoreStocks();
                            }
                        }
                    }"
                    x-on:scroll.debounce.100ms="
                        if ($el.scrollHeight - $el.scrollTop - $el.clientHeight < 100) {
                            loadMore();
                        }
                    "
                >
                    @if($stockList->isEmpty())
                        <div class="px-4 py-8 text-center">
                            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center mx-auto mb-3">
                                <flux:icon.magnifying-glass class="size-6 text-gray-400 dark:text-zinc-600" />
                            </div>
                            <p class="text-sm text-gray-500 dark:text-zinc-400">
                                @if(!empty($stockSearch))
                                    Tidak ditemukan saham dengan kata kunci "{{ $stockSearch }}"
                                @else
                                    Ketik untuk mencari saham
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                            @foreach($stockList as $stock)
                                <button
                                    wire:click="selectStock('{{ $stock->kode_emiten }}')"
                                    wire:key="stock-{{ $stock->kode_emiten }}"
                                    class="w-full px-4 py-3 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors text-left {{ $stock->kode_emiten === $stockCode ? 'bg-blue-50 dark:bg-blue-500/10' : '' }}"
                                >
                                    <!-- Logo -->
                                    <div
                                        class="relative w-10 h-10 shrink-0"
                                        x-data="{ imageLoaded: false, imageError: false }"
                                        x-init="imageLoaded = false; imageError = false"
                                        wire:key="stock-logo-{{ $stock->kode_emiten }}"
                                    >
                                        {{-- Fallback initials --}}
                                        <div
                                            class="absolute inset-0 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center"
                                            x-show="!imageLoaded || imageError"
                                            x-cloak
                                        >
                                            <span class="text-gray-600 dark:text-zinc-400 font-bold text-xs">{{ substr($stock->kode_emiten, 0, 2) }}</span>
                                        </div>
                                        {{-- Logo image --}}
                                        @if($stock->logo_url)
                                            <img
                                                src="//s3.goapi.io/logo/{{ $stock->kode_emiten }}.jpg"
                                                alt="{{ $stock->kode_emiten }}"
                                                class="absolute inset-0 w-10 h-10 rounded-full object-contain bg-white dark:bg-zinc-800 p-0.5"
                                                x-show="imageLoaded && !imageError"
                                                x-cloak
                                                x-on:load="imageLoaded = true"
                                                x-on:error="imageError = true"
                                            />
                                        @endif
                                    </div>

                                    <!-- Stock Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $stock->kode_emiten }}</span>
                                            @if($stock->papan_pencatatan)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-zinc-700 text-gray-500 dark:text-zinc-400">
                                                    {{ $stock->papan_pencatatan }}
                                                </span>
                                            @endif
                                            @if($stock->kode_emiten === $stockCode)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 font-medium">
                                                    Current
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">{{ $stock->nama_emiten }}</p>
                                    </div>

                                    <!-- Sector/Industry -->
                                    <div class="text-right shrink-0 hidden sm:block">
                                        <p class="text-xs text-gray-400 dark:text-zinc-500">{{ $stock->sektor ?? '-' }}</p>
                                    </div>

                                    <!-- Arrow -->
                                    <flux:icon.chevron-right class="size-4 text-gray-400 dark:text-zinc-600 shrink-0" />
                                </button>
                            @endforeach

                            {{-- Loading indicator --}}
                            @if($hasMoreStocks)
                                <div class="px-4 py-3 flex items-center justify-center" wire:loading.class="opacity-100" wire:loading.class.remove="opacity-50">
                                    <div class="flex items-center gap-2 text-gray-400 dark:text-zinc-500">
                                        <flux:icon.arrow-path class="size-4 animate-spin" wire:loading wire:target="loadMoreStocks" />
                                        <span class="text-xs" wire:loading wire:target="loadMoreStocks">Memuat lebih banyak...</span>
                                        <span class="text-xs" wire:loading.remove wire:target="loadMoreStocks">Scroll untuk memuat lebih banyak</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-gray-500 dark:text-zinc-400">
                    @if($stockList->isNotEmpty())
                        Menampilkan {{ $stockList->count() }} saham
                        @if($hasMoreStocks)
                            <span class="text-gray-400 dark:text-zinc-600">(scroll untuk lebih banyak)</span>
                        @endif
                    @endif
                </p>
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</div>

@push('scripts')
<script src="https://unpkg.com/lightweight-charts@5.1.0/dist/lightweight-charts.standalone.production.js"></script>
<script>
(function() {
    // Use window object to persist state across wire:navigate
    if (!window.DashboardChart) {
        window.DashboardChart = {
            chart: null,
            candlestickSeries: null,
            volumeSeries: null,
            candlestickData: null,
            volumeData: null,
            rsiSeries: null,
            macdSeries: null,
            macdHistogramSeries: null,
            macdSignalSeries: null,
            indicatorSeries: {},
            indicatorStates: {
                sma20: false,
                sma50: false,
                ema20: false,
                ema50: false,
                bollinger: false,
                rsi: false,
                macd: false
            },
            resizeObserver: null,
            themeObserver: null,
            retryCount: 0,
            maxRetries: 15
        };
    }

    const SC = window.DashboardChart;

    function isOnDashboardPage() {
        return document.querySelector('[data-dashboard]') !== null;
    }

    function getDashboardComponent() {
        const el = document.querySelector('[data-dashboard][wire\\:id]');
        if (el) {
            const componentId = el.getAttribute('wire:id');
            if (componentId && typeof Livewire !== 'undefined') {
                return Livewire.find(componentId);
            }
        }
        return null;
    }

    function getChartData() {
        const component = getDashboardComponent();
        return component ? component.chartData : null;
    }

    function cleanupChart() {
        if (SC.chart) {
            try { SC.chart.remove(); } catch(e) {}
            SC.chart = null;
        }
        SC.candlestickSeries = null;
        SC.volumeSeries = null;
        SC.rsiSeries = null;
        SC.macdSeries = null;
        SC.macdHistogramSeries = null;
        SC.macdSignalSeries = null;
        SC.indicatorSeries = {};
        if (SC.resizeObserver) {
            SC.resizeObserver.disconnect();
            SC.resizeObserver = null;
        }
    }

    function initChart(chartData, isRetry = false) {
        // Check if we're on the dashboard page
        if (!isOnDashboardPage()) {
            return;
        }

        const container = document.getElementById('chart-container');
        if (!container) {
            if (SC.retryCount < SC.maxRetries) {
                SC.retryCount++;
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
                volume: SC.volumeData || [],
                indicators: SC.indicatorData || {}
            };
        }
        if (!data) {
            data = getChartData();
        }
        if (!data || !data.candlestick || data.candlestick.length === 0) {
            return;
        }

        if (typeof LightweightCharts === 'undefined') {
            if (SC.retryCount < SC.maxRetries) {
                SC.retryCount++;
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
                background: { type: 'solid', color: isDark ? '#171717' : '#ffffff' },
                textColor: isDark ? '#a1a1aa' : '#71717a',
            },
            grid: {
                vertLines: { color: isDark ? '#27272a' : '#f4f4f5' },
                horzLines: { color: isDark ? '#27272a' : '#f4f4f5' },
            },
            localization: {
                timeFormatter: (time) => {
                    // Check if this time point has API data
                    const component = getDashboardComponent();
                    if (component && SC.candlestickData && SC.candlestickData.length > 0) {
                        const currentIndex = SC.candlestickData.findIndex(c => c.time === time);
                        const isFromApi = currentIndex >= 0 ? SC.candlestickData[currentIndex]._isFromApi : false;

                        if (currentIndex >= 0 && isFromApi && component.candlestickLastUpdated) {
                            // For API data, show date and time according to market hours
                            const now = new Date();
                            const updateDate = new Date(component.candlestickLastUpdated);

                            // Function to check if current time is within market operating hours
                            function isMarketOpen(currentTime) {
                                const dayOfWeek = currentTime.getDay(); // 0 = Sunday, 1 = Monday, ..., 5 = Friday
                                const hours = currentTime.getHours();
                                const minutes = currentTime.getMinutes();
                                const seconds = currentTime.getSeconds();
                                const currentTimeInSeconds = hours * 3600 + minutes * 60 + seconds;

                                // Market closed on weekends
                                if (dayOfWeek === 0 || dayOfWeek === 6) {
                                    return false;
                                }

                                // Session I: Monday-Thursday 09:00:00 – 12:00:00, Friday 09:00:00 – 11:30:00
                                const session1Start = 9 * 3600; // 09:00:00
                                const session1EndFriday = 11 * 3600 + 30 * 60; // 11:30:00
                                const session1EndWeekday = 12 * 3600; // 12:00:00

                                // Session II: Monday-Thursday 13:30:00 – 15:49:59, Friday 14:00:00 – 15:49:59
                                const session2StartWeekday = 13 * 3600 + 30 * 60; // 13:30:00
                                const session2StartFriday = 14 * 3600; // 14:00:00
                                const session2End = 15 * 3600 + 49 * 60 + 59; // 15:49:59

                                if (dayOfWeek === 5) { // Friday
                                    // Check Session I: 09:00:00 – 11:30:00
                                    if (currentTimeInSeconds >= session1Start && currentTimeInSeconds <= session1EndFriday) {
                                        return true;
                                    }
                                    // Check Session II: 14:00:00 – 15:49:59
                                    if (currentTimeInSeconds >= session2StartFriday && currentTimeInSeconds <= session2End) {
                                        return true;
                                    }
                                } else { // Monday-Thursday
                                    // Check Session I: 09:00:00 – 12:00:00
                                    if (currentTimeInSeconds >= session1Start && currentTimeInSeconds <= session1EndWeekday) {
                                        return true;
                                    }
                                    // Check Session II: 13:30:00 – 15:49:59
                                    if (currentTimeInSeconds >= session2StartWeekday && currentTimeInSeconds <= session2End) {
                                        return true;
                                    }
                                }

                                return false;
                            }

                            // Function to get last market closing time
                            function getLastMarketClose(currentTime) {
                                const dayOfWeek = currentTime.getDay();
                                const hours = currentTime.getHours();
                                const minutes = currentTime.getMinutes();
                                const seconds = currentTime.getSeconds();
                                const currentTimeInSeconds = hours * 3600 + minutes * 60 + seconds;
                                const year = currentTime.getFullYear();
                                const month = currentTime.getMonth();
                                const day = currentTime.getDate();

                                // Market closed on weekends - show previous Friday's closing
                                if (dayOfWeek === 0 || dayOfWeek === 6) {
                                    const friday = new Date(currentTime);
                                    friday.setDate(day - ((dayOfWeek === 0) ? 2 : 1)); // Sunday = -2, Saturday = -1
                                    return new Date(friday.getFullYear(), friday.getMonth(), friday.getDate(), 15, 49, 59);
                                }

                                // Session times in seconds
                                const session1Start = 9 * 3600; // 09:00:00
                                const session1EndWeekday = 12 * 3600; // 12:00:00
                                const session1EndFriday = 11 * 3600 + 30 * 60; // 11:30:00
                                const session2StartWeekday = 13 * 3600 + 30 * 60; // 13:30:00
                                const session2StartFriday = 14 * 3600; // 14:00:00
                                const session2End = 15 * 3600 + 49 * 60 + 59; // 15:49:59

                                if (dayOfWeek === 5) { // Friday
                                    if (currentTimeInSeconds <= session1EndFriday) {
                                        // Before or during Session I - show yesterday's (Thursday) closing
                                        const thursday = new Date(currentTime);
                                        thursday.setDate(day - 1);
                                        return new Date(thursday.getFullYear(), thursday.getMonth(), thursday.getDate(), 15, 49, 59);
                                    } else if (currentTimeInSeconds <= session2StartFriday) {
                                        // Break period after Session I - show Session I closing (11:30:00)
                                        return new Date(year, month, day, 11, 30, 0);
                                    } else if (currentTimeInSeconds <= session2End) {
                                        // During Session II - show Session I closing during break, but this is handled by isMarketOpen check
                                        return new Date(year, month, day, 11, 30, 0);
                                    } else {
                                        // After market close - show Session II closing
                                        return new Date(year, month, day, 15, 49, 59);
                                    }
                                } else { // Monday-Thursday
                                    if (currentTimeInSeconds <= session1EndWeekday) {
                                        // Before or during Session I - show yesterday's closing
                                        const yesterday = new Date(currentTime);
                                        yesterday.setDate(day - 1);
                                        return new Date(yesterday.getFullYear(), yesterday.getMonth(), yesterday.getDate(), 15, 49, 59);
                                    } else if (currentTimeInSeconds <= session2StartWeekday) {
                                        // Break period after Session I - show Session I closing (12:00:00)
                                        return new Date(year, month, day, 12, 0, 0);
                                    } else if (currentTimeInSeconds <= session2End) {
                                        // During Session II - show Session I closing during break, but this is handled by isMarketOpen check
                                        return new Date(year, month, day, 12, 0, 0);
                                    } else {
                                        // After market close - show Session II closing
                                        return new Date(year, month, day, 15, 49, 59);
                                    }
                                }
                            }

                            // Determine which time to display
                            let displayTime;
                            if (isMarketOpen(now)) {
                                // Market is open, show actual last update time
                                displayTime = updateDate;
                            } else {
                                // Market is closed, show last market closing time
                                displayTime = getLastMarketClose(now);
                            }

                            const dateStr = displayTime.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });
                            const timeStr = displayTime.toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            }).replace('.', ':');
                            return `${dateStr}, ${timeStr}`;
                        }
                    }

                    // Default formatting for non-API data
                    const date = new Date(time);
                    return date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                }
            },
            crosshair: {
                mode: LightweightCharts.CrosshairMode.Normal,
                vertLine: {
                    color: isDark ? '#6b7280' : '#9ca3af',
                    width: 1,
                    style: LightweightCharts.LineStyle.Dashed,
                    labelVisible: true,
                    labelBackgroundColor: isDark ? '#374151' : '#f9fafb',
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
                // tickMarkFormatter: (time) => {
                //     // Check if this time point has API data
                //     const component = getDashboardComponent();
                //     if (component && SC.candlestickData && SC.candlestickData.length > 0) {
                //         const currentIndex = SC.candlestickData.findIndex(c => c.time === time);
                //         const isFromApi = currentIndex >= 0 ? SC.candlestickData[currentIndex]._isFromApi : false;

                //         if (currentIndex >= 0 && isFromApi && component.candlestickLastUpdated) {
                //             // For API data, show date and time
                //             const updateDate = new Date(component.candlestickLastUpdated);
                //             const dateStr = updateDate.toLocaleDateString('id-ID', {
                //                 day: '2-digit',
                //                 month: 'short',
                //                 year: 'numeric'
                //             });
                //             const timeStr = updateDate.toLocaleTimeString('id-ID', {
                //                 hour: '2-digit',
                //                 minute: '2-digit',
                //                 hour12: false
                //             }).replace('.', ':');
                //             return `${dateStr} ${timeStr}`;
                //         }
                //     }

                //     // Default formatting for non-API data
                //     const date = new Date(time);
                //     return date.toLocaleDateString('id-ID', {
                //         day: '2-digit',
                //         month: 'short',
                //         year: 'numeric'
                //     });
                // }
            },
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

        // RSI Pane
        if (data.indicators && data.indicators.rsi && data.indicators.rsi.length > 0) {
            SC.rsiSeries = SC.chart.addSeries(LightweightCharts.LineSeries, {
                priceScaleId: 'rsi',
                color: '#FF6B6B',
                lineWidth: 1,
                title: 'RSI (14)',
                priceLineVisible: false,
                lastValueVisible: false,
                visible: SC.indicatorStates.rsi,
            });

            // Set RSI scale to 0-100 range
            SC.chart.priceScale('rsi').applyOptions({
                scaleMargins: { top: 0.85, bottom: 0 },
                autoScale: false,
                mode: LightweightCharts.PriceScaleMode.Normal,
            });

            // Set fixed range for RSI (0-100)
            SC.rsiSeries.priceScale().applyOptions({
                scaleMargins: { top: 0.1, bottom: 0.1 },
            });

            SC.rsiSeries.setData(data.indicators.rsi);

            // Add RSI reference lines (30 and 70)
            SC.rsiSeries.createPriceLine({
                price: 70,
                color: '#FF9800',
                lineWidth: 1,
                lineStyle: LightweightCharts.LineStyle.Dashed,
                axisLabelVisible: true,
                title: 'Overbought (70)',
            });

            SC.rsiSeries.createPriceLine({
                price: 30,
                color: '#4CAF50',
                lineWidth: 1,
                lineStyle: LightweightCharts.LineStyle.Dashed,
                axisLabelVisible: true,
                title: 'Oversold (30)',
            });
        }

        // MACD Pane
        if (data.indicators && data.indicators.macd) {
            // MACD Line
            SC.macdSeries = SC.chart.addSeries(LightweightCharts.LineSeries, {
                priceScaleId: 'macd',
                color: '#4ECDC4',
                lineWidth: 1,
                title: 'MACD',
                priceLineVisible: false,
                lastValueVisible: false,
                visible: SC.indicatorStates.macd,
            });

            // MACD Signal Line
            SC.macdSignalSeries = SC.chart.addSeries(LightweightCharts.LineSeries, {
                priceScaleId: 'macd',
                color: '#FF6B6B',
                lineWidth: 1,
                title: 'Signal',
                priceLineVisible: false,
                lastValueVisible: false,
                visible: SC.indicatorStates.macd,
            });

            // MACD Histogram
            SC.macdHistogramSeries = SC.chart.addSeries(LightweightCharts.HistogramSeries, {
                priceScaleId: 'macd',
                color: '#26a69a',
                title: 'MACD Histogram',
                priceLineVisible: false,
                lastValueVisible: false,
                visible: SC.indicatorStates.macd,
            });

            SC.chart.priceScale('macd').applyOptions({
                scaleMargins: { top: 0.85, bottom: 0 },
            });

            // Format MACD data for chart
            const macdData = [];
            const signalData = [];
            const histogramData = [];

            data.indicators.macd.macd.forEach((macd, index) => {
                const date = data.candlestick[index]?.time;
                if (date && macd !== null) {
                    macdData.push({ time: date, value: macd });
                }
            });

            data.indicators.macd.signal.forEach((signal, index) => {
                const date = data.candlestick[index]?.time;
                if (date && signal !== null) {
                    signalData.push({ time: date, value: signal });
                }
            });

            data.indicators.macd.histogram.forEach((hist, index) => {
                const date = data.candlestick[index]?.time;
                if (date && hist !== null) {
                    const color = hist >= 0 ? 'rgba(76, 175, 80, 0.8)' : 'rgba(244, 67, 54, 0.8)';
                    histogramData.push({ time: date, value: hist, color });
                }
            });

            SC.macdSeries.setData(macdData);
            SC.macdSignalSeries.setData(signalData);
            SC.macdHistogramSeries.setData(histogramData);
        }

        // Technical Indicators - Line Series Overlays
        if (data.indicators) {
            // SMA 20
            if (data.indicators.sma_20 && data.indicators.sma_20.length > 0) {
                SC.indicatorSeries.sma20 = SC.chart.addSeries(LightweightCharts.LineSeries, {
                    color: '#2196F3',
                    lineWidth: 1,
                    title: 'SMA 20',
                    priceLineVisible: false,
                    lastValueVisible: false,
                    visible: SC.indicatorStates.sma20,
                });
                SC.indicatorSeries.sma20.setData(data.indicators.sma_20);
            }

            // SMA 50
            if (data.indicators.sma_50 && data.indicators.sma_50.length > 0) {
                SC.indicatorSeries.sma50 = SC.chart.addSeries(LightweightCharts.LineSeries, {
                    color: '#FF9800',
                    lineWidth: 1,
                    title: 'SMA 50',
                    priceLineVisible: false,
                    lastValueVisible: false,
                    visible: SC.indicatorStates.sma50,
                });
                SC.indicatorSeries.sma50.setData(data.indicators.sma_50);
            }

            // EMA 20
            if (data.indicators.ema_20 && data.indicators.ema_20.length > 0) {
                SC.indicatorSeries.ema20 = SC.chart.addSeries(LightweightCharts.LineSeries, {
                    color: '#4CAF50',
                    lineWidth: 1,
                    title: 'EMA 20',
                    priceLineVisible: false,
                    lastValueVisible: false,
                    visible: SC.indicatorStates.ema20,
                });
                SC.indicatorSeries.ema20.setData(data.indicators.ema_20);
            }

            // EMA 50
            if (data.indicators.ema_50 && data.indicators.ema_50.length > 0) {
                SC.indicatorSeries.ema50 = SC.chart.addSeries(LightweightCharts.LineSeries, {
                    color: '#9C27B0',
                    lineWidth: 1,
                    title: 'EMA 50',
                    priceLineVisible: false,
                    lastValueVisible: false,
                    visible: SC.indicatorStates.ema50,
                });
                SC.indicatorSeries.ema50.setData(data.indicators.ema_50);
            }

            // Bollinger Bands
            if (data.indicators.bollinger_upper && data.indicators.bollinger_upper.length > 0) {
                SC.indicatorSeries.bollingerUpper = SC.chart.addSeries(LightweightCharts.LineSeries, {
                    color: '#FF5722',
                    lineWidth: 1,
                    lineStyle: LightweightCharts.LineStyle.Dashed,
                    title: 'BB Upper',
                    priceLineVisible: false,
                    lastValueVisible: false,
                    visible: SC.indicatorStates.bollinger,
                });
                SC.indicatorSeries.bollingerUpper.setData(data.indicators.bollinger_upper);
            }

            if (data.indicators.bollinger_middle && data.indicators.bollinger_middle.length > 0) {
                SC.indicatorSeries.bollingerMiddle = SC.chart.addSeries(LightweightCharts.LineSeries, {
                    color: '#795548',
                    lineWidth: 1,
                    lineStyle: LightweightCharts.LineStyle.Dotted,
                    title: 'BB Middle',
                    priceLineVisible: false,
                    lastValueVisible: false,
                    visible: SC.indicatorStates.bollinger,
                });
                SC.indicatorSeries.bollingerMiddle.setData(data.indicators.bollinger_middle);
            }

            if (data.indicators.bollinger_lower && data.indicators.bollinger_lower.length > 0) {
                SC.indicatorSeries.bollingerLower = SC.chart.addSeries(LightweightCharts.LineSeries, {
                    color: '#3F51B5',
                    lineWidth: 1,
                    lineStyle: LightweightCharts.LineStyle.Dashed,
                    title: 'BB Lower',
                    priceLineVisible: false,
                    lastValueVisible: false,
                    visible: SC.indicatorStates.bollinger,
                });
                SC.indicatorSeries.bollingerLower.setData(data.indicators.bollinger_lower);
            }
        }

        // Store indicator data for theme changes
        if (data.indicators) {
            SC.indicatorData = data.indicators;
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

        // Setup indicator toggles
        setupIndicatorToggle();
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

            // Update technical indicators for any range change (panning)
            const component = getDashboardComponent();
            if (component) {
                component.call('updateIndicatorsForVisibleRange');
            }

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

        const component = getDashboardComponent();
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

            // Check if this is API candlestick data
            const component = getDashboardComponent();
            let displayTime = param.time;

            if (component && SC.candlestickData && SC.candlestickData.length > 0) {
                const currentIndex = SC.candlestickData.findIndex(c => c.time === param.time);

                // If this candlestick has API data, show last update time according to market hours
                if (currentIndex >= 0 && SC.candlestickData[currentIndex]._isFromApi && component.candlestickLastUpdated) {
                    const now = new Date();
                    const updateDate = new Date(component.candlestickLastUpdated);

                    // Function to check if current time is within market operating hours
                    function isMarketOpen(currentTime) {
                        const dayOfWeek = currentTime.getDay(); // 0 = Sunday, 1 = Monday, ..., 5 = Friday
                        const hours = currentTime.getHours();
                        const minutes = currentTime.getMinutes();
                        const seconds = currentTime.getSeconds();
                        const currentTimeInSeconds = hours * 3600 + minutes * 60 + seconds;

                        // Market closed on weekends
                        if (dayOfWeek === 0 || dayOfWeek === 6) {
                            return false;
                        }

                        // Session I: Monday-Thursday 09:00:00 – 12:00:00, Friday 09:00:00 – 11:30:00
                        const session1Start = 9 * 3600; // 09:00:00
                        const session1EndFriday = 11 * 3600 + 30 * 60; // 11:30:00
                        const session1EndWeekday = 12 * 3600; // 12:00:00

                        // Session II: Monday-Thursday 13:30:00 – 15:49:59, Friday 14:00:00 – 15:49:59
                        const session2StartWeekday = 13 * 3600 + 30 * 60; // 13:30:00
                        const session2StartFriday = 14 * 3600; // 14:00:00
                        const session2End = 15 * 3600 + 49 * 60 + 59; // 15:49:59

                        if (dayOfWeek === 5) { // Friday
                            // Check Session I: 09:00:00 – 11:30:00
                            if (currentTimeInSeconds >= session1Start && currentTimeInSeconds <= session1EndFriday) {
                                return true;
                            }
                            // Check Session II: 14:00:00 – 15:49:59
                            if (currentTimeInSeconds >= session2StartFriday && currentTimeInSeconds <= session2End) {
                                return true;
                            }
                        } else { // Monday-Thursday
                            // Check Session I: 09:00:00 – 12:00:00
                            if (currentTimeInSeconds >= session1Start && currentTimeInSeconds <= session1EndWeekday) {
                                return true;
                            }
                            // Check Session II: 13:30:00 – 15:49:59
                            if (currentTimeInSeconds >= session2StartWeekday && currentTimeInSeconds <= session2End) {
                                return true;
                            }
                        }

                        return false;
                    }

                    // Function to get last market closing time
                    function getLastMarketClose(currentTime) {
                        const dayOfWeek = currentTime.getDay();
                        const hours = currentTime.getHours();
                        const minutes = currentTime.getMinutes();
                        const seconds = currentTime.getSeconds();
                        const currentTimeInSeconds = hours * 3600 + minutes * 60 + seconds;
                        const year = currentTime.getFullYear();
                        const month = currentTime.getMonth();
                        const day = currentTime.getDate();

                        // Market closed on weekends - show previous Friday's closing
                        if (dayOfWeek === 0 || dayOfWeek === 6) {
                            const friday = new Date(currentTime);
                            friday.setDate(day - ((dayOfWeek === 0) ? 2 : 1)); // Sunday = -2, Saturday = -1
                            return new Date(friday.getFullYear(), friday.getMonth(), friday.getDate(), 15, 49, 59);
                        }

                        // Session times in seconds
                        const session1Start = 9 * 3600; // 09:00:00
                        const session1EndWeekday = 12 * 3600; // 12:00:00
                        const session1EndFriday = 11 * 3600 + 30 * 60; // 11:30:00
                        const session2StartWeekday = 13 * 3600 + 30 * 60; // 13:30:00
                        const session2StartFriday = 14 * 3600; // 14:00:00
                        const session2End = 15 * 3600 + 49 * 60 + 59; // 15:49:59

                        if (dayOfWeek === 5) { // Friday
                            if (currentTimeInSeconds <= session1EndFriday) {
                                // Before or during Session I - show yesterday's (Thursday) closing
                                const thursday = new Date(currentTime);
                                thursday.setDate(day - 1);
                                return new Date(thursday.getFullYear(), thursday.getMonth(), thursday.getDate(), 15, 49, 59);
                            } else if (currentTimeInSeconds <= session2StartFriday) {
                                // Break period after Session I - show Session I closing (11:30:00)
                                return new Date(year, month, day, 11, 30, 0);
                            } else if (currentTimeInSeconds <= session2End) {
                                // During Session II - show Session I closing during break, but this is handled by isMarketOpen check
                                return new Date(year, month, day, 11, 30, 0);
                            } else {
                                // After market close - show Session II closing
                                return new Date(year, month, day, 15, 49, 59);
                            }
                        } else { // Monday-Thursday
                            if (currentTimeInSeconds <= session1EndWeekday) {
                                // Before or during Session I - show yesterday's closing
                                const yesterday = new Date(currentTime);
                                yesterday.setDate(day - 1);
                                return new Date(yesterday.getFullYear(), yesterday.getMonth(), yesterday.getDate(), 15, 49, 59);
                            } else if (currentTimeInSeconds <= session2StartWeekday) {
                                // Break period after Session I - show Session I closing (12:00:00)
                                return new Date(year, month, day, 12, 0, 0);
                            } else if (currentTimeInSeconds <= session2End) {
                                // During Session II - show Session I closing during break, but this is handled by isMarketOpen check
                                return new Date(year, month, day, 12, 0, 0);
                            } else {
                                // After market close - show Session II closing
                                return new Date(year, month, day, 15, 49, 59);
                            }
                        }
                    }

                    // Determine which time to display
                    let displayDate;
                    if (isMarketOpen(now)) {
                        // Market is open, show actual last update time
                        displayDate = updateDate;
                    } else {
                        // Market is closed, show last market closing time
                        displayDate = getLastMarketClose(now);
                    }

                    const dateStr = displayDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                    const timeStr = displayDate.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    }).replace('.', ':');
                    displayTime = `${dateStr}, ${timeStr}`;
                } else {
                    const dateStr = new Date(param.time).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                    displayTime = `${dateStr}`;
                }
            }

            legendDate.textContent = displayTime;
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
    }

    function formatVolume(value) {
        if (value >= 1e9) return (value / 1e9).toFixed(2) + 'B';
        if (value >= 1e6) return (value / 1e6).toFixed(2) + 'M';
        if (value >= 1e3) return (value / 1e3).toFixed(2) + 'K';
        return value.toLocaleString('id-ID');
    }

    function setupIndicatorToggle() {
        // Setup individual indicator checkboxes
        const indicators = ['sma20', 'sma50', 'ema20', 'ema50', 'bollinger', 'rsi', 'macd'];

        indicators.forEach(indicator => {
            const checkbox = document.getElementById(`toggle-${indicator}`);
            if (checkbox) {
                // Set initial state from SC.indicatorStates
                checkbox.checked = SC.indicatorStates[indicator];

                // Add change event listener
                checkbox.addEventListener('change', (e) => {
                    SC.indicatorStates[indicator] = e.target.checked;
                    toggleIndicator(indicator, e.target.checked);
                });
            }
        });

        // Update checkbox states to sync with current chart state
        updateCheckboxStates();
    }

    function toggleIndicator(indicator, visible) {
        switch (indicator) {
            case 'sma20':
                if (SC.indicatorSeries.sma20) {
                    try { SC.indicatorSeries.sma20.applyOptions({ visible }); } catch(e) {}
                }
                break;
            case 'sma50':
                if (SC.indicatorSeries.sma50) {
                    try { SC.indicatorSeries.sma50.applyOptions({ visible }); } catch(e) {}
                }
                break;
            case 'ema20':
                if (SC.indicatorSeries.ema20) {
                    try { SC.indicatorSeries.ema20.applyOptions({ visible }); } catch(e) {}
                }
                break;
            case 'ema50':
                if (SC.indicatorSeries.ema50) {
                    try { SC.indicatorSeries.ema50.applyOptions({ visible }); } catch(e) {}
                }
                break;
            case 'bollinger':
                if (SC.indicatorSeries.bollingerUpper) {
                    try { SC.indicatorSeries.bollingerUpper.applyOptions({ visible }); } catch(e) {}
                }
                if (SC.indicatorSeries.bollingerMiddle) {
                    try { SC.indicatorSeries.bollingerMiddle.applyOptions({ visible }); } catch(e) {}
                }
                if (SC.indicatorSeries.bollingerLower) {
                    try { SC.indicatorSeries.bollingerLower.applyOptions({ visible }); } catch(e) {}
                }
                break;
            case 'rsi':
                if (SC.rsiSeries) {
                    try { SC.rsiSeries.applyOptions({ visible }); } catch(e) {}
                }
                break;
            case 'macd':
                if (SC.macdSeries) {
                    try { SC.macdSeries.applyOptions({ visible }); } catch(e) {}
                }
                if (SC.macdHistogramSeries) {
                    try { SC.macdHistogramSeries.applyOptions({ visible }); } catch(e) {}
                }
                break;
        }
    }

    function updateCheckboxStates() {
        // Update checkbox states to match current indicator visibility
        const indicators = ['sma20', 'sma50', 'ema20', 'ema50', 'bollinger', 'rsi', 'macd'];

        indicators.forEach(indicator => {
            const checkbox = document.getElementById(`toggle-${indicator}`);
            if (checkbox) {
                checkbox.checked = SC.indicatorStates[indicator];
            }
        });
    }

    // Set period to 1M (30 days) via Livewire
    function setPeriodTo1M() {
        const component = getDashboardComponent();
        if (component) {
            component.set('period', '30');
            return true;
        }
        return false;
    }

    // Initialize chart with retry
    function startInit() {
        if (!isOnDashboardPage()) return;

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
    if (!window.dashboardChartEventsRegistered) {
        window.dashboardChartEventsRegistered = true;

        // Re-init on Livewire navigation
        document.addEventListener('livewire:navigated', () => {
            if (isOnDashboardPage()) {
                // Chart will be reinitialized by the global event listeners

        // Register event listeners globally
        // Chart data updated
        Livewire.on('chart-data-updated', (data) => {
            // Handle both array and object formats
            const eventData = Array.isArray(data) ? data[0] : data;

            if (eventData && eventData.chartData) {
                SC.retryCount = 0;
                SC.noMoreData = false;
                SC.initialLoadComplete = false; // Reset to prevent immediate load on period change
                initChart(eventData.chartData);
            }
        });

                // Stock price updated - refresh chart immediately
                Livewire.on('stock-price-updated', (data) => {
                    // Handle both array and object formats
                    const eventData = Array.isArray(data) ? data[0] : data;

                    if (eventData && eventData.hasNewData) {
                        // Trigger chart refresh from server side
                        const component = getDashboardComponent();
                        if (component) {
                            component.call('refreshChart');
                        }
                    }
                });

                // More data loaded (infinite scroll)
                Livewire.on('more-data-loaded', (eventData) => {
                    const data = eventData[0] || eventData;

                    if (SC.candlestickSeries && data.candlestick && data.candlestick.length > 0) {
                        // Get current data
                        const currentData = SC.candlestickSeries.data() || [];

                        // Prepend new data (older data comes before) and preserve _isFromApi flags
                        const mergedCandlestick = [...data.candlestick, ...currentData];
                        SC.candlestickSeries.setData(mergedCandlestick);

                        // Preserve _isFromApi flags from existing data
                        const existingDataWithFlags = SC.candlestickData || [];
                        const updatedCandlestickData = mergedCandlestick.map((candle, index) => {
                            // For newly added data (from the beginning), check if there's existing data with same time
                            const existingIndex = existingDataWithFlags.findIndex(existing => existing.time === candle.time);
                            if (existingIndex >= 0) {
                                // Preserve the _isFromApi flag from existing data
                                return { ...candle, _isFromApi: existingDataWithFlags[existingIndex]._isFromApi || false };
                            }
                            // For truly new data, set _isFromApi to false
                            return { ...candle, _isFromApi: false };
                        });

                        SC.candlestickData = updatedCandlestickData; // Update stored data for legend

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
                    SC.noMoreData = true;
                    SC.isLoadingMore = false;
                });
            }
        });

        // Theme change handler - watch for class changes on <html> element
        let lastDarkState = document.documentElement.classList.contains('dark');

        SC.themeObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class' && isOnDashboardPage()) {
                    const currentDarkState = document.documentElement.classList.contains('dark');
                    // Only reinit if dark state actually changed
                    if (currentDarkState !== lastDarkState) {
                        lastDarkState = currentDarkState;
                        SC.retryCount = 0;
                        // Use longer delay to ensure Flux has updated
                        setTimeout(() => {
                            if (isOnDashboardPage()) {
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
