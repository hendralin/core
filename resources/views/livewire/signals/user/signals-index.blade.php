<div class="py-6">
    <div class="mb-8 space-y-2 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-50 sm:text-3xl">
            Sinyal Saham Premium
        </h1>
        <p class="max-w-2xl mx-auto text-sm text-zinc-600 dark:text-zinc-300 sm:text-base">
            Dapatkan rekomendasi sinyal saham terkurasi untuk membantu Anda mengambil keputusan trading yang lebih terukur dan terencana.
        </p>
    </div>

    @if($hasActiveSubscription)
        {{-- SECTION: Info Subscription Aktif --}}
        <div class="grid gap-4 mb-8 sm:grid-cols-2">
            <div class="p-4 border rounded-xl border-emerald-100 bg-emerald-50/60 dark:border-emerald-700/60 dark:bg-emerald-900/40">
                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center shrink-0 w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-800 dark:text-emerald-100">
                        <flux:icon name="check-badge" variant="outline" class="w-5 h-5" />
                    </div>
                    <div class="space-y-1 text-sm">
                        <p class="font-semibold text-emerald-800 dark:text-emerald-100">
                            Subscription Aktif
                        </p>
                        @if($subscription)
                            <p class="text-xs text-emerald-700/80 dark:text-emerald-200/80">
                                Berlaku sampai tanggal
                                <span class="font-medium">
                                    {{ $subscription->end_date?->format('d M Y') }}
                                </span>
                            </p>
                            <p class="text-xs text-emerald-700/80 dark:text-emerald-200/80">
                                Status pembayaran:
                                <span class="font-semibold uppercase">
                                    {{ str($subscription->payment_status)->replace('_', ' ')->title() }}
                                </span>
                            </p>
                        @else
                            <p class="text-xs text-emerald-700/80 dark:text-emerald-200/80">
                                Subscription Anda terdeteksi aktif.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-4 border rounded-xl border-zinc-200 bg-zinc-50/70 dark:border-zinc-700/70 dark:bg-zinc-900/50">
                <p class="mb-2 text-xs font-semibold tracking-wide text-zinc-500 uppercase dark:text-zinc-400">
                    Catatan Penting
                </p>
                <ul class="space-y-1 text-xs text-zinc-600 dark:text-zinc-300">
                    <li class="flex gap-2">
                        <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        <span>Sinyal bukan merupakan ajakan membeli / menjual. Gunakan sebagai bahan pertimbangan analisis pribadi.</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        <span>Selalu sesuaikan keputusan dengan profil risiko dan money management Anda.</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- SECTION: Daftar Sinyal (Published + masih valid) --}}
        <div class="p-6 border rounded-2xl border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900/70">
            <div class="flex flex-col items-start justify-between gap-4 mb-4 sm:flex-row sm:items-center">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">
                        Daftar Sinyal Saham
                    </h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Menampilkan kumpulan sinyal saham terkini yang siap digunakan sebagai bahan pertimbangan keputusan trading Anda.
                    </p>
                </div>

                <div class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-medium text-emerald-700 ring-1 ring-emerald-100 dark:bg-emerald-900/40 dark:text-emerald-100 dark:ring-emerald-800/70">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live Update
                    </span>
                    <span class="hidden text-[11px] sm:inline">
                        Terakhir diperbarui: {{ now()->format('d M Y, H:i') }} WIB
                    </span>
                </div>
            </div>

            {{-- Filters --}}
            <div class="p-4 mb-5 border rounded-xl border-zinc-200 bg-zinc-50/70 dark:border-zinc-700/70 dark:bg-zinc-900/50">
                <div class="grid gap-3 md:grid-cols-12 md:items-end">
                    <div class="md:col-span-10">
                        <flux:input
                            wire:model.live.debounce.300ms="search"
                            label="Pencarian Kode Emiten"
                            placeholder="Masukkan kode emiten..."
                            icon="magnifying-glass"
                            clearable
                        />
                    </div>

                    {{-- <div class="md:col-span-5">
                        <flux:select wire:model.live="signalType" label="Tipe Sinyal">
                            <flux:select.option value="">Semua</flux:select.option>
                            @foreach($signalTypes as $type)
                                <flux:select.option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div> --}}

                    <div class="md:col-span-2">
                        <flux:select wire:model.live="perPage" label="Tampilkan">
                            <flux:select.option value="10">10</flux:select.option>
                            <flux:select.option value="15">15</flux:select.option>
                            <flux:select.option value="25">25</flux:select.option>
                        </flux:select>
                    </div>

                    <div class="md:col-span-12 flex flex-wrap gap-2 justify-between pt-1">
                        <div class="flex items-center gap-2 text-[11px] text-zinc-500 dark:text-zinc-400">
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.chevron-up-down class="w-4 h-4" />
                                Urutkan berdasarkan:
                            </span>
                            <button
                                type="button"
                                wire:click="sortBy('kode_emiten')"
                                class="px-2 py-1 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800"
                            >
                                Emiten
                            </button>
                            <span class="ml-1 text-[10px] uppercase tracking-wide text-zinc-400">
                                ({{ $sortDirection }})
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <flux:button size="sm" variant="ghost" wire:click="clearFilters" icon="x-mark">
                                Reset
                            </flux:button>
                            <div wire:loading class="text-zinc-500 dark:text-zinc-400">
                                <flux:icon name="loading" variant="outline" class="w-4 h-4" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- List --}}
            @if($signals && $signals->count())
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($signals as $signal)
                        <div class="p-4 border rounded-2xl border-zinc-200 bg-white/70 hover:shadow-md transition dark:border-zinc-700 dark:bg-zinc-900/60">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="relative w-10 h-10 shrink-0" x-data="{ imageLoaded: false, imageError: false }" x-init="imageLoaded = false; imageError = false">
                                        <div
                                            class="absolute inset-0 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center"
                                            x-show="!imageLoaded || imageError"
                                            x-cloak
                                        >
                                            <span class="text-zinc-700 dark:text-zinc-200 font-bold text-xs">
                                                {{ substr($signal->kode_emiten, 0, 2) }}
                                            </span>
                                        </div>
                                        @if($signal->stockCompany && $signal->stockCompany->logo_url)
                                            <img
                                                src="{{ $signal->stockCompany->logo_url }}"
                                                alt="{{ $signal->kode_emiten }}"
                                                class="absolute inset-0 w-10 h-10 rounded-full object-contain bg-white dark:bg-zinc-800 p-0.5"
                                                x-show="imageLoaded && !imageError"
                                                x-cloak
                                                x-on:load="imageLoaded = true"
                                                x-on:error="imageError = true"
                                            />
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-zinc-900 dark:text-zinc-50 truncate">
                                                {{ $signal->kode_emiten }}
                                            </p>
                                            @if($signal->published_at && $signal->published_at->diffInDays(now()) < 3)
                                                <flux:badge size="sm" color="red" class="animate-pulse">
                                                    New
                                                </flux:badge>
                                            @else
                                                <flux:badge size="sm" color="green">
                                                    Sinyal Aktif
                                                </flux:badge>
                                            @endif
                                        </div>
                                        @if($signal->stockCompany)
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                                                {{ $signal->stockCompany->nama_emiten ?? '' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- <flux:badge size="sm" color="blue">
                                    {{ ucfirst(str_replace('_', ' ', $signal->signal_type)) }}
                                </flux:badge> --}}
                            </div>

                            <div class="grid grid-cols-2 gap-3 mt-4 text-[11px] text-zinc-600 dark:text-zinc-300">
                                <div>
                                    <div class="text-[10px] uppercase tracking-wide text-zinc-400">Tanggal Hit</div>
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-50">
                                        {{ $signal->hit_date ? $signal->hit_date->format('d M Y') : '-' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase tracking-wide text-zinc-400">Published</div>
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-50">
                                        {{ $signal->published_at ? $signal->published_at->format('d M Y, H:i') : '-' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase tracking-wide text-zinc-400">Market Cap</div>
                                    <div class="font-semibold">{{ $signal->formatted_market_cap }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase tracking-wide text-zinc-400">PBV / PER</div>
                                    <div class="font-semibold">{{ $signal->formatted_pbv }} · {{ $signal->formatted_per }}</div>
                                </div>
                            </div>

                            {{-- @if($signal->recommendation)
                                <div class="mt-4">
                                    <div class="text-[10px] uppercase tracking-wide text-zinc-400 mb-1">Ringkasan</div>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                                        {{ \Illuminate\Support\Str::limit($signal->recommendation, 140) }}
                                    </p>
                                </div>
                            @endif --}}
                        </div>
                    @endforeach
                </div>

                @if($signals->hasPages())
                    <div class="mt-6">
                        {{ $signals->links(data: ['scrollTo' => false]) }}
                    </div>
                @endif
            @else
                <div class="py-10 text-center border border-dashed rounded-xl border-zinc-200/80 dark:border-zinc-700/80 bg-zinc-50/60 dark:bg-zinc-900/60">
                    <div class="flex flex-col items-center justify-center px-4 space-y-3">
                        <flux:icon name="inbox" variant="outline" class="w-10 h-10 text-zinc-500 dark:text-zinc-400" />
                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">
                            Tidak ada sinyal yang ditemukan.
                        </p>
                        <p class="max-w-md text-xs text-zinc-500 dark:text-zinc-400">
                            {{-- Coba ubah filter (periode valid / tipe sinyal) atau kata kunci pencarian Anda. --}}
                            Coba ubah kata kunci pencarian Anda.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    @else
        {{-- SECTION: Belum Berlangganan --}}
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="p-6 mb-6 border rounded-2xl border-amber-200 bg-amber-50/70 dark:border-amber-700/70 dark:bg-amber-900/40">
                    <div class="flex items-start gap-3">
                        <div class="flex items-center justify-center shrink-0 w-10 h-10 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-800 dark:text-amber-100">
                            <flux:icon name="lock-closed" variant="outline" class="w-5 h-5" />
                        </div>
                        <div class="space-y-2 text-sm">
                            <p class="font-semibold text-amber-900 dark:text-amber-50">
                                Akses Sinyal Saham Terkunci
                            </p>
                            <p class="text-sm leading-relaxed text-amber-900/90 dark:text-amber-100/90">
                                Untuk mendapatkan akses ke Sinyal Saham silahkan berlangganan Paket Premium Tahunan.<br>
                                Hubungi Admin (WhatsApp 0821-1313-1800 atau
                                <a href="https://wa.me/6282113131800?text=Halo%20Admin,%20saya%20ingin%20berlangganan%20Paket%20Premium%20Tahunan%20Sinyal%20Saham." target="_blank" class="font-semibold underline decoration-dotted underline-offset-2 hover:text-emerald-700 dark:hover:text-emerald-300">
                                    KLIK DISINI
                                </a>). Terima kasih.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Contoh preview konten sinyal untuk edukasi --}}
                <div class="p-5 border rounded-2xl border-zinc-200 bg-linear-to-br from-zinc-50 via-white to-emerald-50/70 shadow-emerald-500/5 shadow-lg dark:border-zinc-700 dark:from-zinc-950 dark:via-zinc-900 dark:to-emerald-950/40">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs font-semibold tracking-wide text-emerald-700 uppercase dark:text-emerald-300">
                                Contoh Tampilan Sinyal
                            </p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                Preview ini hanya contoh, bukan rekomendasi real-time.
                            </p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-zinc-900 px-2 py-1 text-[11px] font-medium text-zinc-50 dark:bg-zinc-50 dark:text-zinc-900">
                            Demo
                        </span>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="p-3 border rounded-xl border-zinc-200/70 bg-white/60 dark:border-zinc-700/80 dark:bg-zinc-900/70">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-100">
                                        BULLISH
                                    </span>
                                    <span class="text-xs font-semibold tracking-wide text-zinc-800 uppercase dark:text-zinc-50">
                                        ABCD
                                    </span>
                                </div>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    Swing Trade
                                </span>
                            </div>
                            <dl class="grid grid-cols-2 gap-2 text-[11px] text-zinc-600 dark:text-zinc-300">
                                <div>
                                    <dt class="text-[10px] uppercase text-zinc-400">Area Beli</dt>
                                    <dd class="font-semibold text-zinc-900 dark:text-zinc-50">1.230 – 1.250</dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] uppercase text-zinc-400">Target Jual</dt>
                                    <dd class="font-semibold text-emerald-700 dark:text-emerald-300">1.320 – 1.370</dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] uppercase text-zinc-400">Stop Loss</dt>
                                    <dd class="font-semibold text-red-600 dark:text-red-400">&lt; 1.190</dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] uppercase text-zinc-400">Risk / Reward</dt>
                                    <dd class="font-semibold">1 : 2,5</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="p-3 border rounded-xl border-zinc-200/70 bg-white/60 dark:border-zinc-700/80 dark:bg-zinc-900/70">
                            <p class="mb-2 text-xs font-semibold text-zinc-800 dark:text-zinc-50">
                                Apa yang Anda dapatkan?
                            </p>
                            <ul class="space-y-1 text-xs text-zinc-600 dark:text-zinc-300">
                                <li class="flex gap-2">
                                    <flux:icon name="check" variant="outline" class="w-4 h-4 mt-0.5 text-emerald-500" />
                                    <span>Sinyal saham terkurasi harian / mingguan sesuai kondisi market.</span>
                                </li>
                                <li class="flex gap-2">
                                    <flux:icon name="check" variant="outline" class="w-4 h-4 mt-0.5 text-emerald-500" />
                                    <span>Level harga jelas: area beli, area jual bertahap, dan batas stop loss.</span>
                                </li>
                                <li class="flex gap-2">
                                    <flux:icon name="check" variant="outline" class="w-4 h-4 mt-0.5 text-emerald-500" />
                                    <span>Risk management yang membantu menjaga portofolio lebih sehat.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION: Card Harga & Benefit --}}
            <div class="lg:col-span-1">
                <div class="sticky border rounded-2xl top-4 border-zinc-200 bg-white/90 shadow-lg shadow-emerald-500/5 backdrop-blur-sm dark:border-zinc-700 dark:bg-zinc-900/90">
                    <div class="px-5 pt-5 pb-4 border-b border-zinc-200/80 dark:border-zinc-700/80">
                        <p class="text-xs font-semibold tracking-wide text-emerald-700 uppercase dark:text-emerald-300">
                            Paket Premium Tahunan
                        </p>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Akses penuh seluruh Sinyal Saham selama 1 tahun penuh.
                        </p>
                    </div>
                    <div class="px-5 pt-4 pb-5 space-y-4">
                        <div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                    IDR
                                </span>
                                <span class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-50">
                                    1.200.000
                                </span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    / tahun
                                </span>
                            </div>
                            <p class="mt-1 text-[11px] text-zinc-500 dark:text-zinc-400">
                                Kurang lebih ± Rp100.000 / bulan untuk akses penuh insight pasar.
                            </p>
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-semibold tracking-wide text-zinc-700 uppercase dark:text-zinc-200">
                                Benefit Berlangganan
                            </p>
                            <ul class="space-y-1.5 text-xs text-zinc-600 dark:text-zinc-300">
                                <li class="flex gap-2">
                                    <flux:icon name="bolt" variant="outline" class="w-4 h-4 mt-0.5 text-emerald-500" />
                                    <span>Akses penuh Sinyal Saham Premium selama 12 bulan.</span>
                                </li>
                                <li class="flex gap-2">
                                    <flux:icon name="chart-bar" variant="outline" class="w-4 h-4 mt-0.5 text-emerald-500" />
                                    <span>Update sinyal berkala sesuai momentum & kondisi market.</span>
                                </li>
                                <li class="flex gap-2">
                                    <flux:icon name="shield-check" variant="outline" class="w-4 h-4 mt-0.5 text-emerald-500" />
                                    <span>Pendekatan risk management sehingga keputusan trading lebih terukur.</span>
                                </li>
                                <li class="flex gap-2">
                                    <flux:icon name="clock" variant="outline" class="w-4 h-4 mt-0.5 text-emerald-500" />
                                    <span>Menghemat waktu screening saham sendiri setiap hari.</span>
                                </li>
                            </ul>
                        </div>

                        <div class="space-y-2">
                            <a
                                href="https://wa.me/6282113131800?text=Halo%20Admin,%20saya%20ingin%20berlangganan%20Paket%20Premium%20Tahunan%20Sinyal%20Saham."
                                target="_blank"
                                class="inline-flex items-center justify-center w-full px-4 py-2.5 text-sm font-semibold text-white transition rounded-xl bg-emerald-600 shadow-sm hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-950/0"
                            >
                                <flux:icon name="chat-bubble-left-right" variant="outline" class="w-4 h-4 mr-1.5" />
                                Hubungi Admin via WhatsApp
                            </a>
                            <p class="text-[11px] text-center text-zinc-500 dark:text-zinc-400">
                                Klik tombol di atas untuk terhubung langsung dengan Admin dan melakukan proses berlangganan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
