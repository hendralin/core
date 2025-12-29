<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Stock Summary') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Monitor daily stock trading data') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/80">
            <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                <!-- Per Page Selector -->
                <flux:select wire:model.live="perPage" label="Per Page">
                    @foreach ($this->perPageOptions as $option)
                    <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                    @endforeach
                </flux:select>

                <!-- Search Input -->
                <div class="flex-1 min-w-0">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        label="{{ __('Search') }}"
                        placeholder="{{ __('Stock code or company name...') }}"
                        icon="magnifying-glass"
                        clearable />
                </div>

                <!-- Trading Date -->
                <div class="w-full lg:w-48">
                    <flux:input
                        type="date"
                        wire:model.live="date"
                        label="{{ __('Trading Date') }}" />
                </div>

                <!-- Column Visibility -->
                <div class="shrink-0">
                    <flux:dropdown>
                        <flux:button icon="view-columns">
                            {{ __('Columns') }}
                            <flux:badge size="sm" color="blue" class="ml-1">{{ $this->visibleColumnCount }}</flux:badge>
                        </flux:button>

                        <flux:menu class="w-64 max-h-80 overflow-y-auto">
                            <flux:menu.heading>{{ __('Toggle Columns') }}</flux:menu.heading>

                            <!-- Select All / Deselect All -->
                            @if($this->isAllColumnsSelected)
                                <flux:menu.item wire:click="deselectAllColumns">
                                    <div class="flex items-center justify-between w-full font-medium">
                                        <span>{{ __('Deselect All') }}</span>
                                        <flux:icon.x-mark class="size-4 text-red-500" />
                                    </div>
                                </flux:menu.item>
                            @else
                                <flux:menu.item wire:click="selectAllColumns">
                                    <div class="flex items-center justify-between w-full font-medium">
                                        <span>{{ __('Select All') }}</span>
                                        <flux:icon.check-circle class="size-4 text-green-500" />
                                    </div>
                                </flux:menu.item>
                            @endif

                            <flux:menu.separator />

                            @foreach($availableColumns as $column => $config)
                                <flux:menu.item wire:click="toggleColumn('{{ $column }}')">
                                    <div class="flex items-center justify-between w-full">
                                        <span>{{ __($config['label']) }}</span>
                                        @if(in_array($column, $visibleColumns))
                                            <flux:icon.check class="size-4 text-green-500" />
                                        @endif
                                    </div>
                                </flux:menu.item>
                            @endforeach

                            <flux:menu.separator />

                            <flux:menu.item wire:click="resetColumns">
                                <div class="flex items-center text-blue-600 dark:text-blue-400">
                                    <flux:icon.arrow-path class="size-4 mr-2" />
                                    {{ __('Reset to Default') }}
                                </div>
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>

                <!-- Clear Filters Button -->
                <div class="shrink-0">
                    <flux:button variant="ghost" wire:click="clearFilters" icon="x-mark">
                        {{ __('Clear') }}
                    </flux:button>
                </div>

                <!-- Loading Indicator -->
                <div wire:loading class="shrink-0">
                    <flux:icon.loading class="text-blue-600 size-6" />
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden mb-6">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:border-zinc-700 dark:bg-zinc-800/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-16">
                            {{ __('No') }}
                        </th>

                        @foreach($availableColumns as $column => $config)
                            @if(in_array($column, $visibleColumns))
                                <th class="px-4 py-3 text-{{ $config['align'] }} text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">
                                    @if($config['sortable'])
                                        <div class="flex items-center {{ $config['align'] === 'right' ? 'justify-end' : '' }} cursor-pointer" wire:click="sortBy('{{ $column }}')">
                                            {{ __($config['label']) }}
                                            @if($sortField === $column)
                                                <flux:icon.chevron-up class="size-4 ml-1 {{ $sortDirection === 'desc' ? 'rotate-180' : '' }}" />
                                            @else
                                                <flux:icon.chevron-up-down class="size-4 ml-1" />
                                            @endif
                                        </div>
                                    @else
                                        {{ __($config['label']) }}
                                    @endif
                                </th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $startNumber = $isAllData ? 1 : (($stocks->currentPage() - 1) * $stocks->perPage() + 1);
                    @endphp
                    @forelse($isAllData ? $stocks : $stocks->items() as $index => $stock)
                        <tr wire:loading.class="opacity-50" class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 hover:bg-gray-100 dark:border-zinc-700 dark:hover:bg-zinc-700/50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-zinc-400">
                                {{ $startNumber + $index }}
                            </td>

                            @if(in_array('kode_emiten', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-zinc-100">
                                    {{ $stock->kode_emiten }}
                                </td>
                            @endif

                            @if(in_array('company_name', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-zinc-400">
                                    {{ $stock->stockCompany?->nama_emiten ?? '-' }}
                                </td>
                            @endif

                            @if(in_array('previous', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->previous, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('open_price', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->open_price, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('high', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->high, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('low', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->low, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('close', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->close, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('change', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium {{ $stock->change > 0 ? 'text-green-600 dark:text-green-400' : ($stock->change < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-zinc-400') }}">
                                    @if($stock->change > 0)
                                        <span class="inline-flex items-center justify-end">
                                            <flux:icon.arrow-up class="size-3 mr-1" />
                                            {{ number_format($stock->change, 0, ',', '.') }}
                                        </span>
                                    @elseif($stock->change < 0)
                                        <span class="inline-flex items-center justify-end">
                                            <flux:icon.arrow-down class="size-3 mr-1" />
                                            {{ number_format(abs($stock->change), 0, ',', '.') }}
                                        </span>
                                    @else
                                        {{ number_format($stock->change, 0, ',', '.') }}
                                    @endif
                                </td>
                            @endif

                            @if(in_array('volume', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->volume, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('value', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    @if($stock->value >= 1000000000)
                                        {{ number_format($stock->value / 1000000000, 2, ',', '.') }} B
                                    @elseif($stock->value >= 1000000)
                                        {{ number_format($stock->value / 1000000, 2, ',', '.') }} M
                                    @else
                                        {{ number_format($stock->value, 0, ',', '.') }}
                                    @endif
                                </td>
                            @endif

                            @if(in_array('frequency', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->frequency, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('index_individual', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->index_individual, 4, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('offer', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->offer, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('offer_volume', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->offer_volume, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('bid', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->bid, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('bid_volume', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->bid_volume, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('listed_shares', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->listed_shares, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('tradeble_shares', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->tradeble_shares, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('foreign_sell', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->foreign_sell, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('foreign_buy', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->foreign_buy, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('non_regular_volume', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->non_regular_volume, 0, ',', '.') }}
                                </td>
                            @endif

                            @if(in_array('non_regular_value', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    @if($stock->non_regular_value >= 1000000000)
                                        {{ number_format($stock->non_regular_value / 1000000000, 2, ',', '.') }} B
                                    @elseif($stock->non_regular_value >= 1000000)
                                        {{ number_format($stock->non_regular_value / 1000000, 2, ',', '.') }} M
                                    @else
                                        {{ number_format($stock->non_regular_value, 0, ',', '.') }}
                                    @endif
                                </td>
                            @endif

                            @if(in_array('non_regular_frequency', $visibleColumns))
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-600 dark:text-zinc-400">
                                    {{ number_format($stock->non_regular_frequency, 0, ',', '.') }}
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($visibleColumns) + 1 }}" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center justify-center">
                                    <flux:icon.chart-bar class="size-12 mb-2 text-gray-400 dark:text-zinc-500" />
                                    @if(!empty($search))
                                        <p>{{ __('No results found for') }} "{{ $search }}"</p>
                                    @else
                                        <p>{{ __('No trading data available') }}</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
    <!-- Pagination -->
    @if(!$isAllData)
        {{ $stocks->links(data: ['scrollTo' => false]) }}
    @else
        <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-800/50 border border-gray-200 dark:border-zinc-700 rounded-lg">
            <p class="text-sm text-gray-600 dark:text-zinc-400">
                {{ __('Showing all') }} <span class="font-medium">{{ $stocks->count() }}</span> {{ __('results') }}
            </p>
        </div>
    @endif
</div>
