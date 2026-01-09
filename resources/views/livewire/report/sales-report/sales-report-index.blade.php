<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Laporan Penjualan') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Laporan penjualan kendaraan') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach($stats as $key => $stat)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ $stat['formatted'] }}
                        </p>
                    </div>
                    <div class="shrink-0">
                        @if($stat['color'] === 'green')
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                <flux:icon.currency-dollar class="w-5 h-5 text-green-600 dark:text-green-400" />
                            </div>
                        @elseif($stat['color'] === 'blue')
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                <flux:icon.truck class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                        @elseif($stat['color'] === 'emerald')
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg flex items-center justify-center">
                                <flux:icon.banknotes class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                        @elseif($stat['color'] === 'purple')
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                <flux:icon.percent-badge class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Filter Section -->
    <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-lg p-4 mb-6 border border-gray-200 dark:border-zinc-700">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Month/Year Filter -->
            <div>
                <label for="month-year" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Bulan & Tahun</label>
                <input type="month"
                       id="month-year"
                       wire:model.live="selectedMonthYear"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-zinc-200 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                       min="2019-01"
                       max="{{ date('Y') + 1 }}-12">
            </div>

            <!-- Date Period Filters -->
            <flux:input type="date" wire:model.live="dateFrom" label="Dari Tanggal" size="sm" />
            <flux:input type="date" wire:model.live="dateTo" label="Sampai Tanggal" size="sm" />

            <!-- Clear Filters Button -->
            @if($dateFrom !== \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') || $dateTo !== \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') || $selectedMonthYear)
            <div class="space-y-2 flex flex-col justify-end">
                <flux:button wire:click="clearFilters" variant="filled" size="sm" icon="x-mark" class="w-full cursor-pointer">
                    Hapus Filter
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

    <!-- Vehicle Sales Cards Grid -->
    @if($vehicles->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            @foreach($vehicles as $vehicle)
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6 hover:shadow-md transition-shadow">
                    <!-- Header with Vehicle Info -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                {{ $vehicle->police_number ?? 'N/A' }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-zinc-400">
                                @if($vehicle->brand)
                                    {{ $vehicle->brand->name }}
                                @endif
                                @if($vehicle->vehicle_model)
                                    {{ $vehicle->vehicle_model->name }}
                                @endif
                                @if($vehicle->type)
                                    {{ $vehicle->type->name }}
                                @endif
                                @if($vehicle->year)
                                    ({{ $vehicle->year }})
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-green-600 dark:text-green-400">
                                Rp {{ number_format($vehicle->selling_price ?? 0, 0) }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-zinc-500">
                                Harga Jual
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Details -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Tanggal Jual</span>
                            <p class="text-sm text-gray-900 dark:text-white mt-1">
                                @if($vehicle->selling_date)
                                    {{ \Carbon\Carbon::parse($vehicle->selling_date)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Warna</span>
                            <p class="text-sm text-gray-900 dark:text-white mt-1">
                                {{ $vehicle->color ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Kilometer</span>
                            <p class="text-sm text-gray-900 dark:text-white mt-1">
                                @if($vehicle->kilometer)
                                    {{ number_format($vehicle->kilometer, 0) }} km
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Jenis Bahan Bakar</span>
                            <p class="text-sm text-gray-900 dark:text-white mt-1">
                                {{ $vehicle->fuel_type ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Buyer Information -->
                    @if($vehicle->buyer_name)
                        <div class="border-t border-gray-100 dark:border-zinc-700 pt-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Informasi Pembeli</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-zinc-400">Nama:</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $vehicle->buyer_name }}</span>
                                </div>
                                @if($vehicle->buyer_phone)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-zinc-400">Telepon:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $vehicle->buyer_phone }}</span>
                                    </div>
                                @endif
                                @if($vehicle->buyer_address)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-zinc-400">Alamat:</span>
                                        <span class="text-sm text-gray-900 dark:text-white max-w-32 truncate" title="{{ $vehicle->buyer_address }}">{{ $vehicle->buyer_address }}</span>
                                    </div>
                                @endif
                                @if($vehicle->payment_type)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-zinc-400">Pembayaran:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $vehicle->payment_type == 'cash' ? 'Cash' : 'Kredit' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Salesman Information -->
                    @if($vehicle->salesman)
                        <div class="border-t border-gray-100 dark:border-zinc-700 pt-4 mt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-zinc-400">Salesman:</span>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $vehicle->salesman->name }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Purchase Information -->
                    <div class="border-t border-gray-100 dark:border-zinc-700 pt-4 mt-4">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Informasi Pembelian</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-zinc-400">Harga Jadi ({{ \Carbon\Carbon::parse($vehicle->purchase_date)->format('d.m.Y') }}):</span>
                                <span class="text-sm text-gray-900 dark:text-white">
                                    @if(isset($vehicle->purchase_price))
                                        Rp {{ number_format($vehicle->purchase_price, 0) }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            @if($vehicle->commissions->where('type', 2)->count() > 0)
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-zinc-400">Komisi Pembelian ({{ $vehicle->commissions->where('type', 2)->count() }}x):</span>
                                <span class="text-sm text-gray-900 dark:text-white">
                                    Rp {{ number_format($vehicle->commissions->where('type', 2)->sum('amount'), 0) }}
                                </span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-zinc-400">Modal Awal:</span>
                                <span class="text-sm text-gray-900 dark:text-white">
                                    Rp {{ number_format($vehicle->purchase_price + $vehicle->commissions->where('type', 2)->where('type', 2)->sum('amount'), 0) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Information -->
                    @if($vehicle->costs->count() > 0)
                    <div class="border-t border-gray-100 dark:border-zinc-700 pt-4 mt-4">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Pembukuan Modal</h4>
                        <div class="space-y-2">
                            @foreach($vehicle->costs as $cost)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-zinc-400">{{ $cost->description }} {{ $cost->vendor->name ?? '-' }} ({{ \Carbon\Carbon::parse($cost->cost_date)->format('d/m/Y') }}):</span>
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        Rp {{ number_format($cost->total_price, 0) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Total Cost Information, Total Modal Keseluruhan, Harga Jual ALL IN, Keuntungan -->
                    <div class="border-t border-gray-100 dark:border-zinc-700 pt-4 mt-4">
                        <div class="flex justify-between">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Total Biaya (Pembukuan Modal)</h4>
                            <div class="text-sm text-gray-900 dark:text-white">
                                Rp {{ number_format($vehicle->costs->sum('total_price'), 0) }}
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Total Modal Keseluruhan</h4>
                            <div class="text-sm text-gray-900 dark:text-white">
                                Rp {{ number_format($vehicle->purchase_price + $vehicle->commissions->where('type', 2)->where('type', 2)->sum('amount') + $vehicle->costs->sum('total_price'), 0) }}
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Harga Jual ALL IN</h4>
                            <div class="text-sm text-gray-900 dark:text-white">
                                Rp {{ number_format($vehicle->selling_price, 0) }}
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Keuntungan</h4>
                            <div class="text-sm text-gray-900 dark:text-white">
                                Rp {{ number_format($vehicle->selling_price - $vehicle->purchase_price - $vehicle->commissions->where('type', 2)->where('type', 2)->sum('amount') - $vehicle->costs->sum('total_price'), 0) }}
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Margin Keuntungan</h4>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ number_format(($vehicle->selling_price - $vehicle->purchase_price - $vehicle->commissions->where('type', 2)->where('type', 2)->sum('amount') - $vehicle->costs->sum('total_price')) / $vehicle->selling_price * 100, 1, ',', '.') }}%
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6 mb-2">
            {{ $vehicles->links(data: ['scrollTo' => false]) }}
        </div>
    @else
        <div class="text-center py-12">
            <flux:icon.document class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada data penjualan</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Belum ada kendaraan yang terjual dalam periode ini.</p>
        </div>
    @endif
</div>
