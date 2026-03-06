<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Detail Pembayaran Pinjaman') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Informasi lengkap pembayaran pinjaman') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
        <flux:button variant="primary" size="sm" href="{{ route('employee-loan-payments.index') }}" wire:navigate icon="arrow-uturn-left">
            Kembali
        </flux:button>
        @can('employee-loan-payment.edit')
            <flux:button variant="ghost" size="sm" href="{{ route('employee-loan-payments.edit', $employeeLoanPayment) }}" wire:navigate icon="pencil-square">
                Edit
            </flux:button>
        @endcan
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
    @endsession

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Informasi Pembayaran</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <flux:heading size="sm" class="text-gray-500 dark:text-zinc-400">Karyawan</flux:heading>
                        <flux:text class="mt-1 font-medium">{{ $employeeLoanPayment->employee?->name ?? '-' }}</flux:text>
                    </div>
                    <div>
                        <flux:heading size="sm" class="text-gray-500 dark:text-zinc-400">Tanggal Pembayaran</flux:heading>
                        <flux:text class="mt-1">{{ $employeeLoanPayment->paid_at?->format('d-m-Y') ?? '-' }}</flux:text>
                    </div>
                    <div>
                        <flux:heading size="sm" class="text-gray-500 dark:text-zinc-400">Jumlah</flux:heading>
                        <flux:text class="mt-1 font-semibold text-lg">Rp {{ number_format($employeeLoanPayment->amount, 0, ',', '.') }}</flux:text>
                    </div>
                    <div>
                        <flux:heading size="sm" class="text-gray-500 dark:text-zinc-400">Tujuan</flux:heading>
                        <flux:text class="mt-1">
                            @if($employeeLoanPayment->big_cash)
                                <flux:badge color="blue">Kas Besar</flux:badge>
                            @else
                                <flux:badge color="green">{{ $employeeLoanPayment->cost?->warehouse?->name ?? 'Kas Kecil' }}</flux:badge>
                            @endif
                        </flux:text>
                    </div>
                    <div class="md:col-span-2">
                        <flux:heading size="sm" class="text-gray-500 dark:text-zinc-400">Deskripsi</flux:heading>
                        <flux:text class="mt-1">{{ $employeeLoanPayment->description ?? '-' }}</flux:text>
                    </div>
                    <div>
                        <flux:heading size="sm" class="text-gray-500 dark:text-zinc-400">Dibuat oleh</flux:heading>
                        <flux:text class="mt-1">{{ $employeeLoanPayment->createdBy?->name ?? '-' }}</flux:text>
                    </div>
                    <div>
                        <flux:heading size="sm" class="text-gray-500 dark:text-zinc-400">Dibuat pada</flux:heading>
                        <flux:text class="mt-1">{{ $employeeLoanPayment->created_at?->format('d-m-Y H:i') ?? '-' }}</flux:text>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Sisa Pinjaman Karyawan</flux:heading>
                <flux:text class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    Rp {{ number_format($employeeLoanPayment->employee?->remaining_loan ?? 0, 0, ',', '.') }}
                </flux:text>
                <flux:subheading size="sm" class="mt-2">Total sisa pinjaman {{ $employeeLoanPayment->employee?->name ?? 'karyawan' }} setelah pembayaran ini</flux:subheading>
            </div>

            @if($employeeLoanPayment->cost_id)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mt-6">
                    <flux:heading size="md" class="mb-2">Tercatat di Kas Kecil</flux:heading>
                    <flux:text class="text-sm">Pembayaran ini tercatat di Kas Kecil, sehingga saldo Kas Kecil bertambah.</flux:text>
                </div>
            @endif
        </div>
    </div>
</div>
