<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Detail Penggajian') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ $salary->employee?->name ?? '-' }}
            <span class="text-gray-400 dark:text-zinc-500 mx-1">•</span>
            {{ $salary->salary_date ? $salary->salary_date->format('F Y') : '-' }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex flex-wrap items-center gap-2 mb-4">
        <flux:button variant="primary" size="sm" href="{{ route('salaries.index') }}" wire:navigate icon="arrow-uturn-left">Kembali</flux:button>

        @can('salary.edit')
            <flux:button variant="ghost" size="sm" href="{{ route('salaries.edit', $salary) }}" wire:navigate icon="pencil-square">Edit</flux:button>
        @endcan
        @can('salary.print')
            <flux:button variant="ghost" size="sm" href="{{ route('salaries.print', $salary) }}" target="_blank" icon="printer">Cetak Slip Gaji</flux:button>
        @endcan
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @php
        $details = $salary->salaryDetails ?? collect();
        $insentifDetails = $details->filter(fn($d) => $d->vehicle);
        $regularDetails = $details->reject(fn($d) => $d->vehicle);
        $componentCount = $details->count();
        $insentifCount = $insentifDetails->count();
        $baseComponents = $salary->employee?->employeeSalaryComponents ?? collect();
        $fixedTotal = 0;
        $variableTotal = 0;
        foreach ($details as $d) {
            $esc = $baseComponents->firstWhere('salary_component_id', $d->salary_component_id);
            $isQuantitative = $esc ? (bool) $esc->is_quantitative : false;
            if ($isQuantitative) {
                $variableTotal += $d->total_amount;
            } else {
                $fixedTotal += $d->total_amount;
            }
        }
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <!-- Info Karyawan + Ringkasan -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="md" class="mb-4">Info Karyawan</flux:heading>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-zinc-400">Nama</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $salary->employee?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-zinc-400">Jabatan</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $salary->employee?->position?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-zinc-400">Periode Gaji</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            {{ $salary->salary_date ? $salary->salary_date->format('d F Y') : '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-zinc-400">Jumlah Komponen</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $componentCount }}</dd>
                    </div>
                </dl>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4">
                    <div class="text-xs uppercase text-emerald-700 dark:text-emerald-400 font-semibold tracking-wide mb-1">Total Gaji</div>
                    <div class="text-lg font-bold text-emerald-900 dark:text-emerald-100">
                        Rp {{ number_format($totalSalary ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="text-xs uppercase text-blue-700 dark:text-blue-400 font-semibold tracking-wide mb-1">Komponen Tetap</div>
                    <div class="text-sm font-semibold text-blue-900 dark:text-blue-100">
                        Rp {{ number_format($fixedTotal, 0, ',', '.') }}
                    </div>
                </div>
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                    <div class="text-xs uppercase text-amber-700 dark:text-amber-400 font-semibold tracking-wide mb-1">Komponen Variabel</div>
                    <div class="text-sm font-semibold text-amber-900 dark:text-amber-100">
                        Rp {{ number_format($variableTotal, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Insentif & Penjualan (Marketing) -->
        <div class="space-y-3">
            @if(isset($marketingSalesSummaryForPeriod) && $marketingSalesSummaryForPeriod && (int) ($salary->employee?->position_id ?? 0) === 1)
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-blue-200 dark:border-blue-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <flux:heading size="sm">Penjualan Mobil Periode Ini</flux:heading>
                        <p class="text-xs text-gray-500 dark:text-zinc-400">
                            {{ $salary->salary_date ? $salary->salary_date->format('F Y') : '-' }}
                        </p>
                    </div>
                    <div class="shrink-0">
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                            <flux:icon.currency-dollar class="w-4 h-4 text-blue-600 dark:text-blue-300" />
                        </div>
                    </div>
                </div>

                <div class="space-y-2 mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Unit Terjual</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ number_format($marketingSalesSummaryForPeriod['vehicles_sold_in_period'] ?? 0, 0, ',', '.') }} unit
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Total Nilai Penjualan</span>
                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                            Rp {{ number_format($marketingSalesSummaryForPeriod['total_sales_in_period'] ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-zinc-400 uppercase tracking-wide">Rata-rata Harga Jual</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            Rp {{ number_format($marketingSalesSummaryForPeriod['average_selling_price_in_period'] ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @if(isset($marketingSalesForPeriod) && $marketingSalesForPeriod->isNotEmpty())
                <div class="border-t border-gray-200 dark:border-zinc-700 pt-2 mt-2">
                    <p class="text-[11px] font-semibold text-gray-500 dark:text-zinc-400 mb-1 uppercase tracking-wide">
                        Detail Unit Terjual
                    </p>
                    <ul class="space-y-1.5 text-xs text-gray-700 dark:text-zinc-300 max-h-40 overflow-y-auto">
                        @foreach($marketingSalesForPeriod as $sale)
                            @php
                                $vehicleLabel = trim(implode(' ', array_filter([
                                    $sale->brand?->name,
                                    $sale->vehicle_model?->name,
                                    $sale->year,
                                ])));
                            @endphp
                            <li class="flex justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="truncate font-medium">
                                        <a href="{{ route('vehicles.show', $sale->id) }}" wire:navigate class="hover:underline">
                                            {{ $sale->police_number ?? 'N/A' }}
                                        </a>
                                    </p>
                                    <p class="truncate text-[11px] text-gray-500 dark:text-zinc-400">
                                        {{ $vehicleLabel }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($sale->selling_price ?? 0, 0, ',', '.') }}
                                    </p>
                                    @if($sale->selling_date)
                                        <p class="text-[10px] text-gray-500 dark:text-zinc-400">
                                            {{ \Carbon\Carbon::parse($sale->selling_date)->format('d M') }}
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @else
                <p class="text-xs text-gray-500 dark:text-zinc-400">
                    Tidak ada kendaraan yang terjual oleh karyawan ini pada periode gaji ini.
                </p>
                @endif
            </div>
            @endif

            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <flux:heading size="sm">Ringkasan Insentif</flux:heading>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                        {{ $insentifCount }} baris
                    </span>
                </div>
                @if($insentifDetails->isNotEmpty())
                    <ul class="space-y-1 text-xs text-gray-700 dark:text-zinc-300 max-h-40 overflow-y-auto">
                        @foreach($insentifDetails as $d)
                            @php
                                $vehicleLabel = trim(implode(' ', array_filter([
                                    $d->vehicle?->brand?->name,
                                    $d->vehicle?->vehicle_model?->name,
                                    $d->vehicle?->year,
                                    $d->vehicle?->police_number,
                                ])));
                            @endphp
                            <li class="flex justify-between gap-2">
                                <span class="truncate">
                                    {{ $d->salaryComponent?->name ?? 'Insentif' }}
                                    @if($vehicleLabel)
                                        – {{ $vehicleLabel }}
                                    @endif
                                </span>
                                <span class="whitespace-nowrap font-medium">
                                    Rp {{ number_format($d->total_amount ?? 0, 0, ',', '.') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Tidak ada insentif terkait kendaraan pada periode ini.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabel Rincian Lengkap -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b dark:border-zinc-700">
            <flux:heading size="md">Rincian Gaji Lengkap</flux:heading>
            <span class="text-xs text-gray-500 dark:text-zinc-400">
                {{ $componentCount }} komponen
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-zinc-300">
                    <tr>
                        <th class="px-4 py-3 text-left">Komponen</th>
                        <th class="px-4 py-3 text-right">Qty</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Satuan (Rp)</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-zinc-700">
                    @foreach($salary->salaryDetails ?? [] as $d)
                        @php
                            $compName = $d->salaryComponent?->name ?? '-';
                            $isInsentif = (bool) $d->vehicle;
                            if ($d->vehicle) {
                                $vehicleLabel = trim(implode(' ', array_filter([
                                    $d->vehicle->brand?->name,
                                    $d->vehicle->vehicle_model?->name,
                                    $d->vehicle->year,
                                    $d->vehicle->police_number,
                                ])));
                                $compName = $compName . ' - ' . $vehicleLabel;
                            }
                        @endphp
                        <tr @class([
                            'hover:bg-gray-100 dark:hover:bg-zinc-700/50',
                            'bg-white dark:bg-zinc-800' => $loop->odd,
                            'bg-gray-50 dark:bg-zinc-900' => $loop->even,
                        ])>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span>{{ $compName }}</span>
                                    @if($isInsentif)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                            Insentif
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 text-right">{{ number_format($d->quantity ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">Rp {{ number_format($d->amount ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right font-medium whitespace-nowrap">Rp {{ number_format($d->total_amount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-zinc-700/50 font-bold">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right">Total Gaji</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">Rp {{ number_format($totalSalary ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
