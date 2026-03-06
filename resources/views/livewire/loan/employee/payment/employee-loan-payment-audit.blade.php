<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Audit Trail Pembayaran Pinjaman') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Pantau semua aktivitas pada pembayaran pinjaman karyawan') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <flux:button variant="primary" size="sm" href="{{ route('employee-loan-payments.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Pembayaran Pinjaman" class="mb-4">Kembali</flux:button>

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.document-text class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_activities'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Total Aktivitas</flux:text>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.clock class="h-8 w-8 text-green-600 dark:text-green-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['today_activities'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Hari Ini</flux:text>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.plus class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['created_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Dibuat</flux:text>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.pencil-square class="h-8 w-8 text-yellow-600 dark:text-yellow-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['updated_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Diperbarui</flux:text>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.trash class="h-8 w-8 text-red-600 dark:text-red-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['deleted_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Dihapus</flux:text>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-7 gap-4">
            <flux:input type="text" label="Cari" wire:model.live.debounce.300ms="search" placeholder="Cari aktivitas..." clearable />

            <flux:select label="Pembayaran" wire:model.live="selectedPayment">
                <flux:select.option value="">Semua Pembayaran</flux:select.option>
                @foreach($paymentRecords as $rec)
                    <flux:select.option value="{{ $rec->id }}">{{ $rec->paid_at->format('d-m-Y') }} - {{ $rec->employee?->name }} - Rp {{ number_format($rec->amount, 0) }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select label="Aksi" wire:model.live="actionFilter">
                @foreach($this->actionOptions as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input type="date" label="Dari" wire:model.live="dateFrom" />
            <flux:input type="date" label="Sampai" wire:model.live="dateTo" />

            <flux:select label="Per Halaman" wire:model.live="perPage">
                @foreach($this->perPageOptions as $option)
                    <flux:select.option value="{{ $option }}">{{ $option }} per halaman</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex items-end">
                <flux:button wire:click="clearFilters" variant="ghost" class="cursor-pointer">
                    Reset Filter
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Activities List -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden">
        @if($activities->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-zinc-700">
                @foreach($activities as $activity)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <div class="flex items-start space-x-4">
                            <div class="shrink-0">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                                    @switch($activity->description)
                                        @case('created employee loan payment record')
                                            <flux:icon.plus class="w-5 h-5 text-green-600 dark:text-green-400" />
                                            @break
                                        @case('updated employee loan payment record')
                                            <flux:icon.pencil-square class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                                            @break
                                        @case('deleted employee loan payment record')
                                            <flux:icon.trash class="w-5 h-5 text-red-600 dark:text-red-400" />
                                            @break
                                        @default
                                            <flux:icon.document-text class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    @endswitch
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <flux:text class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                                        {{ $activity->description }}
                                    </flux:text>
                                    <flux:text class="text-xs text-gray-500 dark:text-zinc-400">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </flux:text>
                                </div>

                                @if($activity->causer)
                                    <div class="mt-1 text-xs text-gray-600 dark:text-zinc-400">
                                        oleh <span class="font-medium">{{ $activity->causer->name }}</span>
                                    </div>
                                @endif

                                @if($activity->subject)
                                    <div class="mt-2 p-3 bg-gray-50 dark:bg-zinc-700/50 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <flux:text class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                                                    {{ $activity->subject->employee?->name ?? 'N/A' }}
                                                </flux:text>
                                                <div class="text-xs text-gray-600 dark:text-zinc-400 mt-1">
                                                    {{ $activity->subject->paid_at?->format('d-m-Y') ?? 'N/A' }}
                                                    @if($activity->subject->big_cash)
                                                        • Kas Besar
                                                    @elseif($activity->subject->cost?->warehouse)
                                                        • {{ $activity->subject->cost->warehouse->name }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <flux:text class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                                                    Rp {{ number_format($activity->subject->amount ?? 0, 0, ',', '.') }}
                                                </flux:text>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-2 text-xs text-gray-500 dark:text-zinc-400 italic">
                                        Record pembayaran telah dihapus
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700">
                {{ $activities->links(data: ['scrollTo' => false]) }}
            </div>
        @else
            <div class="p-12 text-center">
                <flux:icon.document-text class="mx-auto h-12 w-12 text-gray-400 dark:text-zinc-600" />
                <flux:heading size="md" class="mt-4 text-gray-900 dark:text-zinc-100">Tidak Ada Aktivitas</flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-zinc-400">
                    @if($search || $selectedPayment)
                        Tidak ada aktivitas yang sesuai filter. Coba ubah kriteria pencarian.
                    @else
                        Belum ada aktivitas pembayaran pinjaman yang tercatat.
                    @endif
                </flux:text>
            </div>
        @endif
    </div>
</div>
