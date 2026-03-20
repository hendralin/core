<div>
@php
    $vehicleShareContext = [
        'title' => trim(($vehicle->brand?->name ?? '-') . ' ' . ($vehicle->type?->name ?? '-') . ' ' . ($vehicle->year ?? '')),
        'price' => $vehicle->display_price ? ('Rp ' . number_format($vehicle->display_price, 0, ',', '.')) : null,
        'loan_price' => $vehicle->loan_price ? ('Rp ' . number_format($vehicle->loan_price, 0, ',', '.')) : null,
        'km' => $vehicle->kilometer ? (number_format($vehicle->kilometer, 0, ',', '.') . ' km') : null,
        'color' => $vehicle->color ?: null,
    ];
@endphp
<script type="application/json" id="woto-vehicle-share-context">
@json($vehicleShareContext)
</script>
<div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back link -->
        <div class="mb-4">
            <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center text-sm text-gray-600 dark:text-zinc-300 hover:text-blue-600 dark:hover:text-blue-400">
                <flux:icon.chevron-left class="w-4 h-4 mr-1" />
                Kembali ke katalog
            </a>
        </div>

        <!-- Header -->
        <div class="mb-6">
            <flux:heading size="xl" level="1" class="mb-1">
                {{ $vehicle->brand?->name }} {{ $vehicle->type?->name }} {{ $vehicle->vehicle_model?->name }} {{ $vehicle->year }}
            </flux:heading>
            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">
                Warna: {{ $vehicle->color ?? '-' }} • KM: {{ $vehicle->kilometer ? number_format($vehicle->kilometer, 0, ',', '.') . ' km' : '-' }}
            </flux:text>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Gallery + description -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Gallery -->
                @php
                    $galleryImages = $vehicle->images
                        ->map(fn($img) => asset('photos/vehicles/' . $img->image))
                        ->values();
                    $mainImageUrl = $galleryImages->first();
                @endphp

                <div
                    class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl overflow-hidden"
                    x-data="{
                        images: @js($galleryImages),
                        activeIndex: 0,
                        setActive(i) { this.activeIndex = i; },
                        get activeSrc() { return this.images?.[this.activeIndex] ?? null; },
                    }"
                >
                    <button
                        type="button"
                        class="relative aspect-video w-full bg-gray-100 dark:bg-zinc-900 flex items-center justify-center group"
                        :disabled="!activeSrc"
                        @click="activeSrc && openPublicGallery(activeIndex)"
                    >
                        <template x-if="activeSrc">
                            <img
                                :src="activeSrc"
                                alt="{{ $vehicle->brand?->name }} {{ $vehicle->type?->name }} {{ $vehicle->vehicle_model?->name }} {{ $vehicle->year }}"
                                class="w-full h-full object-contain"
                            >
                        </template>

                        <template x-if="!activeSrc">
                            <div class="flex flex-col items-center justify-center text-gray-400 dark:text-zinc-500">
                                <flux:icon.photo class="w-12 h-12 mb-2" />
                                <span class="text-sm">Belum ada foto kendaraan</span>
                            </div>
                        </template>

                        <template x-if="activeSrc">
                            <div>
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                                <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-2">
                                    <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-black/60 text-white backdrop-blur">
                                        {{ $galleryImages->count() }} foto
                                    </div>
                                    <div class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white/80 dark:bg-zinc-900/80 text-gray-900 dark:text-zinc-100 backdrop-blur border border-gray-200/70 dark:border-zinc-700/70">
                                        Klik untuk perbesar
                                    </div>
                                </div>
                            </div>
                        </template>
                    </button>

                    @if ($galleryImages->count() > 1)
                        <div class="border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-900 px-3 py-2">
                            <div class="flex gap-2 overflow-x-auto">
                                @foreach ($galleryImages as $url)
                                    <button
                                        type="button"
                                        class="shrink-0 w-20 h-16 rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 hover:ring-2 hover:ring-blue-500 transition cursor-pointer"
                                        @click="setActive({{ $loop->index }})"
                                        :class="activeIndex === {{ $loop->index }} ? 'ring-2 ring-blue-500' : ''"
                                        aria-label="Buka foto {{ $loop->iteration }}"
                                    >
                                        <img
                                            src="{{ $url }}"
                                            alt="Foto {{ $loop->iteration }}"
                                            class="w-full h-full object-cover"
                                        >
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Deskripsi -->
                @if ($vehicle->description)
                    <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-5">
                        <flux:heading size="md" class="mb-3">
                            Deskripsi Kendaraan
                        </flux:heading>
                        <div class="prose prose-sm dark:prose-invert max-w-none">
                            @php
                                $allowed = "<p><b><i><u><br><a><strong><em><ul><ol><li><span><div>";
                                $description = strip_tags($vehicle->description, $allowed);
                            @endphp
                            {!! $description !!}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right: Info & contact -->
            <div class="space-y-4">
                <!-- Price card -->
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-5 space-y-3">
                    <flux:heading size="xl" class="mb-1 text-green-600 dark:text-green-400 font-bold">
                        @if ($vehicle->loan_price)
                            <span class="sm:hidden">{{ format_idr_jt_mobile($vehicle->loan_price) }}</span>
                            <span class="hidden sm:inline">Rp {{ number_format($vehicle->loan_price, 0, ',', '.') }}</span>
                        @else
                            Harga belum tersedia
                        @endif
                    </flux:heading>

                    @if ($vehicle->display_price)
                        <flux:text class="text-sm text-blue-600 dark:text-blue-400">
                            <span class="sm:hidden">Estimasi harga cash: {{ format_idr_jt_mobile($vehicle->display_price) }}</span>
                            <span class="hidden sm:inline">Estimasi harga cash: Rp {{ number_format($vehicle->display_price, 0, ',', '.') }}</span>
                        </flux:text>
                    @endif

                    @if ($vehicle->minimun_credit_down_payment)
                        <flux:text class="text-sm text-amber-700 dark:text-amber-400">
                            <span class="sm:hidden">DP minimal kredit: {{ format_idr_jt_mobile($vehicle->minimun_credit_down_payment) }}</span>
                            <span class="hidden sm:inline">DP minimal kredit: Rp {{ number_format($vehicle->minimun_credit_down_payment, 0, ',', '.') }}</span>
                        </flux:text>
                    @endif

                    <div class="text-xs text-gray-500 dark:text-zinc-400">
                        Iklan dibuat {{ $vehicle->created_at?->diffForHumans() }}
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row gap-2">
                        <flux:button
                            variant="primary"
                            size="sm"
                            icon="paper-airplane"
                            class="cursor-pointer w-full sm:w-auto"
                            wire:click="incrementWhatsAppShare"
                            onclick="shareVehicleToWhatsApp()"
                        >
                            Share WhatsApp
                        </flux:button>
                        <flux:button
                            variant="ghost"
                            size="sm"
                            icon="link"
                            class="cursor-pointer w-full sm:w-auto"
                            wire:click="incrementLinkCopy"
                            onclick="copyVehicleLink()"
                        >
                            Salin Link
                        </flux:button>
                    </div>

                    @if (($vehicle->whatsapp_share_count ?? 0) > 0 || ($vehicle->link_copy_count ?? 0) > 0)
                        <div class="mt-2 text-[11px] text-gray-500 dark:text-zinc-400">
                            @if (($vehicle->whatsapp_share_count ?? 0) > 0)
                                Dibagikan ke WhatsApp {{ number_format($vehicle->whatsapp_share_count) }} kali
                            @endif
                            @if (($vehicle->link_copy_count ?? 0) > 0)
                                @if (($vehicle->whatsapp_share_count ?? 0) > 0)
                                    ·
                                @endif
                                Link disalin {{ number_format($vehicle->link_copy_count) }} kali
                            @endif
                        </div>
                    @endif

                    <div class="mt-3 text-[11px] text-gray-500 dark:text-zinc-400">
                        Tips: kirim link ini ke keluarga/teman untuk diskusi, atau chat kami untuk info lebih lanjut.
                    </div>
                </div>

                <!-- Specs -->
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-5 space-y-3">
                    <flux:heading size="md">
                        Spesifikasi Utama
                    </flux:heading>

                    <dl class="grid grid-cols-1 gap-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-zinc-400">Merek</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $vehicle->brand?->name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-zinc-400">Tipe</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $vehicle->type?->name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-zinc-400">Model</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $vehicle->vehicle_model?->name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-zinc-400">Tahun</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $vehicle->year ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-zinc-400">Kapasitas Silinder</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">
                                {{ $vehicle->cylinder_capacity ? number_format($vehicle->cylinder_capacity, 0, ',', '.') . ' cc' : '-' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-zinc-400">Bahan Bakar</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $vehicle->fuel_type ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-zinc-400">Warna</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $vehicle->color ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-zinc-400">Kilometer</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">
                                {{ $vehicle->kilometer ? number_format($vehicle->kilometer, 0, ',', '.') . ' km' : '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Registration info -->
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-5 space-y-3">
                    <flux:heading size="md">
                        Informasi Registrasi
                    </flux:heading>

                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-zinc-400">Tgl. Registrasi</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">
                                {{ $vehicle->vehicle_registration_date ? \Carbon\Carbon::parse($vehicle->vehicle_registration_date)->format('d-m-Y') : '-' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-zinc-400">Tgl. STNK Habis</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">
                                {{ $vehicle->vehicle_registration_expiry_date ? \Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->format('d-m-Y') : '-' }}
                            </dd>
                        </div>
                        {{-- <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-zinc-400">Lokasi Gudang</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $vehicle->warehouse?->name ?? '-' }}</dd>
                        </div> --}}
                    </dl>
                </div>

                <!-- Contact box -->
                @php
                    $marketingWhatsApp = config('marketing.whatsapp_contacts', []);
                    $waMeNumber = static fn (string $local): string => '62' . ltrim(preg_replace('/\D/', '', $local), '0');
                    $vehicleTitle = trim(($vehicle->brand?->name ?? '') . ' ' . ($vehicle->type?->name ?? '') . ' ' . ($vehicle->year ?? ''));
                @endphp

                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <flux:heading size="md">
                                Hubungi Kami
                            </flux:heading>
                            <flux:text class="mt-1 text-sm text-gray-600 dark:text-zinc-300">
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $vehicleTitle ?: '-' }}</span>
                            </flux:text>
                        </div>
                        <flux:badge icon="bolt" size="sm" color="teal">Fast response</flux:badge>
                    </div>

                    <div class="mt-4 space-y-2">
                        <flux:text class="text-xs text-gray-500 dark:text-zinc-400">
                            Marketing (WhatsApp)
                        </flux:text>
                        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2">
                            @foreach ($marketingWhatsApp as $contact)
                                <flux:button
                                    variant="primary"
                                    size="sm"
                                    icon="chat-bubble-left-right"
                                    class="cursor-pointer w-full sm:w-auto justify-center sm:justify-start"
                                    title="{{ $contact['phone'] }}"
                                    wire:click="incrementChatWhatsApp"
                                    onclick="chatVehicleToWhatsApp('{{ $waMeNumber($contact['phone']) }}')"
                                >
                                    {{ $contact['name'] }}
                                </flux:button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Public Lightbox -->
<div id="public-gallery-data" class="hidden" data-images='@json($galleryImages)'></div>
<div id="public-gallery-modal" class="fixed inset-0 hidden z-50">
    <div class="absolute inset-0 bg-black/90" onclick="closePublicGallery()"></div>

    <div class="relative h-full w-full flex items-center justify-center p-3 sm:p-6">
        <div class="relative w-full max-w-6xl">
            <div class="absolute -top-10 left-0 text-white text-xs sm:text-sm">
                <span id="public-gallery-index">1</span> / <span id="public-gallery-total">1</span>
            </div>
            <button
                type="button"
                onclick="closePublicGallery()"
                class="absolute -top-12 right-0 text-white/90 hover:text-white transition"
                aria-label="Tutup"
            >
                <flux:icon.x-mark class="w-7 h-7" />
            </button>

            <div class="relative bg-white/5 rounded-xl overflow-hidden border border-white/10">
                <button
                    id="public-gallery-prev"
                    type="button"
                    onclick="navigatePublicGallery(-1)"
                    class="absolute left-2 top-1/2 -translate-y-1/2 hidden sm:flex items-center justify-center w-10 h-10 rounded-full bg-black/40 hover:bg-black/60 text-white transition"
                    aria-label="Sebelumnya"
                >
                    <flux:icon.chevron-left class="w-6 h-6" />
                </button>
                <button
                    id="public-gallery-next"
                    type="button"
                    onclick="navigatePublicGallery(1)"
                    class="absolute right-2 top-1/2 -translate-y-1/2 hidden sm:flex items-center justify-center w-10 h-10 rounded-full bg-black/40 hover:bg-black/60 text-white transition"
                    aria-label="Berikutnya"
                >
                    <flux:icon.chevron-right class="w-6 h-6" />
                </button>

                <div class="bg-black/20">
                    <img id="public-gallery-image" src="" alt="" class="w-full h-[72vh] object-contain select-none">
                </div>

                <div id="public-gallery-thumbs-wrap" class="border-t border-white/10 bg-black/30 px-3 py-3">
                    <div id="public-gallery-thumbs" class="flex gap-2 overflow-x-auto"></div>
                </div>
            </div>

            <div class="mt-3 flex items-center justify-between text-white/80 text-xs">
                <div class="hidden sm:block">Tekan <span class="font-semibold">Esc</span> untuk menutup • <span class="font-semibold">←/→</span> untuk navigasi</div>
                <div class="sm:hidden">Swipe kiri/kanan untuk ganti foto</div>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        if (window.__wotoPublicGallery) return;

        window.__wotoPublicGallery = {
            images: [],
            index: 0,
            touchStartX: null,

            refreshImages() {
                const dataEl = document.getElementById('public-gallery-data');
                if (!dataEl) {
                    this.images = [];
                    return;
                }

                try {
                    this.images = JSON.parse(dataEl.dataset.images || '[]') || [];
                } catch (e) {
                    this.images = [];
                }
            },

            ensureModalListeners(modalEl) {
                if (!modalEl || modalEl.dataset.listenersAttached === '1') return;
                modalEl.dataset.listenersAttached = '1';

                modalEl.addEventListener('touchstart', (e) => {
                    if (e.touches && e.touches.length === 1) {
                        this.touchStartX = e.touches[0].clientX;
                    }
                }, { passive: true });

                modalEl.addEventListener('touchend', (e) => {
                    if (this.touchStartX === null) return;
                    const touchEndX = (e.changedTouches && e.changedTouches.length) ? e.changedTouches[0].clientX : null;
                    if (touchEndX === null) return;

                    const diff = touchEndX - this.touchStartX;
                    this.touchStartX = null;
                    if (Math.abs(diff) < 40) return;
                    this.navigate(diff > 0 ? -1 : 1);
                });
            },

            open(index) {
                this.refreshImages();
                if (!this.images || this.images.length === 0) return;
                this.index = Math.max(0, Math.min(index, this.images.length - 1));

                const modal = document.getElementById('public-gallery-modal');
                this.ensureModalListeners(modal);
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                document.getElementById('public-gallery-total').textContent = this.images.length;
                this.renderThumbs();
                this.update();
                this.toggleNav();
            },

            close() {
                const modal = document.getElementById('public-gallery-modal');
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            },

            navigate(dir) {
                this.index += dir;
                if (this.index < 0) this.index = this.images.length - 1;
                if (this.index >= this.images.length) this.index = 0;
                this.update();
            },

            jump(index) {
                this.index = index;
                this.update();
            },

            update() {
                const img = document.getElementById('public-gallery-image');
                img.src = this.images[this.index];
                document.getElementById('public-gallery-index').textContent = this.index + 1;

                const thumbs = document.querySelectorAll('#public-gallery-thumbs > button');
                thumbs.forEach((el, idx) => {
                    if (idx === this.index) {
                        el.classList.add('ring-2', 'ring-blue-400');
                        el.classList.remove('opacity-70');
                        el.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                    } else {
                        el.classList.remove('ring-2', 'ring-blue-400');
                        el.classList.add('opacity-70');
                    }
                });
            },

            toggleNav() {
                const show = this.images.length > 1;
                document.getElementById('public-gallery-prev').classList.toggle('hidden', !show);
                document.getElementById('public-gallery-next').classList.toggle('hidden', !show);
                document.getElementById('public-gallery-thumbs-wrap').classList.toggle('hidden', !show);
            },

            renderThumbs() {
                const wrap = document.getElementById('public-gallery-thumbs');
                wrap.innerHTML = '';
                this.images.forEach((src, idx) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'shrink-0 w-16 h-12 rounded-lg overflow-hidden border border-white/10 bg-black/20 hover:bg-black/30 transition';
                    btn.onclick = () => this.jump(idx);

                    const image = document.createElement('img');
                    image.src = src;
                    image.alt = `Thumbnail ${idx + 1}`;
                    image.className = 'w-full h-full object-cover';
                    btn.appendChild(image);

                    wrap.appendChild(btn);
                });
            },
        };

        window.openPublicGallery = (index) => window.__wotoPublicGallery.open(index);
        window.closePublicGallery = () => window.__wotoPublicGallery.close();
        window.navigatePublicGallery = (dir) => window.__wotoPublicGallery.navigate(dir);

        document.addEventListener('keydown', (e) => {
            const modal = document.getElementById('public-gallery-modal');
            if (!modal || modal.classList.contains('hidden')) return;

            if (e.key === 'Escape') window.__wotoPublicGallery.close();
            if (e.key === 'ArrowLeft') window.__wotoPublicGallery.navigate(-1);
            if (e.key === 'ArrowRight') window.__wotoPublicGallery.navigate(1);
        });
    })();
</script>

<script>
    (() => {
        window.__wotoPublicShare = {
            readContext() {
                const el = document.getElementById('woto-vehicle-share-context');
                if (!el) {
                    return {};
                }
                try {
                    return JSON.parse(el.textContent.trim() || '{}') || {};
                } catch (e) {
                    return {};
                }
            },

            async copyLink() {
                const url = window.location.href;
                try {
                    await navigator.clipboard.writeText(url);
                    return true;
                } catch (e) {
                    const input = document.createElement('input');
                    input.value = url;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                    return true;
                }
            },

            toWhatsApp() {
                const ctx = this.readContext();
                const url = window.location.href;
                const lines = [];
                lines.push('Halo kak, cek dulu nih mobil kece yang lagi kami jual!');
                lines.push('');
                lines.push(ctx.title || '');
                if (ctx.loan_price) lines.push(`Harga Kredit: ${ctx.loan_price}`);
                if (ctx.price) lines.push(`Harga Tunai: ${ctx.price}`);
                if (ctx.km) lines.push(`KM: ${ctx.km}`);
                if (ctx.color) lines.push(`Warna: ${ctx.color}`);
                lines.push('');
                lines.push('Klik linknya di bawah, jangan sampe kehabisan!');
                lines.push(url);

                const message = lines.join('\n');
                const waUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
                window.open(waUrl, '_blank', 'noopener');
            },
        };

        window.copyVehicleLink = async () => {
            const ok = await window.__wotoPublicShare.copyLink();
            if (ok) alert('Link berhasil disalin.');
        };

        window.shareVehicleToWhatsApp = () => {
            window.__wotoPublicShare.toWhatsApp();
        };

        window.chatVehicleToWhatsApp = (phoneNumber) => {
            const ctx = window.__wotoPublicShare.readContext();
            const url = window.location.href;
            const lines = [
                'Halo kak, saya ingin menanyakan tentang kendaraan yang tersedia:',
                ctx.title || '',
            ];
            if (ctx.loan_price) lines.push(`Harga Kredit: ${ctx.loan_price}`);
            if (ctx.price) lines.push(`Harga Tunai: ${ctx.price}`);
            lines.push('', 'Link:', url);
            const message = lines.join('\n');

            const waUrl = `https://wa.me/${encodeURIComponent(phoneNumber)}?text=${encodeURIComponent(message)}`;
            window.open(waUrl, '_blank', 'noopener');
        };
    })();
</script>
</div>

