@php
    use Illuminate\Support\Str;
@endphp

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Detail Pengeluaran Kas') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Detail informasi pengeluaran kas perusahaan') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('cash-disbursements.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Pengeluaran Kas">Back</flux:button>
        @can('cashdisbursement.edit')
            @if($cost->status === 'pending')
                <flux:button variant="filled" size="sm" href="{{ route('cash-disbursements.edit', $cost) }}" wire:navigate icon="pencil-square" class="ml-1">Edit</flux:button>
            @endif
        @endcan

        <div class="mt-3">
            @session('success')
                <flux:callout color="green" icon="check-circle">
                    {{ session('success') }}
                </flux:callout>
            @endsession

            @session('error')
                <flux:callout color="red" icon="x-circle">
                    {{ session('error') }}
                </flux:callout>
            @endsession
        </div>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Informasi Pengeluaran Kas</flux:heading>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:heading size="md">Tanggal</flux:heading>
                                <flux:text class="mt-1">{{ Carbon\Carbon::parse($cost->cost_date)->format('d-m-Y') }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm">Total Pengeluaran</flux:heading>
                                <flux:text class="mt-1 font-medium text-lg">Rp {{ number_format($cost->total_price) }}</flux:text>
                            </div>
                        </div>

                        @if($cost->description)
                        <div>
                            <flux:heading size="sm">Deskripsi</flux:heading>
                            <flux:text class="mt-1">{!! nl2br(e($cost->description)) !!}</flux:text>
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                            <div>
                                <flux:heading size="sm">Dibuat Oleh</flux:heading>
                                <flux:text class="text-sm">{{ $cost->createdBy->name ?? 'N/A' }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm">Status</flux:heading>
                                <flux:text class="text-sm">
                                    @if($cost->status === 'approved')
                                        <flux:badge size="sm" color="green" icon="check">Approved</flux:badge>
                                    @elseif($cost->status === 'rejected')
                                        <flux:badge size="sm" color="red" icon="x-circle">Rejected</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="yellow" icon="clock">Pending</flux:badge>
                                    @endif
                                </flux:text>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Section -->
                @if($cost->document)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Dokumentasi</flux:heading>

                    <div class="flex items-center space-x-4">
                        @php
                            $extension = pathinfo($cost->document, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                        @endphp

                        @if($isImage)
                            <img src="{{ asset('photos/costs/' . $cost->document) }}" alt="Document" class="w-24 h-24 object-contain rounded-lg border-2 border-gray-200 dark:border-zinc-700">
                        @else
                            <div class="w-24 h-24 border-2 border-gray-200 dark:border-zinc-700 rounded-lg flex items-center justify-center bg-gray-50 dark:bg-zinc-800">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        @endif

                        <div class="flex-1">
                            <flux:heading size="sm">File Dokumen</flux:heading>
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400 mt-1">{{ $cost->document }}</flux:text>
                            <div class="mt-2">
                                <a href="{{ asset('photos/costs/' . $cost->document) }}" target="_blank" class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    Lihat Dokumen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Sidebar -->
            <div class="space-y-6">
                @if($cost->status === 'pending')
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                        <flux:heading size="lg" class="mb-4">Aksi</flux:heading>

                        <div class="space-y-4">
                            @can('cashdisbursement.approve')
                                <flux:modal.trigger name="approve-modal">
                                    <flux:button variant="primary" icon="check" class="w-full cursor-pointer mb-2">
                                        Setujui Pengeluaran
                                    </flux:button>
                                </flux:modal.trigger>
                            @endcan

                            @can('cashdisbursement.reject')
                                <flux:modal.trigger name="reject-modal">
                                    <flux:button variant="danger" icon="x-mark" class="w-full cursor-pointer">
                                        Tolak Pengeluaran
                                    </flux:button>
                                </flux:modal.trigger>
                            @endcan
                        </div>
                    </div>
                @endif

                <!-- Activity Log -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Riwayat Aktivitas</flux:heading>

                    <div class="space-y-3">
                        @foreach($cost->activities ?? [] as $activity)
                            <div class="flex items-start space-x-3">
                                <div class="shrink-0">
                                    @if($activity->description === 'created cost record' || $activity->description === 'created cash disbursement record')
                                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                    @elseif(str_contains($activity->description, 'updated'))
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-gray-100 dark:bg-gray-900 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ ucfirst(str_replace(['cost record', 'cash disbursement record'], ['pengeluaran kas', 'pengeluaran kas'], $activity->description)) }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-zinc-400">
                                        {{ $activity->causer?->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach

                        @if(empty($cost->activities) || $cost->activities->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-zinc-400">Belum ada aktivitas tercatat.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <flux:modal name="approve-modal" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Setujui pengeluaran kas?</flux:heading>
                <flux:text class="mt-2">
                    <p>Anda akan menyetujui pengeluaran kas ini sebesar <strong>Rp {{ number_format($cost->total_price) }}</strong>.</p>
                    <p>Setelah disetujui, pengeluaran ini akan tercatat sebagai pengeluaran yang sah.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="approve" variant="primary" color="green">Setujui</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Reject Modal -->
    <flux:modal name="reject-modal" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tolak pengeluaran kas?</flux:heading>
                <flux:text class="mt-2">
                    <p>Anda akan menolak pengeluaran kas ini sebesar <strong>Rp {{ number_format($cost->total_price) }}</strong>.</p>
                    <p>Setelah ditolak, pengeluaran ini tidak akan dapat diubah lagi.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="reject" variant="danger">Tolak</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
