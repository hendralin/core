@php
    use Illuminate\Support\Str;
@endphp

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Detail Pembukuan Modal') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Detail informasi pembukuan modal kendaraan') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('costs.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Pembukuan Modal">Back</flux:button>
        @can('cost.edit')
            @if($cost->status === 'pending')
                <flux:button variant="filled" size="sm" href="{{ route('costs.edit', $cost) }}" wire:navigate icon="pencil-square" class="ml-1">Edit</flux:button>
            @endif
        @endcan

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Informasi Pembukuan Modal</flux:heading>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:heading size="md">Tipe</flux:heading>
                                <flux:text class="mt-1">{{ $cost->cost_type == 'service_parts' ? 'Service & Parts' : 'Biaya Lainnya' }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="md">Tanggal</flux:heading>
                                <flux:text class="mt-1">{{ Carbon\Carbon::parse($cost->cost_date)->format('d-m-Y') }}</flux:text>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:heading size="sm">Nama Vendor</flux:heading>
                                <flux:text class="mt-1">{{ $cost->vendor?->name ?? 'N/A' }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm">Total Biaya</flux:heading>
                                <flux:text class="mt-1 font-medium">Rp {{ number_format($cost->total_price) }}</flux:text>
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

                <!-- Vehicle Information -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Informasi Kendaraan</flux:heading>

                    <div class="space-y-4">
                        <div>
                            <flux:heading size="sm">Nomor Polisi</flux:heading>
                            <flux:text class="mt-1 font-medium">{{ $cost->vehicle->police_number }}</flux:text>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:heading size="sm">Merek</flux:heading>
                                <flux:text class="text-sm">{{ $cost->vehicle->brand->name ?? 'N/A' }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm">Model</flux:heading>
                                <flux:text class="text-sm">{{ $cost->vehicle->vehicle_model->name ?? '' }}</flux:text>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:heading size="sm">Tipe</flux:heading>
                                <flux:text class="text-sm">{{ $cost->vehicle->type->name ?? 'N/A' }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm">Tahun</flux:heading>
                                <flux:text class="text-sm">{{ $cost->vehicle->year ?? 'N/A' }}</flux:text>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:heading size="sm">Nomor Rangka</flux:heading>
                                <flux:text class="text-sm">{{ $cost->vehicle->chassis_number ?? 'N/A' }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm">Nomor Mesin</flux:heading>
                                <flux:text class="text-sm">{{ $cost->vehicle->engine_number ?? 'N/A' }}</flux:text>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Section -->
                @if($cost->document)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Dokumen</flux:heading>

                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-700/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <flux:icon.document class="w-8 h-8 text-gray-400" />
                            <div>
                                <flux:text class="font-medium">{{ basename($cost->document) }}</flux:text>
                                <flux:text class="text-sm text-gray-500">
                                    @if(\Storage::disk('public')->exists('photos/costs/' . $cost->document))
                                        {{ number_format(\Storage::disk('public')->size('photos/costs/' . $cost->document) / 1024, 1) }} KB
                                    @endif
                                </flux:text>
                            </div>
                        </div>
                        <flux:button variant="primary" size="sm" href="{{ asset('photos/costs/' . $cost->document) }}" target="_blank" icon="eye">
                            Lihat Dokumen
                        </flux:button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Statistics Sidebar -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Status & Actions</flux:heading>

                    <div class="space-y-4">
                        <div class="text-center">
                            @if($cost->status === 'approved')
                                <flux:badge color="green" icon="check">Approved</flux:badge>
                            @elseif($cost->status === 'rejected')
                                <flux:badge color="red" icon="x-circle">Rejected</flux:badge>
                            @else
                                <flux:badge color="yellow" icon="clock">Pending</flux:badge>
                            @endif
                        </div>

                        @if($cost->status === 'pending')
                            <div class="space-y-2">
                                @can('cost.approve')
                                    <flux:button variant="primary" size="sm" icon="check" wire:click="approve" tooltip="Approve" class="w-full cursor-pointer" wire:confirm="Are you sure you want to approve this cost record?">
                                        Approve
                                    </flux:button>
                                @endcan
                                @can('cost.reject')
                                    <flux:button variant="danger" size="sm" icon="x-mark" wire:click="reject" tooltip="Reject" class="w-full mt-2 cursor-pointer" wire:confirm="Are you sure you want to reject this cost record?">
                                        Reject
                                    </flux:button>
                                @endcan
                            </div>
                        @endif

                        <div class="border-t border-gray-200 dark:border-zinc-700 pt-4 space-y-2">
                            @can('cost.view')
                                <flux:button size="sm" href="{{ route('costs.audit') }}?model=Cost&id={{ $cost->id }}" wire:navigate class="w-full" icon="document-text">
                                    View Audit Trail
                                </flux:button>
                            @endcan

                            <flux:button variant="ghost" size="sm" href="{{ route('vehicles.show', $cost->vehicle) }}" wire:navigate class="w-full" icon="eye">
                                View Vehicle Details
                            </flux:button>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Record Info</flux:heading>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <flux:text>Total Cost</flux:text>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                Rp {{ number_format($cost->total_price, 0) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <flux:text>Record Age</flux:text>
                            <span class="text-sm text-gray-600 dark:text-zinc-400">
                                {{ $cost->created_at->diffForHumans() }}
                            </span>
                        </div>

                        @if($cost->updated_at != $cost->created_at)
                        <div class="flex items-center justify-between">
                            <flux:text>Last Modified</flux:text>
                            <span class="text-sm text-gray-600 dark:text-zinc-400">
                                {{ $cost->updated_at->diffForHumans() }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
