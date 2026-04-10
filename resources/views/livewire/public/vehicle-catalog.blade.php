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
                        placeholder="Cari merek, tipe, atau model..."
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
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-4 lg:gap-6">
                @foreach ($vehicles as $vehicle)
                    @php
                        $thumbnail = $vehicle->images->first();
                    @endphp
                    <a
                        href="{{ route('public.vehicles.show', $vehicle->slug) }}"
                        wire:navigate
                        class="group bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg sm:rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-150 flex flex-col"
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
                                    <flux:icon.photo class="w-7 h-7 sm:w-10 sm:h-10 text-gray-300 dark:text-zinc-600" />
                                </div>
                            @endif

                            @if ($vehicle->created_at && $vehicle->created_at->diffInDays() <= 7)
                                <span class="absolute top-1.5 left-1.5 sm:top-3 sm:left-3 inline-flex items-center px-1.5 py-px sm:px-2.5 sm:py-0.5 rounded-full text-[9px] sm:text-xs font-medium bg-blue-600 text-white shadow">
                                    Baru
                                </span>
                            @endif

                            @if ($vehicle->images->count() > 1)
                                <span class="absolute bottom-1.5 right-1.5 sm:bottom-2 sm:right-2 inline-flex items-center px-1.5 py-px sm:px-2 sm:py-0.5 rounded-full text-[9px] sm:text-xs font-medium bg-black/60 text-white backdrop-blur">
                                    {{ $vehicle->images->count() }} foto
                                </span>
                            @endif
                        </div>

                        <div class="flex-1 flex flex-col gap-y-2 sm:gap-y-3 p-2 sm:p-3 lg:p-4 min-h-0 sm:min-h-[11rem]">
                            <div class="flex items-start justify-between gap-1 sm:gap-2 min-w-0">
                                <p class="min-w-0 text-[8px] sm:text-[10px] font-semibold uppercase tracking-wide sm:tracking-wider text-emerald-600 dark:text-emerald-400 leading-tight line-clamp-2 sm:line-clamp-3" title="{{ trim(($vehicle->brand?->name ?? '-') . ' · ' . ($vehicle->type?->name ?? '-')) }}">
                                    {{ $vehicle->brand?->name ?? '-' }}
                                    <span class="text-gray-400 dark:text-zinc-500 font-normal">·</span>
                                    {{ $vehicle->type?->name ?? '-' }}
                                </p>
                                <span class="shrink-0 inline-flex items-center px-1 py-px sm:px-2 sm:py-0.5 rounded-full text-[8px] sm:text-[10px] font-semibold uppercase tracking-wide
                                    bg-emerald-100 text-emerald-800 dark:bg-emerald-900/80 dark:text-emerald-200 ring-1 ring-inset ring-emerald-200/60 dark:ring-emerald-700/50">
                                    <span class="sm:hidden">Ada</span>
                                    <span class="hidden sm:inline">Tersedia</span>
                                </span>
                            </div>

                            <h3 class="text-xs sm:text-sm lg:text-[15px] font-bold text-gray-900 dark:text-white leading-tight sm:leading-snug line-clamp-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                {{ $vehicle->vehicle_model?->name ?? '-' }}
                                <span class="text-gray-500 dark:text-zinc-400 font-semibold">{{ $vehicle->year }}</span>
                            </h3>

                            <div class="rounded-lg sm:rounded-xl border border-emerald-200/90 dark:border-emerald-800/60 bg-gradient-to-br from-emerald-50 via-white to-white dark:from-emerald-950/35 dark:via-zinc-800/90 dark:to-zinc-800 p-2 sm:p-3 shadow-sm shadow-emerald-900/5 dark:shadow-none space-y-1 sm:space-y-2">
                                <div>
                                    <p class="text-[8px] sm:text-[10px] font-semibold uppercase tracking-wide text-gray-500 dark:text-zinc-400 mb-0.5">
                                        <span class="sm:hidden">Kredit</span>
                                        <span class="hidden sm:inline">Angsuran / kredit</span>
                                    </p>
                                    <p class="text-sm sm:text-base lg:text-lg font-bold tabular-nums text-emerald-700 dark:text-emerald-400 leading-tight break-words">
                                        @if ($vehicle->loan_price)
                                            <span class="sm:hidden">{{ format_idr_jt_mobile($vehicle->loan_price) }}</span>
                                            <span class="hidden sm:inline">Rp {{ number_format($vehicle->loan_price, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-[11px] sm:text-sm font-semibold text-gray-500 dark:text-zinc-400 leading-tight">Hubungi kami</span>
                                        @endif
                                    </p>
                                </div>
                                @if ($vehicle->display_price)
                                    <div class="flex flex-wrap items-center gap-1 sm:gap-2 pt-1 border-t border-emerald-100/80 dark:border-emerald-900/40">
                                        <span class="shrink-0 inline-flex items-center rounded bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-200 px-1 py-px sm:rounded-md sm:px-2 sm:py-0.5 text-[8px] sm:text-[10px] font-bold uppercase tracking-wide">
                                            Tunai
                                        </span>
                                        <span class="text-[10px] sm:text-xs font-semibold text-blue-700 dark:text-blue-300 tabular-nums min-w-0 leading-tight">
                                            <span class="sm:hidden">{{ format_idr_jt_mobile($vehicle->display_price) }}</span>
                                            <span class="hidden sm:inline">Rp {{ number_format($vehicle->display_price, 0, ',', '.') }}</span>
                                        </span>
                                    </div>
                                @endif
                                @if ($vehicle->minimun_credit_down_payment > 0)
                                    <div class="flex flex-wrap items-center gap-1 sm:gap-2 pt-1 sm:pt-2 border-t border-emerald-100/80 dark:border-emerald-900/40">
                                        <span class="shrink-0 inline-flex items-center rounded bg-amber-100 dark:bg-amber-950/50 text-amber-900 dark:text-amber-200 px-1 py-px sm:rounded-md sm:px-2 sm:py-0.5 text-[8px] sm:text-[10px] font-bold uppercase tracking-wide">
                                            <span class="sm:hidden">DP</span>
                                            <span class="hidden sm:inline">DP mulai</span>
                                        </span>
                                        <span class="text-[10px] sm:text-xs font-semibold text-amber-800 dark:text-amber-300 tabular-nums min-w-0 leading-tight">
                                            <span class="sm:hidden">{{ format_idr_jt_mobile($vehicle->minimun_credit_down_payment) }}</span>
                                            <span class="hidden sm:inline">Rp {{ number_format($vehicle->minimun_credit_down_payment, 0, ',', '.') }}</span>
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-auto pt-2 sm:pt-3 flex flex-col gap-1.5 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-x-3 sm:gap-y-1 text-[9px] sm:text-[11px] text-gray-500 dark:text-zinc-400 border-t border-gray-100 dark:border-zinc-700/80">
                                <div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5 sm:gap-x-2.5 min-w-0">
                                    @if ($vehicle->kilometer)
                                        <span class="inline-flex items-center gap-0.5 sm:gap-1 min-w-0">
                                            <span class="text-gray-400 dark:text-zinc-500 shrink-0" aria-hidden="true">◦</span>
                                            <span class="tabular-nums truncate">{{ number_format($vehicle->kilometer, 0, ',', '.') }} km</span>
                                        </span>
                                    @endif
                                    @if ($vehicle->color)
                                        <span class="inline-flex items-center gap-0.5 sm:gap-1 min-w-0 max-w-full sm:max-w-[10rem] truncate" title="{{ $vehicle->color }}">
                                            <span class="text-gray-400 dark:text-zinc-500 shrink-0" aria-hidden="true">◦</span>
                                            <span class="truncate">{{ $vehicle->color }}</span>
                                        </span>
                                    @endif
                                </div>
                                <time class="shrink-0 text-gray-400 dark:text-zinc-500 tabular-nums max-sm:text-[8px]" datetime="{{ $vehicle->created_at?->toIso8601String() }}">
                                    {{ $vehicle->created_at?->diffForHumans() }}
                                </time>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $vehicles->links() }}
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

