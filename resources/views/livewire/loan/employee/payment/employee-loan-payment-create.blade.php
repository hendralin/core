<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Tambah Pembayaran Pinjaman') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Pencatatan pembayaran pinjaman ke Kas') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <div class="xl:col-span-3">
            <div class="flex items-center justify-between mb-6">
                <flux:button
                    variant="primary"
                    size="sm"
                    href="{{ route('employee-loan-payments.index') }}"
                    wire:navigate
                    icon="arrow-uturn-left"
                    tooltip="Kembali ke Pembayaran Pinjaman"
                >
                    Kembali
                </flux:button>
            </div>

            @session('success')
                <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
            @endsession

            @session('error')
                <x-alert type="error" class="mb-4">{{ session('error') }}</x-alert>
            @endsession

            <form wire:submit="submit" id="employee-loan-payment-form" class="mt-6 space-y-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.identification class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <div>
                            <flux:heading size="md">Informasi Dasar</flux:heading>
                            <flux:subheading size="sm">Pilih karyawan yang membayar dan tujuan (Kas Kecil / Kas Besar)</flux:subheading>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2 space-y-1" x-data="{ open: false }" @click.away="open = false">
                            <label class="flux-label block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Karyawan *</label>
                            @if($selectedEmployee)
                                <div class="flex items-center gap-2 min-h-10 px-3 py-2 rounded-lg border border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-800">
                                    <span class="flex-1 min-w-0 text-zinc-900 dark:text-white whitespace-nowrap overflow-hidden text-ellipsis">{{ $selectedEmployee->name }}</span>
                                    <flux:button type="button" variant="ghost" size="xs" wire:click="clearEmployee" class="shrink-0">Ubah</flux:button>
                                </div>
                                <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">Sisa pinjaman: Rp {{ number_format($selectedEmployee->remaining_loan ?? 0, 0, ',', '.') }}</p>
                            @else
                                <div class="relative">
                                    <flux:input
                                        type="text"
                                        wire:model.live.debounce.300ms="employee_search"
                                        placeholder="Cari nama karyawan..."
                                        class="w-full"
                                        @focus="open = true"
                                    />
                                    <div x-show="open"
                                         x-transition
                                         class="absolute z-20 w-full mt-1 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                        @forelse($employees as $emp)
                                            <button type="button"
                                                    wire:click="setEmployeeId({{ $emp->id }})"
                                                    wire:key="emp-{{ $emp->id }}"
                                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-left text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700/50 transition-colors border-b border-zinc-100 dark:border-zinc-700 last:border-b-0 whitespace-nowrap overflow-hidden text-ellipsis min-w-0">
                                                {{ $emp->name }}
                                            </button>
                                        @empty
                                            <div class="px-3 py-4 text-sm text-zinc-500 dark:text-zinc-400 text-center">
                                                {{ strlen(trim($employee_search ?? '')) > 0 ? 'Tidak ada karyawan yang cocok. Coba kata kunci lain.' : 'Ketik untuk mencari karyawan.' }}
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endif
                            <p class="text-xs text-slate-500 dark:text-zinc-400">Pilih karyawan yang melakukan pembayaran</p>
                            @error('employee_id')
                                <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <flux:input wire:model="paid_at" type="date" label="Tanggal Pembayaran" class="w-full" />
                            <p class="text-xs text-slate-500 dark:text-zinc-400">Kapan pembayaran dilakukan?</p>
                        </div>

                        <div class="space-y-1">
                            <flux:input
                                wire:model="amount"
                                mask:dynamic="$money($input)"
                                icon="currency-dollar"
                                label="Jumlah Pembayaran (Rp)"
                                placeholder="500,000"
                                class="w-full"
                            />
                            <p class="text-xs text-slate-500 dark:text-zinc-400">Tidak boleh melebihi sisa pinjaman karyawan</p>
                        </div>

                        @if(!$big_cash)
                            <div class="md:col-span-2 space-y-1">
                                <flux:select wire:model="warehouse_id" label="Kas (Tujuan)" class="w-full">
                                    <flux:select.option value="">{{ __('Pilih Kas') }}</flux:select.option>
                                    @foreach($warehouses as $wh)
                                        <flux:select.option value="{{ $wh->id }}">{{ $wh->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                <p class="text-xs text-slate-500 dark:text-zinc-400">Pembayaran ke Kas Kecil akan menambah saldo kas dan mencatat ke costs (loan_payment) & payments</p>
                            </div>
                        @endif

                        <div class="md:col-span-2">
                            <flux:checkbox wire:model.live="big_cash" label="Kas Besar" />
                            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">Centang jika pembayaran ke Kas Besar</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.document-text class="w-5 h-5 text-green-600 dark:text-green-400" />
                        <div>
                            <flux:heading size="md">Keterangan</flux:heading>
                            <flux:subheading size="sm">Deskripsi pembayaran</flux:subheading>
                        </div>
                    </div>
                    <flux:textarea
                        wire:model="description"
                        label="Deskripsi"
                        placeholder="Misal: Angsuran ke-2..."
                        rows="3"
                    />
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-zinc-700">
                    <flux:button variant="ghost" href="{{ route('employee-loan-payments.index') }}" wire:navigate>
                        <flux:icon.arrow-left class="w-4 h-4 mr-2" />
                        Batal
                    </flux:button>

                    <flux:button type="submit" variant="primary" icon="plus">
                        <span wire:loading.remove wire:target="submit">Simpan</span>
                        <span wire:loading wire:target="submit">Menyimpan...</span>
                    </flux:button>
                </div>
            </form>
        </div>

        <div class="xl:col-span-1">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 sticky top-6">
                <div class="flex items-center gap-3 mb-4">
                    <flux:icon.light-bulb class="w-5 h-5 text-green-600 dark:text-green-400" />
                    <div>
                        <flux:heading size="sm">Info</flux:heading>
                        <flux:subheading size="xs">Pembayaran ke Kas Kecil akan menambah saldo kas dan mengurangi sisa pinjaman karyawan.</flux:subheading>
                    </div>
                </div>
                <ul class="text-sm text-gray-700 dark:text-zinc-300 space-y-2">
                    <li>• Pilih Kas jika pembayaran ke Kas Kecil</li>
                    <li>• Centang Kas Besar jika ke Kas Besar</li>
                    <li>• Jumlah tidak boleh melebihi sisa pinjaman</li>
                </ul>
            </div>
        </div>
    </div>
</div>
