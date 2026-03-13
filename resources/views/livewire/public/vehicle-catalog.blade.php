<div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <flux:heading size="xl" level="1" class="mb-2">
                Katalog Kendaraan
            </flux:heading>
            <flux:text class="text-base text-gray-600 dark:text-zinc-300">
                Jelajahi koleksi kendaraan yang kami jual. Pilih berdasarkan merek, tipe, tahun, dan harga.
            </flux:text>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-4 mb-8 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div class="md:col-span-2">
                    <flux:input
                        wire:model.live.debounce.500ms="search"
                        placeholder="Cari merek, tipe, model, atau nomor polisi..."
                        leading-icon="magnifying-glass"
                        clearable
                    />
                </div>

                <div>
                    <flux:select wire:model.live="brandId">
                        <flux:select.option value="">Semua Merek</flux:select.option>
                        @foreach ($brands as $brand)
                            <flux:select.option value="{{ $brand->id }}">{{ $brand->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:select wire:model.live="typeId">
                        <flux:select.option value="">Semua Tipe</flux:select.option>
                        @foreach ($types as $type)
                            <flux:select.option value="{{ $type->id }}">{{ $type->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <flux:input
                        type="number"
                        wire:model.live.debounce.500ms="minYear"
                        placeholder="Tahun min"
                    />
                    <flux:input
                        type="number"
                        wire:model.live.debounce.500ms="maxYear"
                        placeholder="Tahun max"
                    />
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="grid grid-cols-2 gap-2">
                    <flux:input
                        type="number"
                        wire:model.live.debounce.500ms="minPrice"
                        placeholder="Harga min (Rp)"
                    />
                    <flux:input
                        type="number"
                        wire:model.live.debounce.500ms="maxPrice"
                        placeholder="Harga max (Rp)"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-zinc-300">
                        Tampilkan:
                    </span>
                    <flux:select wire:model.live="perPage" class="w-24">
                        <flux:select.option value="8">8</flux:select.option>
                        <flux:select.option value="12">12</flux:select.option>
                        <flux:select.option value="24">24</flux:select.option>
                    </flux:select>
                    <span class="text-sm text-gray-600 dark:text-zinc-300">
                        per halaman
                    </span>
                </div>

                <div class="md:col-span-2 flex justify-end">
                    <flux:button
                        variant="ghost"
                        size="sm"
                        icon="arrow-path"
                        wire:click="resetFilters"
                        class="cursor-pointer"
                    >
                        Reset Filter
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Catalog Grid -->
        @if ($vehicles->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($vehicles as $vehicle)
                    @php
                        $thumbnail = $vehicle->images->first();
                    @endphp
                    <a
                        href="{{ route('public.vehicles.show', $vehicle->slug) }}"
                        wire:navigate
                        class="group bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-150 flex flex-col"
                    >
                        <div class="relative aspect-video bg-gray-100 dark:bg-zinc-900 overflow-hidden">
                            @if ($thumbnail)
                                <img
                                    src="{{ asset('photos/vehicles/' . $thumbnail->image) }}"
                                    alt="{{ $vehicle->brand?->name }} {{ $vehicle->vehicle_model?->name }} {{ $vehicle->year }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                    onerror="this.style.display='none'"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <flux:icon.photo class="w-10 h-10 text-gray-300 dark:text-zinc-600" />
                                </div>
                            @endif

                            @if ($vehicle->created_at && $vehicle->created_at->diffInDays() <= 7)
                                <span class="absolute top-3 left-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-600 text-white shadow">
                                    Baru
                                </span>
                            @endif

                            @if ($vehicle->images->count() > 1)
                                <span class="absolute bottom-2 right-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-black/60 text-white backdrop-blur">
                                    {{ $vehicle->images->count() }} foto
                                </span>
                            @endif
                        </div>

                        <div class="flex-1 flex flex-col p-4 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-zinc-400">
                                    {{ $vehicle->brand?->name ?? '-' }} • {{ $vehicle->type?->name ?? '-' }}
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    Available
                                </span>
                            </div>

                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $vehicle->vehicle_model?->name ?? '-' }} {{ $vehicle->year }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-zinc-400">
                                    {{ $vehicle->police_number }}
                                </div>
                            </div>

                            <div class="mt-1">
                                <div class="text-lg font-bold text-green-600 dark:text-green-400">
                                    @if ($vehicle->loan_price)
                                        Rp {{ number_format($vehicle->loan_price, 0, ',', '.') }}
                                    @else
                                        Hubungi kami
                                    @endif
                                </div>
                                @if ($vehicle->display_price)
                                    <div class="text-xs text-blue-600 dark:text-blue-400">
                                        Cash mulai Rp {{ number_format($vehicle->display_price, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between pt-2 text-xs text-gray-500 dark:text-zinc-400">
                                <div class="flex items-center gap-3">
                                    @if ($vehicle->kilometer)
                                        <span>{{ number_format($vehicle->kilometer, 0, ',', '.') }} km</span>
                                    @endif
                                    <span>{{ $vehicle->color ?? '-' }}</span>
                                </div>
                                <span>{{ $vehicle->created_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $vehicles->links(data: ['scrollTo' => false]) }}
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-10 text-center">
                <flux:icon.exclamation-triangle class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                <flux:heading size="lg" class="mb-2">
                    Tidak ada kendaraan ditemukan
                </flux:heading>
                <flux:text class="text-gray-600 dark:text-zinc-300">
                    Coba ubah kata kunci atau filter pencarian Anda.
                </flux:text>
            </div>
        @endif
    </div>
</div>

