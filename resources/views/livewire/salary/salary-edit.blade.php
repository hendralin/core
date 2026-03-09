<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Penggajian') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ $salary->employee?->name ?? '-' }} - {{ $salary->salary_date ? $salary->salary_date->format('F Y') : '-' }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <flux:button variant="primary" size="sm" href="{{ route('salaries.show', $salary) }}" wire:navigate icon="arrow-uturn-left" class="mb-4">Kembali</flux:button>

    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-2 mb-3">
                <flux:icon.exclamation-circle class="w-5 h-5 text-red-600 dark:text-red-400" />
                <flux:heading size="sm" class="text-red-900 dark:text-red-100">Perbaiki Kesalahan Berikut</flux:heading>
            </div>
            <ul class="text-sm text-red-800 dark:text-red-200 space-y-1">
                @foreach($errors->all() as $error)
                <li class="flex items-start gap-2">
                    <span class="text-red-600 mt-1">•</span>
                    <span>{{ $error }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="submit" class="mt-6 max-w-4xl">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
            <flux:heading size="md" class="mb-4">Periode</flux:heading>
            <flux:input type="date" wire:model="salary_date" label="Tanggal Gaji" />
        </div>

        @php
            $editTotal = 0;
            foreach ($details as $d) {
                $isPk = isset($pinjamanKaryawanComponentId) && (int)($d['salary_component_id'] ?? 0) === (int)$pinjamanKaryawanComponentId;
                $amt = (float) preg_replace('/[^0-9.]/', '', (string)($d['amount'] ?? 0));
                $qty = (int)($d['quantity'] ?? 0);
                $editTotal += $isPk ? (-1 * $qty * $amt) : ($qty * $amt);
            }
            foreach ($additionalComponents as $add) {
                $isPk = isset($pinjamanKaryawanComponentId) && (int)($add['salary_component_id'] ?? 0) === (int)$pinjamanKaryawanComponentId;
                $amt = (float) preg_replace('/[^0-9.]/', '', (string)($add['amount'] ?? 0));
                $qty = (int)($add['quantity'] ?? 0);
                $editTotal += $isPk ? (-1 * $qty * $amt) : ($qty * $amt);
            }
        @endphp

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <flux:heading size="md">Rincian komponen gaji</flux:heading>
                <span class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                    Total Gaji: Rp {{ number_format($editTotal, 0, ',', '.') }}
                </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mb-4">Ubah Qty (untuk komponen kuantitatif) dan Amount sesuai kebutuhan. Total dihitung otomatis.</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-zinc-300">
                        <tr>
                            <th class="px-3 py-2 text-left">Komponen</th>
                            <th class="px-3 py-2 text-center w-28">Qty</th>
                            <th class="px-3 py-2 text-right w-36">Amount (Rp)</th>
                            <th class="px-3 py-2 text-right w-36">Total (Rp)</th>
                            <th class="px-3 py-2 w-14"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-zinc-700">
                        @foreach($details as $index => $d)
                            @php
                                $isPk = isset($pinjamanKaryawanComponentId) && (int)($d['salary_component_id'] ?? 0) === (int)$pinjamanKaryawanComponentId;
                                $qty = (int) ($d['quantity'] ?? 0);
                                $amt = (float) preg_replace('/[^0-9.]/', '', (string)($d['amount'] ?? 0));
                                $total = $isPk ? (-1 * $qty * $amt) : ($qty * $amt);
                            @endphp
                            <tr>
                                <td class="px-3 py-2">
                                    <span>{{ $d['component_name'] }}</span>
                                    @if($d['is_quantitative'] ?? false)
                                        <span class="text-xs text-gray-500 dark:text-zinc-400">(kuantitatif)</span>
                                    @endif
                                    @if($isPk)
                                        <span class="text-xs text-amber-600 dark:text-amber-400">(potongan)</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if($d['is_quantitative'] ?? false)
                                        <flux:input type="number" wire:model.live="details.{{ $index }}.quantity" min="0" step="1" class="w-20 text-center" />
                                    @else
                                        <span class="text-gray-500 dark:text-zinc-400">1</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <flux:input type="text" wire:model.live="details.{{ $index }}.amount" mask:dynamic="$money($input)" placeholder="0" class="w-full text-right" />
                                </td>
                                <td class="px-3 py-2 text-right font-medium @if($isPk) text-amber-700 dark:text-amber-300 @endif">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 w-14">
                                    <flux:button type="button" variant="ghost" size="xs" wire:click="removeDetail({{ $index }})" wire:confirm="Hapus komponen ini dari gaji?" class="text-red-500 hover:text-red-700 cursor-pointer" icon="x-mark" />
                                </td>
                            </tr>
                        @endforeach
                        @foreach($additionalComponents as $addIndex => $add)
                            @php
                                $isPk = isset($pinjamanKaryawanComponentId) && (int)($add['salary_component_id'] ?? 0) === (int)$pinjamanKaryawanComponentId;
                                $addQty = (int) ($add['quantity'] ?? 0);
                                $addAmt = (float) preg_replace('/[^0-9.]/', '', (string)($add['amount'] ?? 0));
                                $addTotal = $isPk ? (-1 * $addQty * $addAmt) : ($addQty * $addAmt);
                                // Cek konfigurasi komponen di karyawan untuk menentukan apakah kuantitatif
                                $baseEsc = $salary->employee?->employeeSalaryComponents->firstWhere('salary_component_id', $add['salary_component_id'] ?? null);
                                $isQuantitative = $baseEsc ? (bool) $baseEsc->is_quantitative : true;
                            @endphp
                            <tr class="bg-gray-50 dark:bg-zinc-700/30">
                                <td class="px-3 py-2">
                                    <flux:select wire:model.live="additionalComponents.{{ $addIndex }}.salary_component_id" class="w-full text-sm" placeholder="Pilih komponen...">
                                        <flux:select.option value="">-- Pilih komponen --</flux:select.option>
                                        @foreach($salaryComponents ?? [] as $sc)
                                            <flux:select.option value="{{ $sc->id }}">{{ $sc->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if(!$isQuantitative)
                                        <span class="text-gray-500 dark:text-zinc-400">1</span>
                                    @else
                                        <flux:input type="number" wire:model.live="additionalComponents.{{ $addIndex }}.quantity" min="0" step="1" class="w-20 text-center" />
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <flux:input type="text" wire:model.live="additionalComponents.{{ $addIndex }}.amount" mask:dynamic="$money($input)" placeholder="0" class="w-full text-right" />
                                </td>
                                <td class="px-3 py-2 text-right font-medium @if($isPk) text-amber-700 dark:text-amber-300 @endif">
                                    Rp {{ number_format($addTotal, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 w-14">
                                    <flux:button type="button" variant="ghost" size="xs" wire:click="removeAdditionalComponent({{ $addIndex }})" class="text-red-500 hover:text-red-700 cursor-pointer" icon="x-mark" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <flux:button type="button" variant="outline" size="sm" wire:click="addAdditionalComponent" icon="plus" class="cursor-pointer">
                    Tambah komponen
                </flux:button>
            </div>
        </div>

        <div class="flex gap-2">
            <flux:button type="submit" variant="primary" class="cursor-pointer" wire:loading.attr="disabled">
                <span wire:loading.remove>Simpan</span>
                <span wire:loading>Menyimpan...</span>
            </flux:button>
            <flux:button type="button" variant="ghost" href="{{ route('salaries.show', $salary) }}" wire:navigate>Batal</flux:button>
        </div>
    </form>
</div>
