{{-- Manual Administrator — konten HTML (violet / infrastruktur & kebijakan) --}}

<div class="not-prose mb-8 rounded-2xl border border-violet-200/80 bg-gradient-to-br from-violet-50 via-white to-indigo-50/50 p-5 shadow-sm dark:border-violet-800/50 dark:from-violet-950/40 dark:via-zinc-900/80 dark:to-indigo-950/20 sm:p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
        <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-violet-600 text-white shadow-lg shadow-violet-600/25 dark:bg-violet-500">
            <flux:icon.server-stack class="size-6" />
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold uppercase tracking-wide text-violet-700 dark:text-violet-300">{{ __('Untuk tim IT') }}</p>
            <p class="mt-1 text-sm leading-relaxed text-zinc-700 dark:text-zinc-200">
                {{ __('Dokumen ini merangkum deploy server, antrian, jadwal, WAHA, pengguna & peran, serta backup. Operator harian memakai') }}
                <strong class="text-zinc-900 dark:text-white">{{ __('Manual Pengguna') }}</strong>.
            </p>
            <div class="mt-3 flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-medium text-violet-900 dark:bg-violet-900/60 dark:text-violet-200">{{ __('Deploy') }}</span>
                <span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-medium text-violet-900 dark:bg-violet-900/60 dark:text-violet-200">{{ __('Queue & cron') }}</span>
                <span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-medium text-violet-900 dark:bg-violet-900/60 dark:text-violet-200">{{ __('Keamanan') }}</span>
            </div>
        </div>
    </div>
</div>

<h2 id="ringkasan-peran" class="scroll-mt-24">{{ __('Ringkasan peran admin') }}</h2>
<p class="leading-relaxed">
    {{ __('Dokumen ini untuk tim IT / administrator yang men-deploy server, mengoperasikan antrian dan jadwal, mengelola pengguna & peran, serta backup. Pengguna akhir merujuk ke Manual Pengguna.') }}
</p>
<ul class="!mt-4 space-y-2 !pl-0">
    @foreach ([
        __('Menyiapkan server aplikasi (PHP, database, web server), build aset, dan file .env'),
        __('Memastikan WAHA dapat dijangkau dari host Laravel (jaringan, TLS)'),
        __('Menjalankan queue worker (antrian messages) agar pesan tidak tertahan pending'),
        __('Mengaktifkan cron untuk php artisan schedule:run agar jadwal otomatis berjalan'),
        __('Mengelola Users dan Roles serta izin Spatie Permission'),
        __('Mengonfigurasi backup dan restore bila diperlukan'),
        __('Menangani isu lisensi jika fitur tersebut dipakai'),
    ] as $point)
        <li class="not-prose flex items-start gap-2.5 text-sm text-zinc-700 dark:text-zinc-300">
            <flux:icon.check-circle class="mt-0.5 size-5 shrink-0 text-violet-500 dark:text-violet-400" />
            <span>{{ $point }}</span>
        </li>
    @endforeach
</ul>
<div class="not-prose mt-5 flex gap-3 rounded-xl border border-violet-200/70 bg-violet-50/50 p-4 dark:border-violet-800/50 dark:bg-violet-950/25">
    <flux:icon.information-circle class="size-5 shrink-0 text-violet-600 dark:text-violet-400" />
    <p class="text-sm text-violet-950 dark:text-violet-100">
        {{ __('Konfigurasi URL dan API Key WAHA disimpan per pengguna di database (halaman /waha). Admin memastikan infrastruktur dan kebijakan akses aman.') }}
    </p>
</div>

<div class="not-prose my-8 grid gap-3 sm:grid-cols-3">
    <div class="rounded-xl border border-zinc-200 bg-zinc-50/90 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
        <flux:icon.queue-list class="mb-2 size-8 text-violet-600 dark:text-violet-400" />
        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Antrian') }}</p>
        <p class="mt-1 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('Worker queue messages wajib di produksi agar broadcast tidak mengendap.') }}</p>
    </div>
    <div class="rounded-xl border border-zinc-200 bg-zinc-50/90 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
        <flux:icon.clock class="mb-2 size-8 text-violet-600 dark:text-violet-400" />
        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Jadwal') }}</p>
        <p class="mt-1 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('Cron setiap menit memanggil schedule:run untuk schedule:process dan tugas terkait.') }}</p>
    </div>
    <div class="rounded-xl border border-zinc-200 bg-zinc-50/90 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
        <flux:icon.user-group class="mb-2 size-8 text-violet-600 dark:text-violet-400" />
        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Akses') }}</p>
        <p class="mt-1 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('Roles & permissions menentukan menu yang terlihat; selaraskan dengan route aplikasi.') }}</p>
    </div>
</div>

<h2 id="prasyarat" class="scroll-mt-24">{{ __('Prasyarat infrastruktur') }}</h2>
<div class="not-prose overflow-hidden rounded-2xl border border-zinc-200 shadow-sm dark:border-zinc-600">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
                <th class="px-4 py-3.5 font-semibold">{{ __('Komponen') }}</th>
                <th class="px-4 py-3.5 font-semibold">{{ __('Keterangan') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900/60">
            <tr>
                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">PHP</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">8.2+</td>
            </tr>
            <tr>
                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">Laravel</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">12 ({{ __('acuan:') }} composer.json)</td>
            </tr>
            <tr>
                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ __('Database') }}</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">MySQL, PostgreSQL, {{ __('atau') }} SQLite</td>
            </tr>
            <tr>
                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">Node.js</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ __('Untuk build front-end (Vite)') }}</td>
            </tr>
            <tr>
                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">WAHA</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ __('Instance terpisah yang dapat diakses dari server aplikasi') }}</td>
            </tr>
            <tr>
                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ __('Proses latar belakang') }}</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ __('Worker antrian + pemanggilan scheduler setiap menit') }}</td>
            </tr>
        </tbody>
    </table>
</div>

<h2 id="instalasi" class="scroll-mt-24">{{ __('Instalasi aplikasi') }}</h2>
@php
    $instalasi = [
        ['kind' => 'text', 'text' => __('Clone repositori dan masuk ke folder proyek')],
        ['kind' => 'sh', 'cmd' => 'composer install'],
        ['kind' => 'sh', 'cmd' => 'npm install'],
        ['kind' => 'sh', 'cmd' => 'cp .env.example .env', 'note' => __('sesuaikan APP_URL, APP_TIMEZONE, database')],
        ['kind' => 'sh', 'cmd' => 'php artisan key:generate'],
        ['kind' => 'sh', 'cmd' => 'php artisan migrate', 'note' => __('dan db:seed bila perlu')],
        ['kind' => 'sh', 'cmd' => 'npm run build', 'note' => __('produksi')],
        ['kind' => 'text', 'text' => __('Layani dengan php artisan serve atau Nginx/Apache')],
    ];
@endphp
<ol class="not-prose !mt-4 !list-none !space-y-0 !pl-0">
    @foreach ($instalasi as $i => $step)
        <li class="relative flex gap-4 pb-6 last:pb-0">
            @if (! $loop->last)
                <span class="absolute left-[1.125rem] top-10 h-[calc(100%-0.5rem)] w-px bg-violet-200 dark:bg-violet-800/80" aria-hidden="true"></span>
            @endif
            <span class="relative z-10 flex size-9 shrink-0 items-center justify-center rounded-full bg-violet-600 text-sm font-bold text-white shadow-md dark:bg-violet-500">{{ $i + 1 }}</span>
            <div class="min-w-0 pt-0.5 text-sm text-zinc-700 dark:text-zinc-300">
                @if ($step['kind'] === 'text')
                    {{ $step['text'] }}
                @else
                    <code class="rounded-md border border-zinc-300 bg-zinc-100 px-2 py-1 font-mono text-xs text-zinc-900 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">{{ $step['cmd'] }}</code>
                    @if (! empty($step['note']))
                        <span class="mt-1 block text-zinc-600 dark:text-zinc-400">— {{ $step['note'] }}</span>
                    @endif
                @endif
            </div>
        </li>
    @endforeach
</ol>

<div class="not-prose mt-6 flex gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800/60 dark:bg-amber-950/40">
    <flux:icon.exclamation-triangle class="size-5 shrink-0 text-amber-600 dark:text-amber-400" />
    <div class="text-sm text-amber-950 dark:text-amber-100">
        <strong>{{ __('Keamanan') }}:</strong>
        {{ __('Nilai contoh superadmin dan perusahaan di .env.example hanya untuk pengembangan — ganti di produksi.') }}
    </div>
</div>

<h2 id="waha-lingkungan" class="scroll-mt-24">{{ __('Konfigurasi WAHA di lingkungan') }}</h2>
<div class="not-prose mb-4 flex gap-3 rounded-xl border border-violet-200/70 bg-violet-50/50 p-4 dark:border-violet-800/50 dark:bg-violet-950/25">
    <flux:icon.cog-6-tooth class="size-5 shrink-0 text-violet-600 dark:text-violet-400" />
    <ul class="space-y-2 text-sm text-violet-950 dark:text-violet-100">
        <li class="flex gap-2"><span class="text-violet-500">•</span>{{ __('Runtime memuat URL dan API Key dari database (Config per user), diisi lewat Setup → WAHA Configuration (/waha)') }}</li>
        <li class="flex gap-2"><span class="text-violet-500">•</span>{{ __('Pastikan endpoint WAHA dapat dijangkau dari server Laravel (DNS, firewall, HTTPS)') }}</li>
        <li class="flex gap-2"><span class="text-violet-500">•</span>{{ __('Sumber kebenaran koneksi API di kode adalah konfigurasi per user di DB (WahaService / HasWahaConfig), bukan hanya variabel WAHA_* di .env') }}</li>
    </ul>
</div>

<h2 id="queue-worker" class="scroll-mt-24">{{ __('Queue worker') }}</h2>
<p class="leading-relaxed">{{ __('Pengiriman pesan memakai antrian Laravel, queue messages. Tanpa worker, pesan tetap pending.') }}</p>
<div class="not-prose mt-3 space-y-3">
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="border-b border-zinc-200 bg-zinc-100 px-4 py-2 text-xs font-medium text-zinc-600 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">{{ __('Pengembangan') }}</div>
        <pre class="overflow-x-auto bg-zinc-50 p-4 text-sm dark:bg-zinc-950"><code>php artisan queue:listen --queue=messages</code></pre>
    </div>
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="border-b border-zinc-200 bg-zinc-100 px-4 py-2 text-xs font-medium text-zinc-600 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">{{ __('Produksi (contoh)') }}</div>
        <pre class="overflow-x-auto bg-zinc-50 p-4 text-sm dark:bg-zinc-950"><code>php artisan queue:work --queue=messages --tries=3 --timeout=120</code></pre>
    </div>
</div>
<p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Pantau kegagalan: php artisan queue:failed; retry sesuai kebutuhan.') }}</p>

<h2 id="scheduler-cron" class="scroll-mt-24">{{ __('Laravel Scheduler dan cron') }}</h2>
<p class="leading-relaxed">{{ __('Di routes/console.php terdaftar schedule:process (setiap menit) untuk jadwal pesan, serta perintah terkait lisensi.') }}</p>
<div class="not-prose mt-3 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
    <div class="border-b border-zinc-200 bg-zinc-100 px-4 py-2 text-xs font-medium text-zinc-600 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">crontab</div>
    <pre class="overflow-x-auto bg-zinc-50 p-4 text-sm dark:bg-zinc-950"><code>* * * * * cd /path/ke/proyek && php artisan schedule:run >> /dev/null 2>&1</code></pre>
</div>
<p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Di Windows gunakan Task Scheduler dengan pemanggilan setara schedule:run. Detail: dokumentasi SCHEDULE_USAGE di folder docs.') }}</p>

<h2 id="users-roles" class="scroll-mt-24">{{ __('Pengguna, peran, dan izin') }}</h2>
<ul class="!mt-3 space-y-2">
    <li class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ __('Menu Access Control: Users (/users), Roles (/roles)') }}</li>
    <li class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ __('Izin didefinisikan di PermissionSeeder: company.*, waha.*, session.*, contact.*, group.*, template.*, message.*, schedule.*, backup-restore.*, dll.') }}</li>
</ul>
<div class="not-prose mt-4 flex gap-3 rounded-xl border border-violet-300 bg-violet-50 p-4 dark:border-violet-800 dark:bg-violet-950/40">
    <flux:icon.exclamation-triangle class="size-5 shrink-0 text-violet-700 dark:text-violet-300" />
    <div class="text-sm text-violet-950 dark:text-violet-100">
        <strong>{{ __('Penting') }}:</strong>
        {{ __('Route Messages memakai message.view|message.send|message.audit, sedangkan seed default memuat message.create. Jika menu tidak muncul, tambahkan izin message.send ke permissions dan peran, atau selaraskan route dengan seed.') }}
    </div>
</div>

<h2 id="backup-restore" class="scroll-mt-24">{{ __('Backup dan restore') }}</h2>
<div class="not-prose rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/50">
    <div class="flex items-start gap-3">
        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/50">
            <flux:icon.archive-box class="size-5 text-violet-700 dark:text-violet-300" />
        </span>
        <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
            <li>{{ __('Tool → Backup and Restore (/backup-restore) — bergantung pada Spatie Laravel Backup') }}</li>
            <li>{{ __('Izin contoh: backup-restore.view, backup-restore.create, download, restore, delete') }}</li>
            <li>{{ __('Jadwal backup otomatis dapat diatur dari UI sesuai fitur yang tersedia') }}</li>
        </ul>
    </div>
</div>

<h2 id="lisensi" class="scroll-mt-24">{{ __('Lisensi') }}</h2>
<p class="leading-relaxed">{{ __('Jika lisensi perusahaan kedaluwarsa, pengguna dapat diarahkan ke /license-expired. Kebijakan perpanjangan di luar cakupan teknis dokumen ini.') }}</p>

<h2 id="troubleshooting" class="scroll-mt-24">{{ __('Pemantauan dan troubleshooting server') }}</h2>
<div class="not-prose overflow-hidden rounded-2xl border border-zinc-200 shadow-sm dark:border-zinc-600">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
                <th class="px-4 py-3.5 font-semibold">{{ __('Gejala') }}</th>
                <th class="px-4 py-3.5 font-semibold">{{ __('Tindakan admin') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900/60">
            <tr>
                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ __('Banyak pesan pending') }}</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ __('Pastikan worker queue messages berjalan; periksa log dan queue:failed') }}</td>
            </tr>
            <tr>
                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ __('Jadwal tidak jalan') }}</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ __('Pastikan cron schedule:run setiap menit dan worker antrian aktif') }}</td>
            </tr>
            <tr>
                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ __('Error koneksi WAHA') }}</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ __('Uji konektivitas ke URL WAHA; periksa TLS dan API key (diisi user di /waha)') }}</td>
            </tr>
            <tr>
                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ __('Beban tinggi') }}</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ __('Sesuaikan jumlah worker, timeout, dan infrastruktur WAHA') }}</td>
            </tr>
        </tbody>
    </table>
</div>

<h2 id="faq-admin" class="scroll-mt-24">{{ __('FAQ administrator') }}</h2>
<div class="not-prose space-y-3">
    <div class="border-l-4 border-violet-500 bg-zinc-50/90 py-3 pl-4 dark:bg-zinc-800/40">
        <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Versi Laravel di README beda dengan composer.json?') }}</p>
        <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-300">{{ __('Ikuti composer.json (proyek ini Laravel 12).') }}</p>
    </div>
    <div class="border-l-4 border-violet-500 bg-zinc-50/90 py-3 pl-4 dark:bg-zinc-800/40">
        <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Cukup set WAHA_* di .env?') }}</p>
        <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-300">{{ __('Aplikasi memuat kredensial dari database per user; pastikan pengguna dengan hak WAHA mengisi /waha.') }}</p>
    </div>
    <div class="border-l-4 border-violet-500 bg-zinc-50/90 py-3 pl-4 dark:bg-zinc-800/40">
        <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Bagaimana memberi akses menu Messages?') }}</p>
        <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-300">{{ __('Pastikan peran memiliki message.view, message.send, atau message.audit sesuai route; tambahkan message.send jika belum ada di seed.') }}</p>
    </div>
</div>

<h2 id="referensi" class="scroll-mt-24">{{ __('Referensi') }}</h2>
<div class="not-prose overflow-hidden rounded-2xl border border-zinc-200 shadow-sm dark:border-zinc-600">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
                <th class="px-4 py-3.5 font-semibold">{{ __('Dokumen') }}</th>
                <th class="px-4 py-3.5 font-semibold">{{ __('Isi') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900/60">
            <tr>
                <td class="px-4 py-3 font-mono text-xs text-zinc-900 dark:text-zinc-100">README.md</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ __('Instalasi panjang, contoh environment') }}</td>
            </tr>
            <tr>
                <td class="px-4 py-3 font-mono text-xs text-zinc-900 dark:text-zinc-100">docs/SCHEDULE_USAGE.md</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ __('Scheduler, schedule:process, cron') }}</td>
            </tr>
            <tr>
                <td class="px-4 py-3 font-mono text-xs text-zinc-900 dark:text-zinc-100">API_DOCUMENTATION.md</td>
                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ __('API HTTP untuk integrasi sistem') }}</td>
            </tr>
        </tbody>
    </table>
</div>
<ul class="!mt-4 space-y-2 !pl-0 text-sm text-zinc-600 dark:text-zinc-400">
    <li class="not-prose flex items-start gap-2">
        <flux:icon.document-text class="mt-0.5 size-4 shrink-0 text-zinc-400" />
        <span>{{ __('Sesuaikan dengan kebijakan TI organisasi Anda.') }}</span>
    </li>
</ul>
