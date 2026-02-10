<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Buat Penggajian') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Generate gaji untuk semua karyawan per periode (akhir bulan)') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <flux:button variant="primary" size="sm" href="{{ route('salaries.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali" class="mb-4">Kembali</flux:button>

    <div class="w-full max-w-4xl">
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

        <form wire:submit="submit" class="mt-6 space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="md" class="mb-4">Periode Gaji</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:select wire:model="period_month" label="Bulan">
                        @foreach($monthOptions as $num => $label)
                            <flux:select.option value="{{ $num }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="period_year" label="Tahun">
                        @foreach($yearOptions as $y => $label)
                            <flux:select.option value="{{ $y }}">{{ $y }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="md" class="mb-4">Karyawan</flux:heading>
                <div class="space-y-4">
                    <div class="max-h-64 overflow-y-auto border border-gray-200 dark:border-zinc-600 rounded-lg p-4">
                        @php
                            $grandTotal = 0;
                            foreach ($employees ?? [] as $e) {
                                if (isset($componentInputs[$e->id])) {
                                    foreach ($e->employeeSalaryComponents ?? [] as $esc) {
                                        $in = $componentInputs[$e->id][$esc->id] ?? ['quantity' => 0, 'amount' => 0];
                                        $amt = (float) preg_replace('/[^0-9.]/', '', (string)($in['amount'] ?? 0));
                                        $grandTotal += (int)($in['quantity'] ?? 0) * $amt;
                                    }
                                    foreach ($additionalComponents[$e->id] ?? [] as $add) {
                                        $amt = (float) preg_replace('/[^0-9.]/', '', (string)($add['amount'] ?? 0));
                                        $grandTotal += (int)($add['quantity'] ?? 0) * $amt;
                                    }
                                }
                            }
                        @endphp
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <flux:text class="text-sm font-medium">Pilih karyawan (klik untuk tampilkan komponen gaji):</flux:text>
                            @if(isset($grandTotal) && $grandTotal > 0)
                                <flux:text class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                    Grand Total Gaji: Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </flux:text>
                            @endif
                        </div>
                        @foreach($employees as $emp)
                            @php
                                $rowTotal = 0;
                                if (isset($componentInputs[$emp->id])) {
                                    foreach ($emp->employeeSalaryComponents ?? [] as $esc) {
                                        $in = $componentInputs[$emp->id][$esc->id] ?? ['quantity' => 0, 'amount' => 0];
                                        $amt = (float) preg_replace('/[^0-9.]/', '', (string)($in['amount'] ?? 0));
                                        $rowTotal += (int)($in['quantity'] ?? 0) * $amt;
                                    }
                                    foreach ($additionalComponents[$emp->id] ?? [] as $add) {
                                        $amt = (float) preg_replace('/[^0-9.]/', '', (string)($add['amount'] ?? 0));
                                        $rowTotal += (int)($add['quantity'] ?? 0) * $amt;
                                    }
                                }
                            @endphp
                            <label class="flex items-center justify-between gap-4 py-1 cursor-pointer w-full">
                                <span class="flex items-center gap-2 min-w-0">
                                    <flux:checkbox wire:model.live="employee_ids" value="{{ $emp->id }}" />
                                    <span>{{ $emp->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-zinc-400">({{ $emp->position?->name ?? '-' }})</span>
                                </span>
                                <span class="text-sm font-medium text-gray-700 dark:text-zinc-300 whitespace-nowrap shrink-0">
                                    @if(isset($componentInputs[$emp->id]))
                                        Rp {{ number_format($rowTotal, 0, ',', '.') }}
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @if($employees->isEmpty())
                        <flux:text class="text-amber-600 dark:text-amber-400">Tidak ada karyawan dengan komponen gaji. Tambah komponen gaji di menu Karyawan (Edit karyawan → Salary Components).</flux:text>
                    @endif
                </div>
            </div>

            @if(isset($selectedEmployees) && $selectedEmployees->isNotEmpty())
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="md" class="mb-4">Rincian komponen gaji per karyawan</flux:heading>
                    <p class="text-sm text-gray-600 dark:text-zinc-400 mb-4">Ubah Qty (untuk komponen kuantitatif) dan Amount sesuai kebutuhan. Total dihitung otomatis.</p>
                    @foreach($selectedEmployees as $emp)
                        @php
                            $empInputs = $componentInputs[$emp->id] ?? [];
                            $empTotal = 0;
                            foreach ($emp->employeeSalaryComponents ?? [] as $esc) {
                                $in = $empInputs[$esc->id] ?? ['quantity' => 0, 'amount' => 0];
                                $amt = (float) preg_replace('/[^0-9.]/', '', (string)($in['amount'] ?? 0));
                                $empTotal += (int)($in['quantity'] ?? 0) * $amt;
                            }
                            foreach ($additionalComponents[$emp->id] ?? [] as $add) {
                                $amt = (float) preg_replace('/[^0-9.]/', '', (string)($add['amount'] ?? 0));
                                $empTotal += (int)($add['quantity'] ?? 0) * $amt;
                            }
                        @endphp
                        <div class="border border-gray-200 dark:border-zinc-600 rounded-lg p-4 mb-4 last:mb-0">
                            <div class="flex items-center justify-between gap-4 mb-3">
                                <flux:heading size="sm">{{ $emp->name }} ({{ $emp->position?->name ?? '-' }})</flux:heading>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                    Total Gaji: Rp {{ number_format($empTotal, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-zinc-300">
                                        <tr>
                                            <th class="px-3 py-2 text-left whitespace-nowrap">Komponen</th>
                                            <th class="px-3 py-2 text-center w-28 whitespace-nowrap">Qty</th>
                                            <th class="px-3 py-2 text-right w-36 whitespace-nowrap">Amount (Rp)</th>
                                            <th class="px-3 py-2 text-right w-36 whitespace-nowrap">Total (Rp)</th>
                                            <th class="px-3 py-2 w-14"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y dark:divide-zinc-700">
                                        @foreach($emp->employeeSalaryComponents as $esc)
                                            @php
                                                $input = $empInputs[$esc->id] ?? ['quantity' => 0, 'amount' => 0];
                                                $qty = (int) ($input['quantity'] ?? 0);
                                                $amt = (float) preg_replace('/[^0-9.]/', '', (string)($input['amount'] ?? 0));
                                                $total = $qty * $amt;
                                            @endphp
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span>{{ $esc->salaryComponent?->name ?? '-' }}</span>
                                                    @if($esc->is_quantitative)
                                                        <span class="text-xs text-gray-500 dark:text-zinc-400">(kuantitatif)</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                                    @if($esc->is_quantitative)
                                                        <flux:input type="number" wire:model.live="componentInputs.{{ $emp->id }}.{{ $esc->id }}.quantity" min="0" step="1" class="w-full text-center" />
                                                    @else
                                                        <span class="text-gray-500 dark:text-zinc-400">1</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2">
                                                    <flux:input type="text" wire:model.live="componentInputs.{{ $emp->id }}.{{ $esc->id }}.amount" mask:dynamic="$money($input)" placeholder="0" class="w-full text-right" />
                                                </td>
                                                <td class="px-3 py-2 text-right font-medium whitespace-nowrap">
                                                    Rp {{ number_format($total, 0, ',', '.') }}
                                                </td>
                                                <td class="px-3 py-2 w-14"></td>
                                            </tr>
                                        @endforeach
                                        @foreach($additionalComponents[$emp->id] ?? [] as $addIndex => $add)
                                            @php
                                                $addQty = (int) ($add['quantity'] ?? 0);
                                                $addAmt = (float) preg_replace('/[^0-9.]/', '', (string)($add['amount'] ?? 0));
                                                $addTotal = $addQty * $addAmt;
                                                $addComp = $salaryComponents->firstWhere('id', $add['salary_component_id']);
                                                // Cek apakah komponen ini punya konfigurasi non-kuantitatif di karyawan
                                                $baseEsc = $emp->employeeSalaryComponents->firstWhere('salary_component_id', $add['salary_component_id'] ?? null);
                                                $isQuantitative = $baseEsc ? (bool) $baseEsc->is_quantitative : true;
                                            @endphp
                                            <tr class="bg-gray-50 dark:bg-zinc-700/30">
                                                <td class="px-3 py-2">
                                                    @if(($add['is_auto'] ?? false) && !empty($add['vehicle_id']))
                                                        <div class="whitespace-nowrap">
                                                            <span class="font-medium text-gray-800 dark:text-zinc-100">{{ $add['vehicle_label'] ?? ($addComp?->name ?? 'Insentif') }}</span>
                                                            <span class="text-xs text-gray-500 dark:text-zinc-400">(auto)</span>
                                                        </div>
                                                    @else
                                                        <flux:select wire:model.live="additionalComponents.{{ $emp->id }}.{{ $addIndex }}.salary_component_id" class="w-full text-sm">
                                                            <flux:select.option value="">-- Pilih komponen --</flux:select.option>
                                                            @foreach($salaryComponents ?? [] as $sc)
                                                                <flux:select.option value="{{ $sc->id }}">{{ $sc->name }}</flux:select.option>
                                                            @endforeach
                                                        </flux:select>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    @if($add['is_auto'] ?? false)
                                                        <span class="text-gray-500 dark:text-zinc-400">1</span>
                                                    @elseif(!$isQuantitative)
                                                        <span class="text-gray-500 dark:text-zinc-400">1</span>
                                                    @else
                                                        <flux:input type="number" wire:model.live="additionalComponents.{{ $emp->id }}.{{ $addIndex }}.quantity" min="0" step="1" class="w-20 text-center" />
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2">
                                                    <flux:input type="text" wire:model.live="additionalComponents.{{ $emp->id }}.{{ $addIndex }}.amount" mask:dynamic="$money($input)" placeholder="0" class="w-full text-right" />
                                                </td>
                                                <td class="px-3 py-2 text-right font-medium">
                                                    Rp {{ number_format($addTotal, 0, ',', '.') }}
                                                </td>
                                                <td class="px-3 py-2 w-14">
                                                    @if(!($add['is_auto'] ?? false))
                                                        <flux:button type="button" variant="ghost" size="xs" wire:click="removeAdditionalComponent({{ $emp->id }}, {{ $addIndex }})" class="text-red-500 hover:text-red-700 cursor-pointer" icon="x-mark" />
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <flux:button type="button" variant="outline" size="sm" wire:click="addAdditionalComponent({{ $emp->id }})" icon="plus" class="cursor-pointer">
                                    Tambah komponen
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" class="cursor-pointer" wire:loading.attr="disabled">
                    <span wire:loading.remove>Simpan</span>
                    <span wire:loading>Memproses...</span>
                </flux:button>
                <flux:button type="button" variant="ghost" href="{{ route('salaries.index') }}" wire:navigate>Batal</flux:button>
            </div>
        </form>
    </div>
</div>
