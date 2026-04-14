<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Analytics') }}</flux:heading>
        <flux:subheading size="lg" class="mb-2">{{ __('Traffic publik, engagement katalog, dan aktivitas internal') }}</flux:subheading>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $periodLabel }}</p>
        <flux:separator variant="subtle" class="mt-4" />
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-4 mb-8 lg:flex-row lg:flex-wrap lg:items-end">
        <div class="flex flex-wrap gap-2">
            <flux:button wire:click="$set('rangePreset', '7d')" variant="{{ $rangePreset === '7d' ? 'primary' : 'ghost' }}" size="sm">{{ __('7 hari') }}</flux:button>
            <flux:button wire:click="$set('rangePreset', '30d')" variant="{{ $rangePreset === '30d' ? 'primary' : 'ghost' }}" size="sm">{{ __('30 hari') }}</flux:button>
            <flux:button wire:click="$set('rangePreset', '90d')" variant="{{ $rangePreset === '90d' ? 'primary' : 'ghost' }}" size="sm">{{ __('90 hari') }}</flux:button>
        </div>
        <div class="flex flex-wrap gap-2 items-end">
            <flux:field>
                <flux:label>{{ __('Dari') }}</flux:label>
                <flux:input type="date" wire:model.live="dateFrom" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Sampai') }}</flux:label>
                <flux:input type="date" wire:model.live="dateTo" />
            </flux:field>
        </div>
    </div>

    {{-- KPI --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="rounded-xl bg-white p-4 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <p class="text-sm text-zinc-500">{{ __('Total kunjungan (log)') }}</p>
            <p class="text-2xl font-bold text-zinc-900 dark:text-white tabular-nums">{{ number_format($visitSummary['total']) }}</p>
            <p class="text-xs text-zinc-400 mt-1">{{ __('Publik') }}: {{ number_format($visitSummary['public_total']) }} · {{ __('Internal') }}: {{ number_format($visitSummary['internal_total']) }}</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <p class="text-sm text-zinc-500">{{ __('Unik IP (publik)') }}</p>
            <p class="text-2xl font-bold text-zinc-900 dark:text-white tabular-nums">{{ number_format($visitSummary['unique_ips_public']) }}</p>
            <p class="text-xs text-zinc-400 mt-1">{{ __('Estimasi online (publik, ~5 mnt)') }}: {{ number_format($visitSummary['online_public_estimate']) }}</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <p class="text-sm text-zinc-500">{{ __('Unik IP (semua)') }}</p>
            <p class="text-2xl font-bold text-zinc-900 dark:text-white tabular-nums">{{ number_format($visitSummary['unique_ips_all']) }}</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <p class="text-sm text-zinc-500">{{ __('Staff online (~3 mnt)') }}</p>
            <p class="text-2xl font-bold text-zinc-900 dark:text-white tabular-nums">{{ number_format($internal['online_staff']) }}</p>
            <p class="text-xs text-zinc-400 mt-1">{{ __('Login (periode)') }}: {{ number_format($internal['login_events']) }}</p>
        </div>
    </div>

    {{-- Charts row 1 --}}
    <div class="grid gap-6 lg:grid-cols-2 mb-8">
        <div class="rounded-xl bg-white p-6 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <flux:heading size="lg" class="mb-4">{{ __('Kunjungan harian (publik)') }}</flux:heading>
            <div class="h-80">
                <canvas id="chartDailyPublic"></canvas>
            </div>
        </div>
        <div class="rounded-xl bg-white p-6 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <flux:heading size="lg" class="mb-4">{{ __('Kunjungan harian (semua)') }}</flux:heading>
            <div class="h-80">
                <canvas id="chartDailyAll"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts row 2 --}}
    <div class="grid gap-6 lg:grid-cols-3 mb-8">
        <div class="rounded-xl bg-white p-6 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <flux:heading size="lg" class="mb-4">{{ __('Kategori perangkat (publik)') }}</flux:heading>
            <div class="h-64">
                <canvas id="chartDevices"></canvas>
            </div>
        </div>
        <div class="rounded-xl bg-white p-6 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <flux:heading size="lg" class="mb-4">{{ __('Browser (publik)') }}</flux:heading>
            <div class="h-64">
                <canvas id="chartBrowsers"></canvas>
            </div>
        </div>
        <div class="rounded-xl bg-white p-6 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <flux:heading size="lg" class="mb-4">{{ __('Login harian (internal)') }}</flux:heading>
            <div class="h-64">
                <canvas id="chartLogins"></canvas>
            </div>
        </div>
    </div>

    {{-- Engagement + internal --}}
    <div class="grid gap-6 lg:grid-cols-2 mb-8">
        <div class="rounded-xl bg-white p-6 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <flux:heading size="lg" class="mb-4">{{ __('Engagement katalog (kumulatif)') }}</flux:heading>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-3">
                    <p class="text-zinc-500">{{ __('Halaman detail') }}</p>
                    <p class="text-xl font-semibold tabular-nums">{{ number_format($engagement['page_views']) }}</p>
                </div>
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-3">
                    <p class="text-zinc-500">{{ __('Chat WA') }}</p>
                    <p class="text-xl font-semibold tabular-nums">{{ number_format($engagement['chat_whatsapp']) }}</p>
                </div>
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-3">
                    <p class="text-zinc-500">{{ __('Share WA') }}</p>
                    <p class="text-xl font-semibold tabular-nums">{{ number_format($engagement['share_whatsapp']) }}</p>
                </div>
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-3">
                    <p class="text-zinc-500">{{ __('Salin link') }}</p>
                    <p class="text-xl font-semibold tabular-nums">{{ number_format($engagement['link_copy']) }}</p>
                </div>
            </div>
            <p class="text-xs text-zinc-400 mt-4">{{ __('Angka di atas dari counter per kendaraan; bukan dibatasi rentang tanggal di atas.') }}</p>
        </div>
        <div class="rounded-xl bg-white p-6 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <flux:heading size="lg" class="mb-4">{{ __('Ringkasan aktivitas internal') }}</flux:heading>
            <ul class="space-y-2 text-sm">
                <li class="flex justify-between gap-4"><span class="text-zinc-500">{{ __('Total event activity log') }}</span><span class="font-semibold tabular-nums">{{ number_format($internal['total_activities']) }}</span></li>
                <li class="flex justify-between gap-4"><span class="text-zinc-500">{{ __('Event login') }}</span><span class="font-semibold tabular-nums">{{ number_format($internal['login_events']) }}</span></li>
            </ul>
        </div>
    </div>

    {{-- Tables --}}
    <div class="grid gap-6 lg:grid-cols-2 mb-8">
        <div class="rounded-xl bg-white p-6 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 overflow-x-auto">
            <flux:heading size="lg" class="mb-4">{{ __('URL teratas (publik)') }}</flux:heading>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-zinc-500 border-b border-zinc-200 dark:border-zinc-700">
                        <th class="pb-2 pr-2">{{ __('URL') }}</th>
                        <th class="pb-2 text-right">{{ __('Hit') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topUrls as $row)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800">
                            <td class="py-2 pr-2 max-w-md truncate" title="{{ $row['url'] }}">{{ $row['url'] }}</td>
                            <td class="py-2 text-right tabular-nums">{{ number_format($row['count']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="py-4 text-zinc-500">{{ __('Belum ada data kunjungan di periode ini.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="rounded-xl bg-white p-6 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 overflow-x-auto">
            <flux:heading size="lg" class="mb-4">{{ __('Kendaraan paling banyak dikunjungi') }}</flux:heading>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-zinc-500 border-b border-zinc-200 dark:border-zinc-700">
                        <th class="pb-2 pr-2">{{ __('Kendaraan') }}</th>
                        <th class="pb-2 text-right">{{ __('Hit') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topVehicles as $row)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800">
                            <td class="py-2 pr-2">{{ $row['label'] }}</td>
                            <td class="py-2 text-right tabular-nums">{{ number_format($row['visits']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="py-4 text-zinc-500">{{ __('Belum ada kunjungan ke halaman detail kendaraan di periode ini.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-xl bg-white p-6 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 overflow-x-auto mb-8">
        <flux:heading size="lg" class="mb-4">{{ __('Pengguna aktif (activity log)') }}</flux:heading>
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-zinc-500 border-b border-zinc-200 dark:border-zinc-700">
                    <th class="pb-2 pr-2">{{ __('Nama') }}</th>
                    <th class="pb-2 text-right">{{ __('Event') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topUsers as $row)
                    <tr class="border-b border-zinc-100 dark:border-zinc-800">
                        <td class="py-2 pr-2">{{ $row['name'] }}</td>
                        <td class="py-2 text-right tabular-nums">{{ number_format($row['count']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="py-4 text-zinc-500">{{ __('Belum ada aktivitas internal di periode ini.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Platform breakdown --}}
    <div class="rounded-xl bg-white p-6 shadow border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 mb-8">
        <flux:heading size="lg" class="mb-4">{{ __('Platform OS (publik)') }}</flux:heading>
        <div class="flex flex-wrap gap-2">
            @foreach ($platforms as $p)
                <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 dark:bg-zinc-700 px-3 py-1 text-sm">
                    <span class="font-medium">{{ $p['label'] }}</span>
                    <span class="text-zinc-500 tabular-nums">{{ number_format($p['count']) }}</span>
                </span>
            @endforeach
            @if (count($platforms) === 0)
                <p class="text-sm text-zinc-500">{{ __('Belum ada data.') }}</p>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            function destroyIfExists(ref) {
                if (ref && ref.destroy) ref.destroy();
            }

            function initCharts() {
                if (typeof Chart === 'undefined') return;

                const dailyPublic = @json($dailyPublic);
                const dailyAll = @json($dailyAll);
                const devices = @json($devices);
                const browsers = @json($browsers);
                const dailyLogins = @json($dailyLogins);

                const lineOpts = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                        x: { grid: { display: false } }
                    }
                };

                const el1 = document.getElementById('chartDailyPublic');
                if (el1) {
                    destroyIfExists(window.__chartDailyPublic);
                    window.__chartDailyPublic = new Chart(el1.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: dailyPublic.map(r => r.day),
                            datasets: [{
                                label: 'Hits',
                                data: dailyPublic.map(r => r.count),
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: lineOpts
                    });
                }

                const el2 = document.getElementById('chartDailyAll');
                if (el2) {
                    destroyIfExists(window.__chartDailyAll);
                    window.__chartDailyAll = new Chart(el2.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: dailyAll.map(r => r.day),
                            datasets: [{
                                label: 'Hits',
                                data: dailyAll.map(r => r.count),
                                borderColor: 'rgb(16, 185, 129)',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: lineOpts
                    });
                }

                const el3 = document.getElementById('chartDevices');
                if (el3 && devices.length) {
                    destroyIfExists(window.__chartDevices);
                    window.__chartDevices = new Chart(el3.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: devices.map(d => d.label),
                            datasets: [{
                                data: devices.map(d => d.count),
                                backgroundColor: [
                                    'rgba(59, 130, 246, 0.8)',
                                    'rgba(16, 185, 129, 0.8)',
                                    'rgba(249, 115, 22, 0.8)',
                                    'rgba(139, 92, 246, 0.8)',
                                ]
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }

                const el4 = document.getElementById('chartBrowsers');
                if (el4 && browsers.length) {
                    destroyIfExists(window.__chartBrowsers);
                    window.__chartBrowsers = new Chart(el4.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: browsers.map(d => d.label),
                            datasets: [{
                                data: browsers.map(d => d.count),
                                backgroundColor: [
                                    'rgba(59, 130, 246, 0.8)',
                                    'rgba(16, 185, 129, 0.8)',
                                    'rgba(249, 115, 22, 0.8)',
                                    'rgba(139, 92, 246, 0.8)',
                                    'rgba(236, 72, 153, 0.8)',
                                    'rgba(234, 179, 8, 0.8)',
                                ]
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }

                const el5 = document.getElementById('chartLogins');
                if (el5) {
                    destroyIfExists(window.__chartLogins);
                    window.__chartLogins = new Chart(el5.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: dailyLogins.map(r => r.day),
                            datasets: [{
                                label: 'Logins',
                                data: dailyLogins.map(r => r.count),
                                backgroundColor: 'rgba(139, 92, 246, 0.7)',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0 } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            }

            document.addEventListener('DOMContentLoaded', () => setTimeout(initCharts, 150));
            document.addEventListener('livewire:navigated', () => setTimeout(initCharts, 150));
            document.addEventListener('livewire:updated', () => setTimeout(initCharts, 150));
        })();
    </script>
    @endpush
</div>
