<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Laporan Kas') }}</flux:heading>
        <flux:subheading size="lg" class="mb-2">{{ __('Laporan pengeluaran kas perusahaan') }}</flux:subheading>
        <p class="text-sm text-gray-500 dark:text-zinc-400 mb-6">Biaya selain Cash hanya diakui dan tampil setelah ada tanggal pembayaran.</p>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach($stats as $key => $stat)
            <div wire:click="filterByCostType('{{ $key }}')" class="cursor-pointer bg-white dark:bg-zinc-800 rounded-lg shadow-sm border @if($selectedCostType === $key) border-blue-500 dark:border-blue-400 ring-2 ring-blue-500/20 dark:ring-blue-400/20 @else border-gray-200 dark:border-zinc-700 @endif p-4 hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            Rp {{ number_format($stat['total'], 0) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">
                            {{ $stat['count'] }} transactions
                        </p>
                    </div>
                    <div class="shrink-0">
                        @php
                            $bgClass = match($stat['color']) {
                                'blue' => 'bg-blue-100 dark:bg-blue-900/20',
                                'green' => 'bg-green-100 dark:bg-green-900/20',
                                'orange' => 'bg-orange-100 dark:bg-orange-900/20',
                                'emerald' => 'bg-emerald-100 dark:bg-emerald-900/20',
                                'rose' => 'bg-rose-100 dark:bg-rose-900/20',
                                default => 'bg-zinc-100 dark:bg-zinc-700/30',
                            };
                            $iconClass = match($stat['color']) {
                                'blue' => 'text-blue-600 dark:text-blue-400',
                                'green' => 'text-green-600 dark:text-green-400',
                                'orange' => 'text-orange-600 dark:text-orange-400',
                                'emerald' => 'text-emerald-600 dark:text-emerald-400',
                                'rose' => 'text-rose-600 dark:text-rose-400',
                                default => 'text-zinc-600 dark:text-zinc-300',
                            };
                        @endphp
                        <div class="w-10 h-10 {{ $bgClass }} rounded-lg flex items-center justify-center">
                            @switch($stat['icon'] ?? '')
                                @case('building-storefront')
                                    <flux:icon.building-storefront class="w-5 h-5 {{ $iconClass }}" />
                                    @break
                                @case('wrench')
                                    <flux:icon.wrench class="w-5 h-5 {{ $iconClass }}" />
                                    @break
                                @case('currency-dollar')
                                    <flux:icon.currency-dollar class="w-5 h-5 {{ $iconClass }}" />
                                    @break
                                @case('lifebuoy')
                                    <flux:icon.lifebuoy class="w-5 h-5 {{ $iconClass }}" />
                                    @break
                                @case('receipt-refund')
                                    <flux:icon.receipt-refund class="w-5 h-5 {{ $iconClass }}" />
                                    @break
                                @default
                                    <flux:icon.document-text class="w-5 h-5 {{ $iconClass }}" />
                            @endswitch
                        </div>
                    </div>
                </div>
                @if($stat['total'] > 0)
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-zinc-700">
                        <div class="flex items-center text-xs">
                            @php
                                $dotClass = match($stat['color']) {
                                    'blue' => 'bg-blue-500',
                                    'green' => 'bg-green-500',
                                    'orange' => 'bg-orange-500',
                                    'emerald' => 'bg-emerald-500',
                                    'rose' => 'bg-rose-500',
                                    default => 'bg-zinc-500',
                                };
                            @endphp
                            <div class="w-2 h-2 {{ $dotClass }} rounded-full mr-2"></div>
                            <span class="text-gray-600 dark:text-zinc-400">Active this period</span>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Filter Section -->
    <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-4 mb-6 border border-gray-200 dark:border-zinc-700">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Month/Year Filter -->
            <div>
                <label for="month-year" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Month & Year</label>
                <input type="month"
                       id="month-year"
                       wire:model.live="selectedMonthYear"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-zinc-200 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                       min="2019-01"
                       max="{{ date('Y') + 1 }}-12">
            </div>

            <!-- Date Period Filters -->
            <flux:input type="date" wire:model.live="dateFrom" label="From" size="sm" />
            <flux:input type="date" wire:model.live="dateTo" label="To" size="sm" />

            <!-- Warehouse Filter -->
            <div>
                <label for="warehouse" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Warehouse</label>
                <select
                    id="warehouse"
                    wire:model.live="selectedWarehouseId"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-zinc-200 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                >
                    <option value="">{{ __('All Warehouses') }}</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Clear Filters Button -->
            @if($dateFrom !== \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') || $dateTo !== \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') || $selectedCostType || $selectedMonthYear || $selectedWarehouseId)
            <div class="space-y-2 flex flex-col justify-end">
                <flux:button wire:click="clearFilters" variant="filled" size="sm" icon="x-mark" class="w-full cursor-pointer">
                    Clear Filter
                </flux:button>
            </div>
            @endif
        </div>
    </div>

    <div class="space-y-4 mb-2">
        <!-- Actions Section -->
        <div class="flex flex-col lg:flex-row gap-3">
            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2">
                <!-- Button Actions -->
                <div class="flex gap-1">
                    <flux:button variant="ghost" size="sm" wire:click="exportExcel" icon="document-arrow-down" tooltip="Export to Excel" class="flex-1 sm:flex-none cursor-pointer">
                        <span class="hidden sm:inline">Excel</span>
                        <span class="sm:hidden">Excel</span>
                    </flux:button>
                    <flux:button variant="ghost" size="sm" wire:click="exportPdf" icon="document-arrow-down" tooltip="Export to PDF" class="flex-1 sm:flex-none cursor-pointer">
                        <span class="hidden sm:inline">PDF</span>
                        <span class="sm:hidden">PDF</span>
                    </flux:button>

                    <div wire:loading class="flex items-center justify-center p-2">
                        <flux:icon.loading class="text-red-600 w-4 h-4" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Per Page Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 mb-3 mt-4">
        <div class="flex items-center gap-2 w-44 mb-2 md:mb-0">
            <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">Show:</label>
            <flux:select id="per-page" wire:model.live="perPage">
                @foreach ($this->perPageOptions as $option)
                <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                @endforeach
            </flux:select>
            <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">entries</label>
        </div>
        <flux:spacer class="hidden md:inline" />
        <flux:spacer class="hidden md:inline" />
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 border dark:border-zinc-700 dark:text-zinc-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-4 py-3 w-10 text-center">No.</th>
                    <th scope="col" class="px-4 py-3 w-52">Date</th>
                    <th scope="col" class="px-4 py-3">Description</th>
                    <th scope="col" class="px-4 py-3 text-right w-32">Debet</th>
                    <th scope="col" class="px-4 py-3 text-right w-32">Kredit</th>
                    <th scope="col" class="px-4 py-3 text-right w-32">Balance</th>
                </tr>
            </thead>
            <tbody>
                @if($costs->currentPage() === 1)
                    <!-- Opening Balance Row -->
                    <tr class="bg-blue-50 dark:bg-zinc-900/30 border-b-2 border-blue-200 dark:border-zinc-700">
                        <td class="px-4 py-2 text-center font-semibold text-blue-900 dark:text-blue-100">-</td>
                        <td class="px-4 py-2 font-semibold text-blue-900 dark:text-blue-100" colspan="4">Opening Balance</td>
                        <td class="px-4 py-2 text-right font-bold @if($openingBalance >= 0) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif">
                            @if($openingBalance >= 0)
                                Rp {{ number_format($openingBalance, 0) }}
                            @else
                                -Rp {{ number_format(abs($openingBalance), 0) }}
                            @endif
                        </td>
                    </tr>
                @endif
                @if(isset($costs) && $costs->count() > 0)
                    @foreach($costsWithBalance as $index => $cost)
                        <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-white">{{ $costs->firstItem() + $index }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-white">{{ Carbon\Carbon::parse($cost->cost_date)->format('d-m-Y') }}</td>
                            <td class="px-4 py-2 lg:whitespace-normal text-gray-600 dark:text-zinc-300 max-w-xs">
                                <div class="flex flex-col gap-0.5">
                                    <span class="truncate" title="{{ $cost->description }}">{{ $cost->description }}</span>
                                    @if($cost->vehicle)
                                        <span class="text-xs">{{ $cost->vehicle->police_number }}</span>
                                    @endif
                                    @if($cost->vendor)
                                        <span class="text-xs">- {{ $cost->vendor->name }}</span>
                                    @endif
                                    @if($cost->warehouse)
                                        <span class="text-xs text-gray-500 dark:text-zinc-400">{{ __('Warehouse') }}: {{ $cost->warehouse->name }}</span>
                                    @endif
                                    @if($cost->cost_type !== 'cash' && $cost->payments->isNotEmpty())
                                        <span class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">Pembayaran: {{ \Carbon\Carbon::parse($cost->payments->first()->payment_date)->format('d-m-Y') }}</span>
                                    @endif
                                </div>
                            </td>
                            @if(in_array($cost->cost_type, ['cash', 'loan_payment'], true))
                                <td class="px-4 py-2 whitespace-nowrap text-right font-medium text-gray-900 dark:text-white">-</td>
                                <td class="px-4 py-2 whitespace-nowrap text-right font-medium text-green-600 dark:text-green-400">Rp {{ number_format($cost->total_price, 0) }}</td>
                            @else
                                <td class="px-4 py-2 whitespace-nowrap text-right font-medium text-gray-900 dark:text-white">Rp {{ number_format($cost->total_price, 0) }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-right font-medium text-gray-900 dark:text-white">-</td>
                            @endif
                            <td class="px-4 py-2 whitespace-nowrap text-right font-medium @if($cost->running_balance >= 0) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif">
                                @if($cost->running_balance >= 0)
                                    Rp {{ number_format($cost->running_balance, 0) }}
                                @else
                                    -Rp {{ number_format(abs($cost->running_balance), 0) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 border-gray-200">
                        <td class="px-4 py-2 text-gray-600 dark:text-zinc-300 text-center" colspan="6">
                            Tidak ada data laporan kas yang ditemukan.
                        </td>
                    </tr>
                @endif

                <!-- Total Footer - Always show for current period/filters -->
                @if($totalDebet > 0 || $totalKredit > 0)
                    <tr class="bg-blue-50 dark:bg-zinc-900/20 border-t-2 border-blue-200 dark:border-zinc-800">
                        <td colspan="2" class="px-4 py-3">
                            @php
                                $activeFilters = [];
                                if ($dateFrom !== \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') || $dateTo !== \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')) {
                                    $activeFilters[] = 'period (' . \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($dateTo)->format('d/m/Y') . ')';
                                }
                                if ($selectedCostType) {
                                    $activeFilters[] = 'cost type: ' . ($stats[$selectedCostType]['label'] ?? $selectedCostType);
                                }
                                if ($selectedMonthYear) {
                                    $date = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonthYear);
                                    $activeFilters[] = 'month/year: ' . $date->format('F Y');
                                }
                                if (!empty($selectedWarehouseName)) {
                                    $activeFilters[] = 'warehouse: ' . $selectedWarehouseName;
                                }
                                if (empty($activeFilters)) {
                                    $activeFilters[] = 'current month';
                                }
                            @endphp
                            @if(!empty($activeFilters))
                                <div class="text-xs text-zinc-700 dark:text-zinc-200 mt-1 whitespace-nowrap">
                                    Filter by {{ implode(', ', $activeFilters) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">
                            TOTAL:
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-zinc-900 dark:text-zinc-100 whitespace-nowrap">
                            Rp {{ number_format($totalDebet, 0) }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600 dark:text-green-400 whitespace-nowrap">
                            Rp {{ number_format($totalKredit, 0) }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold @if($netBalance >= 0) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif whitespace-nowrap">
                            @if($netBalance >= 0)
                                Rp {{ number_format($netBalance, 0) }}
                            @else
                                -Rp {{ number_format(abs($netBalance), 0) }}
                            @endif
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 mb-2">
        {{ $costs->links(data: ['scrollTo' => false]) }}
    </div>
</div>
