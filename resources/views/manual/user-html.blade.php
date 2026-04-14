{{-- Manual Pengguna — konten HTML (emerald / user-friendly). Gulir vertikal area dokumen diatur di livewire/manual/manual-page (article). --}}

{{-- Pengantar singkat --}}
<div class="not-prose mb-5 rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 via-white to-teal-50/50 p-4 shadow-sm dark:border-emerald-800/50 dark:from-emerald-950/40 dark:via-zinc-900/80 dark:to-teal-950/20 sm:p-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
        <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 dark:bg-emerald-500">
            <flux:icon.chat-bubble-left-right class="size-6" />
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">{{ __('Mulai di sini') }}</p>
            <p class="mt-1 text-sm leading-relaxed text-zinc-700 dark:text-zinc-200">
                <strong class="text-zinc-900 dark:text-white">The Broadcaster</strong>
                {{ __('mengelola WhatsApp lewat WAHA: sesi, kontak, grup, template, kirim pesan (teks/media/tautan), bulk Excel/CSV, dan jadwal otomatis.') }}
            </p>
            <div class="mt-3 flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200">WAHA</span>
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200">{{ __('Broadcast') }}</span>
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200">{{ __('Jadwal') }}</span>
            </div>
        </div>
    </div>
</div>

<h2 id="apa-itu" class="scroll-mt-24">{{ __('Apa itu The Broadcaster') }}</h2>
<p class="leading-relaxed">
    {{ __('Aplikasi ini menghubungkan akun WhatsApp bisnis Anda ke server WAHA sehingga Anda bisa mengirim pesan terjadwal, massal, dan memantau status pengiriman (pending → sent / failed) dari satu panel web.') }}
</p>
<ul class="!mt-3 space-y-1.5 !pl-0">
    @foreach ([__('Sesi & kontak tersinkron per akun'), __('Template dengan variabel @{{nama_variabel}}'), __('Kampanye bulk lewat file'), __('Zona waktu mengikuti profil Anda')] as $point)
        <li class="not-prose flex items-start gap-2.5 text-sm text-zinc-700 dark:text-zinc-300">
            <flux:icon.check-circle class="mt-0.5 size-5 shrink-0 text-emerald-500 dark:text-emerald-400" />
            <span>{{ $point }}</span>
        </li>
    @endforeach
</ul>

<h2 id="sebelum-mulai" class="scroll-mt-24">{{ __('Sebelum memulai') }}</h2>
<div class="not-prose grid gap-2 sm:grid-cols-3">
    <div class="rounded-xl border border-zinc-200 bg-zinc-50/90 p-3 dark:border-zinc-700 dark:bg-zinc-800/50">
        <flux:icon.user-circle class="mb-1.5 size-7 text-emerald-600 dark:text-emerald-400" />
        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Akun') }}</p>
        <p class="mt-1 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('Dibuat admin atau daftar sendiri jika dibuka.') }}</p>
    </div>
    <div class="rounded-xl border border-zinc-200 bg-zinc-50/90 p-3 dark:border-zinc-700 dark:bg-zinc-800/50">
        <flux:icon.computer-desktop class="mb-1.5 size-7 text-emerald-600 dark:text-emerald-400" />
        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Perangkat') }}</p>
        <p class="mt-1 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('Browser terbaru; layar lebar lebih nyaman untuk tabel dan broadcast.') }}</p>
    </div>
    <div class="rounded-xl border border-zinc-200 bg-zinc-50/90 p-3 dark:border-zinc-700 dark:bg-zinc-800/50">
        <flux:icon.server-stack class="mb-1.5 size-7 text-emerald-600 dark:text-emerald-400" />
        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Server') }}</p>
        <p class="mt-1 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('Kirim pesan & jadwal butuh antrian aktif — tanya admin jika pesan menumpuk pending.') }}</p>
    </div>
</div>

<div id="akun" class="not-prose mt-4 scroll-mt-24 group relative overflow-hidden rounded-2xl border border-emerald-200/70 bg-white shadow-sm ring-1 ring-emerald-500/10 transition hover:border-emerald-300 hover:shadow-md dark:border-emerald-900/50 dark:bg-zinc-900/85 dark:ring-emerald-500/15 dark:hover:border-emerald-700/80">
    <div class="pointer-events-none absolute -left-16 top-1/2 size-40 -translate-y-1/2 rounded-full bg-emerald-500/[0.06] blur-3xl dark:bg-emerald-400/[0.04]"></div>
    <div class="relative border-b border-emerald-500/20 bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-2.5 text-white sm:px-5">
        <div class="flex flex-wrap items-center gap-2.5">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white shadow-inner ring-1 ring-white/30">
                <flux:icon.user-circle class="size-5" />
            </span>
            <div>
                <h2 class="text-base font-bold tracking-tight sm:text-lg">{{ __('Masuk, keluar, dan pengaturan akun') }}</h2>
                <p class="text-xs font-medium text-emerald-100">{{ __('Rute umum berikut memudahkan akses cepat:') }}</p>
            </div>
        </div>
    </div>
    <div class="relative min-w-0 overflow-x-auto px-4 py-3 sm:px-5">
        <table class="w-full min-w-0 table-auto border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-emerald-200/50 bg-emerald-50/90 text-emerald-950 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-emerald-100">
                    <th class="px-3 py-2 font-semibold">{{ __('Tindakan') }}</th>
                    <th class="px-3 py-2 font-semibold">{{ __('Cara akses') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-emerald-200/60 dark:divide-emerald-800/50">
                <tr>
                    <td class="px-3 py-2 font-medium text-zinc-900 dark:text-zinc-100">Login</td>
                    <td class="px-3 py-2">
                        <kbd class="rounded-md border border-emerald-200/80 bg-emerald-50/80 px-2 py-0.5 font-mono text-xs text-zinc-800 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-zinc-200">/login</kbd>
                    </td>
                </tr>
                <tr>
                    <td class="px-3 py-2 font-medium text-zinc-900 dark:text-zinc-100">{{ __('Daftar') }}</td>
                    <td class="px-3 py-2">
                        <kbd class="rounded-md border border-emerald-200/80 bg-emerald-50/80 px-2 py-0.5 font-mono text-xs text-zinc-800 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-zinc-200">/register</kbd>
                    </td>
                </tr>
                <tr>
                    <td class="px-3 py-2 font-medium text-zinc-900 dark:text-zinc-100">{{ __('Lupa password') }}</td>
                    <td class="px-3 py-2">
                        <kbd class="rounded-md border border-emerald-200/80 bg-emerald-50/80 px-2 py-0.5 font-mono text-xs text-zinc-800 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-zinc-200">/forgot-password</kbd>
                    </td>
                </tr>
                <tr>
                    <td class="px-3 py-2 font-medium text-zinc-900 dark:text-zinc-100">{{ __('Keluar') }}</td>
                    <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">{{ __('Menu profil → Log Out') }}</td>
                </tr>
                <tr>
                    <td class="px-3 py-2 font-medium text-zinc-900 dark:text-zinc-100">Dashboard</td>
                    <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">
                        <kbd class="rounded-md border border-emerald-200/80 bg-emerald-50/80 px-2 py-0.5 font-mono text-xs text-zinc-800 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-zinc-200">/</kbd>
                        {{ __('atau') }}
                        <kbd class="rounded-md border border-emerald-200/80 bg-emerald-50/80 px-2 py-0.5 font-mono text-xs text-zinc-800 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-zinc-200">/dashboard</kbd>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="not-prose mt-3 flex gap-2.5 rounded-xl border border-emerald-200/70 bg-emerald-50/50 p-3 dark:border-emerald-800/50 dark:bg-emerald-950/25">
    <flux:icon.cog-6-tooth class="size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
    <div class="text-sm text-emerald-950 dark:text-emerald-100">
        <strong>{{ __('Settings') }}</strong>
        — {{ __('Profile (nama, email, zona waktu, avatar), Password, Appearance. Tema lewat sidebar → Theming.') }}
    </div>
</div>

<div id="navigasi" class="not-prose mt-4 scroll-mt-24 group relative overflow-hidden rounded-2xl border border-emerald-200/70 bg-white shadow-sm ring-1 ring-emerald-500/10 transition hover:border-emerald-300 hover:shadow-md dark:border-emerald-900/50 dark:bg-zinc-900/85 dark:ring-emerald-500/15 dark:hover:border-emerald-700/80">
    <div class="pointer-events-none absolute -left-16 top-1/2 size-40 -translate-y-1/2 rounded-full bg-emerald-500/[0.06] blur-3xl dark:bg-emerald-400/[0.04]"></div>
    <div class="relative border-b border-emerald-500/20 bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-2.5 text-white sm:px-5">
        <div class="flex flex-wrap items-center gap-2.5">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white shadow-inner ring-1 ring-white/30">
                <flux:icon.squares-2x2 class="size-5" />
            </span>
            <div>
                <h3 class="text-base font-bold tracking-tight sm:text-lg">{{ __('Navigasi menu') }}</h3>
                <p class="text-xs font-medium text-emerald-100">{{ __('Setiap item hanya tampil jika peran Anda punya izin (ditentukan admin).') }}</p>
            </div>
        </div>
    </div>
    <div class="relative min-w-0 overflow-x-auto px-4 py-3 sm:px-5">
        <table class="w-full min-w-0 table-auto border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-emerald-200/50 bg-emerald-50/90 text-emerald-950 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-emerald-100">
                    <th class="px-3 py-2 font-semibold">{{ __('Grup menu') }}</th>
                    <th class="px-3 py-2 font-semibold">{{ __('Isi singkat') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-emerald-200/60 dark:divide-emerald-800/50">
                <tr>
                    <td class="px-3 py-2 font-semibold text-zinc-900 dark:text-zinc-100">Dashboard</td>
                    <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">{{ __('Ringkasan aktivitas & statistik') }}</td>
                </tr>
                <tr>
                    <td class="px-3 py-2 font-semibold text-zinc-900 dark:text-zinc-100">Setup</td>
                    <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">WAHA Configuration</td>
                </tr>
                <tr>
                    <td class="px-3 py-2 font-semibold text-zinc-900 dark:text-zinc-100">List</td>
                    <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">Sessions, Contacts, Groups, Templates</td>
                </tr>
                <tr>
                    <td class="px-3 py-2 font-semibold text-zinc-900 dark:text-zinc-100">Broadcast</td>
                    <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">Messages, Schedules</td>
                </tr>
                <tr>
                    <td class="px-3 py-2 font-semibold text-zinc-900 dark:text-zinc-100">Tool</td>
                    <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">{{ __('About, API docs, manual') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<h2 id="alur-kerja" class="scroll-mt-24">{{ __('Alur kerja yang disarankan') }}</h2>
@php
    $alur = [
        ['title' => 'WAHA Configuration', 'desc' => __('URL & API Key — pastikan status terhubung')],
        ['title' => 'Sessions', 'desc' => __('Hubungkan sesi WhatsApp (QR di WAHA bila perlu)')],
        ['title' => __('Contacts / Groups'), 'desc' => __('Sinkronkan data untuk sesi yang dipakai')],
        ['title' => 'Templates', 'desc' => __('Buat template bila sering pakai format sama')],
        ['title' => 'Messages', 'desc' => __('Kirim pesan atau kampanye bulk')],
        ['title' => 'Schedules', 'desc' => __('Atur pengiriman berulang')],
        ['title' => __('Monitoring'), 'desc' => __('Pantau Dashboard & daftar pesan')],
    ];
@endphp
<ol class="not-prose !mt-3 !list-none !space-y-0 !pl-0">
    @foreach ($alur as $i => $step)
        <li class="relative flex gap-3 pb-4 last:pb-0">
            @if (! $loop->last)
                <span class="absolute left-[1.125rem] top-8 h-[calc(100%-0.25rem)] w-px bg-emerald-200 dark:bg-emerald-800/80" aria-hidden="true"></span>
            @endif
            <span class="relative z-10 flex size-8 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold text-white shadow-md dark:bg-emerald-500">{{ $i + 1 }}</span>
            <div class="min-w-0 pt-0.5">
                <p class="font-semibold text-zinc-900 dark:text-white">{{ $step['title'] }}</p>
                <p class="mt-0.5 text-sm text-zinc-600 dark:text-zinc-400">{{ $step['desc'] }}</p>
            </div>
        </li>
    @endforeach
</ol>

<h2 id="panduan-fitur" class="scroll-mt-24">{{ __('Panduan per fitur') }}</h2>
<p class="!mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Klik judul di daftar isi untuk loncat ke bagian ini.') }}</p>

<div class="not-prose mt-3 rounded-2xl border border-emerald-200/60 bg-gradient-to-br from-emerald-50/50 via-white to-teal-50/30 p-3 shadow-sm dark:border-emerald-900/40 dark:from-emerald-950/30 dark:via-zinc-900/50 dark:to-teal-950/20 sm:p-4">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm font-medium text-emerald-900 dark:text-emerald-100">{{ __('Alur menu di panel') }}</p>
        <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-emerald-800 dark:text-emerald-200">
            <span class="rounded-lg bg-white/80 px-2.5 py-1 shadow-sm ring-1 ring-emerald-200/80 dark:bg-zinc-900/80 dark:ring-emerald-800/60">{{ __('WAHA & setup') }}</span>
            <span class="text-emerald-500 dark:text-emerald-400" aria-hidden="true">→</span>
            <span class="rounded-lg bg-white/80 px-2.5 py-1 shadow-sm ring-1 ring-emerald-200/80 dark:bg-zinc-900/80 dark:ring-emerald-800/60">{{ __('Sesi & kontak') }}</span>
            <span class="text-emerald-500 dark:text-emerald-400" aria-hidden="true">→</span>
            <span class="rounded-lg bg-white/80 px-2.5 py-1 shadow-sm ring-1 ring-emerald-200/80 dark:bg-zinc-900/80 dark:ring-emerald-800/60">{{ __('Pesan & jadwal') }}</span>
        </div>
    </div>
</div>

<div class="not-prose mt-4 grid gap-3 sm:grid-cols-2">
    {{-- WAHA --}}
    <div id="fitur-waha" class="scroll-mt-24 group relative overflow-hidden rounded-2xl border border-zinc-200/90 bg-white shadow-sm ring-1 ring-zinc-950/[0.04] transition hover:border-emerald-200 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900/85 dark:ring-white/[0.06] dark:hover:border-emerald-800/80">
        <div class="pointer-events-none absolute -right-10 -top-10 size-24 rounded-full bg-emerald-500/[0.07] blur-2xl transition group-hover:bg-emerald-500/[0.12] dark:bg-emerald-400/[0.05]"></div>
        <div class="relative border-b border-emerald-500/15 bg-gradient-to-r from-emerald-600/[0.08] to-teal-600/[0.06] px-4 py-2.5 dark:from-emerald-500/10 dark:to-teal-600/10 sm:px-5">
            <div class="flex items-center gap-2.5">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20">
                    <flux:icon.cog-6-tooth class="size-5" />
                </span>
                <h3 class="text-base font-bold tracking-tight text-zinc-900 dark:text-white">WAHA Configuration</h3>
            </div>
        </div>
        <div class="relative px-4 py-3">
            <ul class="space-y-1.5">
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Isi URL API (https://…) dan API Key, lalu simpan.') }}</span>
                </li>
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Tanpa ini banyak fitur akan memperingatkan atau menolak.') }}</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Sessions --}}
    <div id="fitur-sessions" class="scroll-mt-24 group relative overflow-hidden rounded-2xl border border-zinc-200/90 bg-white shadow-sm ring-1 ring-zinc-950/[0.04] transition hover:border-emerald-200 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900/85 dark:ring-white/[0.06] dark:hover:border-emerald-800/80">
        <div class="pointer-events-none absolute -right-10 -top-10 size-24 rounded-full bg-emerald-500/[0.07] blur-2xl transition group-hover:bg-emerald-500/[0.12] dark:bg-emerald-400/[0.05]"></div>
        <div class="relative border-b border-emerald-500/15 bg-gradient-to-r from-emerald-600/[0.08] to-teal-600/[0.06] px-4 py-2.5 dark:from-emerald-500/10 dark:to-teal-600/10 sm:px-5">
            <div class="flex items-center gap-2.5">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20">
                    <flux:icon.device-tablet class="size-5" />
                </span>
                <h3 class="text-base font-bold tracking-tight text-zinc-900 dark:text-white">Sessions</h3>
            </div>
        </div>
        <div class="relative px-4 py-3">
            <ul class="space-y-1.5">
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Data WAHA digabung dengan sesi di akun Anda.') }}</span>
                </li>
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Buat / ubah / hapus sesuai izin; hapus sesi juga memanggil API WAHA.') }}</span>
                </li>
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Tombol clear cache jika perlu memuat ulang.') }}</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Contacts --}}
    <div id="fitur-contacts" class="scroll-mt-24 group relative overflow-hidden rounded-2xl border border-zinc-200/90 bg-white shadow-sm ring-1 ring-zinc-950/[0.04] transition hover:border-emerald-200 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900/85 dark:ring-white/[0.06] dark:hover:border-emerald-800/80">
        <div class="pointer-events-none absolute -right-10 -top-10 size-24 rounded-full bg-emerald-500/[0.07] blur-2xl transition group-hover:bg-emerald-500/[0.12] dark:bg-emerald-400/[0.05]"></div>
        <div class="relative border-b border-emerald-500/15 bg-gradient-to-r from-emerald-600/[0.08] to-teal-600/[0.06] px-4 py-2.5 dark:from-emerald-500/10 dark:to-teal-600/10 sm:px-5">
            <div class="flex items-center gap-2.5">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20">
                    <flux:icon.user-group class="size-5" />
                </span>
                <h3 class="text-base font-bold tracking-tight text-zinc-900 dark:text-white">{{ __('Contacts & Groups') }}</h3>
            </div>
        </div>
        <div class="relative px-4 py-3">
            <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('Sinkron dari WhatsApp (butuh izin sync). Filter per sesi; buka detail untuk info lengkap.') }}</p>
        </div>
    </div>

    {{-- Templates --}}
    <div id="fitur-templates" class="scroll-mt-24 group relative overflow-hidden rounded-2xl border border-zinc-200/90 bg-white shadow-sm ring-1 ring-zinc-950/[0.04] transition hover:border-emerald-200 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900/85 dark:ring-white/[0.06] dark:hover:border-emerald-800/80">
        <div class="pointer-events-none absolute -right-10 -top-10 size-24 rounded-full bg-emerald-500/[0.07] blur-2xl transition group-hover:bg-emerald-500/[0.12] dark:bg-emerald-400/[0.05]"></div>
        <div class="relative border-b border-emerald-500/15 bg-gradient-to-r from-emerald-600/[0.08] to-teal-600/[0.06] px-4 py-2.5 dark:from-emerald-500/10 dark:to-teal-600/10 sm:px-5">
            <div class="flex items-center gap-2.5">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20">
                    <flux:icon.document-text class="size-5" />
                </span>
                <h3 class="text-base font-bold tracking-tight text-zinc-900 dark:text-white">Templates</h3>
            </div>
        </div>
        <div class="relative px-4 py-3">
            <ul class="space-y-1.5">
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Nama: huruf kecil + garis bawah, contoh:') }} <code class="rounded-md bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">promo_bulan_ini</code></span>
                </li>
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Header opsional (maks. 60 karakter); body wajib (maks. 1024).') }}</span>
                </li>
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Variabel:') }} <code class="rounded-md bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">@{{nama_variabel}}</code></span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Schedules --}}
    <div id="fitur-schedules" class="scroll-mt-24 group relative overflow-hidden rounded-2xl border border-zinc-200/90 bg-white shadow-sm ring-1 ring-zinc-950/[0.04] transition hover:border-emerald-200 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900/85 dark:ring-white/[0.06] dark:hover:border-emerald-800/80">
        <div class="pointer-events-none absolute -right-10 -top-10 size-24 rounded-full bg-emerald-500/[0.07] blur-2xl transition group-hover:bg-emerald-500/[0.12] dark:bg-emerald-400/[0.05]"></div>
        <div class="relative border-b border-emerald-500/15 bg-gradient-to-r from-emerald-600/[0.08] to-teal-600/[0.06] px-4 py-2.5 dark:from-emerald-500/10 dark:to-teal-600/10 sm:px-5">
            <div class="flex items-center gap-2.5">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20">
                    <flux:icon.clock class="size-5" />
                </span>
                <h3 class="text-base font-bold tracking-tight text-zinc-900 dark:text-white">Schedules</h3>
            </div>
        </div>
        <div class="relative px-4 py-3">
            <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('Atur sesi, pesan, penerima, frekuensi harian/mingguan/bulanan, dan waktu. Jadwal otomatis membutuhkan cron & antrian di server — hubungi admin bila tidak pernah jalan.') }}</p>
        </div>
    </div>

    {{-- Messages: full width --}}
    <div id="fitur-messages" class="scroll-mt-24 group relative overflow-hidden rounded-2xl border border-emerald-200/70 bg-white shadow-sm ring-1 ring-emerald-500/10 transition hover:border-emerald-300 hover:shadow-md dark:border-emerald-900/50 dark:bg-zinc-900/85 dark:ring-emerald-500/15 dark:hover:border-emerald-700/80 sm:col-span-2">
        <div class="pointer-events-none absolute -left-16 top-1/2 size-40 -translate-y-1/2 rounded-full bg-emerald-500/[0.06] blur-3xl dark:bg-emerald-400/[0.04]"></div>
        <div class="relative border-b border-emerald-500/20 bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-2.5 text-white sm:px-5">
            <div class="flex flex-wrap items-center gap-2.5">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white shadow-inner ring-1 ring-white/30">
                    <flux:icon.chat-bubble-left-ellipsis class="size-5" />
                </span>
                <div>
                    <h3 class="text-base font-bold tracking-tight sm:text-lg">Messages</h3>
                    <p class="text-xs font-medium text-emerald-100">{{ __('Pusat broadcast & status pengiriman') }}</p>
                </div>
            </div>
        </div>
        <div class="relative px-4 py-3 sm:px-5">
            <ul class="grid gap-2 sm:grid-cols-2 sm:gap-x-3 sm:gap-y-2">
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Pastikan WAHA terhubung sebelum kirim besar.') }}</span>
                </li>
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Pilih sesi di modal; kontak & grup mengikuti sesi.') }}</span>
                </li>
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Direct: teks, gambar, file, custom (pratinjau tautan).') }}</span>
                </li>
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Template: pilih template aktif milik Anda.') }}</span>
                </li>
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400 sm:col-span-2">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Penerima: nomor, contact, group, atau bulk — unduh template di aplikasi.') }}</span>
                </li>
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Jadwal sekali mengikuti zona waktu profil.') }}</span>
                </li>
                <li class="flex gap-2 text-sm leading-snug text-zinc-600 dark:text-zinc-400">
                    <flux:icon.check-circle class="mt-0.5 size-4 shrink-0 text-emerald-500 dark:text-emerald-400" />
                    <span>{{ __('Kirim ulang hanya untuk status failed.') }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<div id="status-pesan" class="not-prose mt-4 scroll-mt-24 group relative overflow-hidden rounded-2xl border border-emerald-200/70 bg-white shadow-sm ring-1 ring-emerald-500/10 transition hover:border-emerald-300 hover:shadow-md dark:border-emerald-900/50 dark:bg-zinc-900/85 dark:ring-emerald-500/15 dark:hover:border-emerald-700/80">
    <div class="pointer-events-none absolute -left-16 top-1/2 size-40 -translate-y-1/2 rounded-full bg-emerald-500/[0.06] blur-3xl dark:bg-emerald-400/[0.04]"></div>
    <div class="relative border-b border-emerald-500/20 bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-2.5 text-white sm:px-5">
        <div class="flex flex-wrap items-center gap-2.5">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white shadow-inner ring-1 ring-white/30">
                <flux:icon.queue-list class="size-5" />
            </span>
            <div>
                <h3 class="text-base font-bold tracking-tight sm:text-lg">{{ __('Status pesan & jadwal') }}</h3>
                <p class="text-xs font-medium text-emerald-100">{{ __('Memahami label status membantu Anda tahu langkah berikutnya.') }}</p>
            </div>
        </div>
    </div>
    <div class="relative min-w-0 overflow-x-auto px-4 py-3 sm:px-5">
        <table class="w-full min-w-0 table-auto border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-emerald-200/50 bg-emerald-50/90 text-emerald-950 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-emerald-100">
                    <th class="px-3 py-2 font-semibold">{{ __('Status / situasi') }}</th>
                    <th class="px-3 py-2 font-semibold">{{ __('Arti & tindakan') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-emerald-200/60 dark:divide-emerald-800/50">
                <tr>
                    <td class="px-3 py-2 align-top">
                        <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-900 dark:bg-amber-900/50 dark:text-amber-200">pending</span>
                    </td>
                    <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">{{ __('Masih dalam antrian. Jika lama, tanyakan admin tentang queue worker.') }}</td>
                </tr>
                <tr>
                    <td class="px-3 py-2 align-top">
                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-900 dark:bg-emerald-900/50 dark:text-emerald-200">sent</span>
                    </td>
                    <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">{{ __('Berhasil terkirim.') }}</td>
                </tr>
                <tr>
                    <td class="px-3 py-2 align-top">
                        <span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-900 dark:bg-red-900/40 dark:text-red-200">failed</span>
                    </td>
                    <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">{{ __('Gagal — coba kirim ulang; periksa nomor & koneksi WAHA.') }}</td>
                </tr>
                <tr>
                    <td class="px-3 py-2 align-top text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Jadwal tidak jalan') }}</td>
                    <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">{{ __('Hubungi admin — biasanya cron atau antrian belum berjalan di server.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<h2 id="faq-user" class="scroll-mt-24">{{ __('FAQ singkat') }}</h2>
<dl class="not-prose mt-2 grid gap-2 sm:grid-cols-2 sm:gap-3">
    <div class="rounded-xl border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/60">
        <dt class="flex items-start gap-2 font-semibold text-zinc-900 dark:text-white">
            <flux:icon.question-mark-circle class="mt-0.5 size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
            {{ __('Template tidak muncul saat kirim?') }}
        </dt>
        <dd class="mt-1.5 border-l-2 border-emerald-200 pl-3 text-sm leading-snug text-zinc-600 dark:border-emerald-800 dark:text-zinc-400">{{ __('Pastikan template aktif, dibuat oleh akun Anda, dan sesi dipilih dengan benar.') }}</dd>
    </div>
    <div class="rounded-xl border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/60">
        <dt class="flex items-start gap-2 font-semibold text-zinc-900 dark:text-white">
            <flux:icon.question-mark-circle class="mt-0.5 size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
            {{ __('Bulk upload ditolak?') }}
        </dt>
        <dd class="mt-1.5 border-l-2 border-emerald-200 pl-3 text-sm leading-snug text-zinc-600 dark:border-emerald-800 dark:text-zinc-400">{{ __('Periksa format Excel/CSV, ukuran file, dan isi setiap baris.') }}</dd>
    </div>
    <div class="rounded-xl border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/60">
        <dt class="flex items-start gap-2 font-semibold text-zinc-900 dark:text-white">
            <flux:icon.question-mark-circle class="mt-0.5 size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
            {{ __('Pratinjau tautan (custom) gagal?') }}
        </dt>
        <dd class="mt-1.5 border-l-2 border-emerald-200 pl-3 text-sm leading-snug text-zinc-600 dark:border-emerald-800 dark:text-zinc-400">{{ __('URL di teks pesan harus sama persis dengan Preview URL.') }}</dd>
    </div>
    <div class="rounded-xl border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/60">
        <dt class="flex items-start gap-2 font-semibold text-zinc-900 dark:text-white">
            <flux:icon.question-mark-circle class="mt-0.5 size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
            {{ __('Pesan tidak pernah terkirim?') }}
        </dt>
        <dd class="mt-1.5 border-l-2 border-emerald-200 pl-3 text-sm leading-snug text-zinc-600 dark:border-emerald-800 dark:text-zinc-400">{{ __('Cek status WAHA; jika tetap pending hubungi admin untuk queue worker.') }}</dd>
    </div>
</dl>
