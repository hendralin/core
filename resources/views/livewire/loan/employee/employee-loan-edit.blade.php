<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Pinjaman Karyawan') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Perbarui data pinjaman') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <div class="xl:col-span-3">
            <div class="flex items-center justify-between mb-6">
                <flux:button
                    variant="primary"
                    size="sm"
                    href="{{ route('employee-loans.index') }}"
                    wire:navigate
                    icon="arrow-uturn-left"
                    tooltip="Kembali ke Pinjaman"
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

            <form wire:submit="submit" id="employee-loan-edit-form" class="mt-6 space-y-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon.identification class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <div>
                            <flux:heading size="md">Informasi Pinjaman</flux:heading>
                            <flux:subheading size="sm">Karyawan: {{ $employeeLoan->employee?->name ?? '-' }}</flux:subheading>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <flux:input wire:model="paid_at" type="date" label="Tanggal Pinjaman" class="w-full" />
                        </div>

                        <div class="space-y-1">
                            <flux:input
                                wire:model="amount"
                                mask:dynamic="$money($input)"
                                icon="currency-dollar"
                                label="Jumlah Pinjaman (Rp)"
                                placeholder="500,000"
                                class="w-full"
                            />
                        </div>

                        <div class="md:col-span-2">
                            <flux:checkbox wire:model="big_cash" label="Kas Besar" disabled />
                            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">Status sumber dana tidak dapat diubah setelah pinjaman dibuat.</p>
                        </div>

                        <div class="md:col-span-2">
                            <flux:textarea wire:model="description" label="Deskripsi" placeholder="Keterangan..." rows="3" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-zinc-700">
                    <flux:button variant="ghost" href="{{ route('employee-loans.show', $employeeLoan) }}" wire:navigate>
                        <flux:icon.arrow-left class="w-4 h-4 mr-2" />
                        Batal
                    </flux:button>

                    <flux:button type="submit" variant="primary" icon="check">
                        <span wire:loading.remove wire:target="submit">Simpan Perubahan</span>
                        <span wire:loading wire:target="submit">Menyimpan...</span>
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
