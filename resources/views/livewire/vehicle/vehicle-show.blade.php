@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show Vehicle') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Vehicle details and information') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-6">
            <flux:callout variant="success" class="mb-4" icon="check-circle" heading="{{ session('message') }}" />
        </div>
    @endif

    <!-- Hero Section -->
    @php
        $randomImage = null;
        if ($vehicle->images && $vehicle->images->count() > 0) {
            $randomImage = $vehicle->images->random();
        }
    @endphp
    <div class="relative rounded-lg p-6 mb-6 text-white overflow-hidden
                @if($randomImage)
                    min-h-[120px]
                @else
                    bg-line-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900
                @endif"
         @if($randomImage)
         style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset('photos/vehicles/' . $randomImage->image) }}'); background-size: cover; background-position: center; background-blend-mode: multiply;"
         @endif>
        <div class="flex items-center justify-between relative z-10">
            <div class="flex items-center space-x-4">
                <div class="shrink-0">
                    @if($randomImage)
                        <!-- Vehicle Image -->
                        <div class="w-16 h-16 rounded-lg overflow-hidden border-2 border-white/30 shadow-lg">
                            <img src="{{ asset('photos/vehicles/' . $randomImage->image) }}"
                                 alt="Vehicle Photo"
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <!-- Default Icon -->
                        <div class="w-16 h-16 bg-white/20 rounded-lg flex items-center justify-center">
                            <flux:icon.truck class="w-8 h-8" />
                        </div>
                    @endif
                </div>
                <div>
                    <flux:heading size="xl" class="text-white mb-1">{{ $vehicle->police_number }}</flux:heading>
                    <div class="flex items-center space-x-4 text-white">
                        <span>{{ $vehicle->brand?->name ?? 'Unknown Brand' }}</span>
                        <span>•</span>
                        <span>{{ $vehicle->vehicle_model?->name ?? 'Unknown Model' }}</span>
                        <span>•</span>
                        <span>{{ $vehicle->type?->name ?? 'Unknown Type' }}</span>
                        <span>•</span>
                        <span>{{ $vehicle->year }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                @php
                    $statusClasses = $vehicle->status == 1
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses }}">
                    {{ $vehicle->status == 1 ? 'Available' : 'Sold' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-2">
            <flux:button variant="primary" size="sm" href="{{ route('vehicles.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Vehicles">Back</flux:button>
            @can('vehicle.edit')
                <flux:button variant="filled" size="sm" href="{{ route('vehicles.edit', $vehicle->id) }}" wire:navigate icon="pencil-square">Edit</flux:button>
            @endcan
        </div>

        <!-- Key Metrics -->
        <div class="flex items-center space-x-4">
            <div class="text-center">
                <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($vehicle->kilometer, 0, ',', '.') }}</flux:text>
                <flux:text class="text-xs text-gray-600 dark:text-gray-400">KM</flux:text>
            </div>
            <div class="text-center">
                <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">
                    @if($vehicle->display_price)
                        Rp {{ number_format($vehicle->display_price / 1000000, 2, ',', '.') }}Jt
                    @else
                        -
                    @endif
                </flux:text>
                <flux:text class="text-xs text-gray-600 dark:text-gray-400">Sell</flux:text>
            </div>
            <div class="text-center">
                <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">
                    @if($vehicle->vehicle_registration_date)
                        @php
                            $registrationDate = \Carbon\Carbon::parse($vehicle->vehicle_registration_date);
                            $vehicleAgeDate = \Carbon\Carbon::create($vehicle->year, $registrationDate->month, $registrationDate->day);
                            $ageInYears = $vehicleAgeDate->diffInYears(now());
                        @endphp
                        {{ number_format($ageInYears, 1, ',', '.') }}
                    @else
                        -
                    @endif
                </flux:text>
                <flux:text class="text-xs text-gray-600 dark:text-gray-400">Years Old</flux:text>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Informasi Kendaraan</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <div>
                                <flux:heading size="md">Nomor Polisi</flux:heading>
                                <flux:text class="mt-1">{{ $vehicle->police_number }}</flux:text>
                            </div>

                            <div>
                                <flux:heading size="md">Tahun</flux:heading>
                                <flux:text class="mt-1">{{ $vehicle->year }}</flux:text>
                            </div>

                            <div>
                                <flux:heading size="md">Brand</flux:heading>
                                <flux:text class="mt-1">{{ $vehicle->brand?->name ?? '-' }}</flux:text>
                            </div>

                            <div>
                                <flux:heading size="md">Type</flux:heading>
                                <flux:text class="mt-1">{{ $vehicle->type?->name ?? '-' }}</flux:text>
                            </div>

                            <div>
                                <flux:heading size="md">Category</flux:heading>
                                <flux:text class="mt-1">{{ $vehicle->category?->name ?? '-' }}</flux:text>
                            </div>

                            <div>
                                <flux:heading size="md">Model</flux:heading>
                                <flux:text class="mt-1">{{ $vehicle->vehicle_model?->name ?? '-' }}</flux:text>
                            </div>
                        </div>

                        <!-- Technical Information -->
                        <div class="space-y-4">
                            <div>
                                <flux:heading size="md">Nomor Rangka</flux:heading>
                                <flux:text class="mt-1">{{ $vehicle->chassis_number }}</flux:text>
                            </div>

                            <div>
                                <flux:heading size="md">Nomor Mesin</flux:heading>
                                <flux:text class="mt-1">{{ $vehicle->engine_number }}</flux:text>
                            </div>

                            <div>
                                <flux:heading size="md">Kapasitas Silinder</flux:heading>
                                <flux:text class="mt-1">{{ $vehicle->cylinder_capacity ? number_format($vehicle->cylinder_capacity, 0) . ' cc' : '-' }}</flux:text>
                            </div>

                            <div>
                                <flux:heading size="md">Warna</flux:heading>
                                <flux:text class="mt-1">{{ $vehicle->color ?? '-' }}</flux:text>
                            </div>

                            <div>
                                <flux:heading size="md">Tipe Bahan Bakar</flux:heading>
                                <flux:text class="mt-1">{{ $vehicle->fuel_type ?? '-' }}</flux:text>
                            </div>

                            <div>
                                <flux:heading size="md">Kilometer</flux:heading>
                                <flux:text class="mt-1">{{ number_format($vehicle->kilometer, 0, ',', '.') }}</flux:text>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <div class="flex items-center gap-2">
                            <flux:heading size="md">Status:</flux:heading>
                            @if($vehicle->status == 1)
                            <flux:badge icon="check-circle" size="sm" color="green">Available</flux:badge>
                            @else
                            <flux:badge icon="x-circle" size="sm" color="red">Sold</flux:badge>
                            @endif
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-zinc-700 mt-4">
                        <div>
                            <flux:heading size="sm">Created</flux:heading>
                            <flux:text class="text-sm">{{ $vehicle->created_at->format('M d, Y \a\t H:i') }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Last Updated</flux:heading>
                            <flux:text class="text-sm">{{ $vehicle->updated_at->format('M d, Y \a\t H:i') }}</flux:text>
                        </div>
                    </div>
                </div>

                <!-- Registration Information -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Informasi Registrasi & Dokumen</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:heading size="md">Tanggal Registrasi</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->vehicle_registration_date ? Carbon\Carbon::parse($vehicle->vehicle_registration_date)->format('M d, Y') : '-' }}</flux:text>
                        </div>

                        <div>
                            <flux:heading size="md">Tanggal Kadaluarsa</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->vehicle_registration_expiry_date ? Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->format('M d, Y') : '-' }}</flux:text>
                        </div>

                        <div>
                            <flux:heading size="md">Warehouse</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->warehouse?->name ?? '-' }}</flux:text>
                        </div>

                        <div>
                            <flux:heading size="md">File STNK</flux:heading>
                            @if($vehicle->file_stnk && Storage::disk('public')->exists('photos/stnk/'.$vehicle->file_stnk))
                                <flux:text class="mt-1">
                                    <a href="{{ asset('photos/stnk/'.$vehicle->file_stnk) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat STNK</a>
                                </flux:text>
                            @else
                                <flux:text class="mt-1">-</flux:text>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Vehicle Completeness Checklist -->
                @php
                    $equipment = $vehicle->equipment()->where('type', 2)->first();
                @endphp
                @if($equipment)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Kelengkapan Kendaraan</flux:heading>
                    <flux:text class="mb-4">Berikut adalah daftar kelengkapan yang tersedia pada kendaraan ini:</flux:text>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- STNK Asli -->
                        <div class="flex items-center space-x-3 p-3 rounded-lg border @if($equipment->stnk_asli) bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700 @else bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700 @endif">
                            @if($equipment->stnk_asli)
                                <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" />
                                <div>
                                    <flux:text class="font-medium text-green-900 dark:text-green-100">STNK Asli</flux:text>
                                    <flux:text class="text-sm text-green-700 dark:text-green-300">Tersedia</flux:text>
                                </div>
                            @else
                                <flux:icon.x-circle class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" />
                                <div>
                                    <flux:text class="font-medium text-red-900 dark:text-red-100">STNK Asli</flux:text>
                                    <flux:text class="text-sm text-red-700 dark:text-red-300">Tidak tersedia</flux:text>
                                </div>
                            @endif
                        </div>

                        <!-- Kunci Roda -->
                        <div class="flex items-center space-x-3 p-3 rounded-lg border @if($equipment->kunci_roda) bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700 @else bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700 @endif">
                            @if($equipment->kunci_roda)
                                <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" />
                                <div>
                                    <flux:text class="font-medium text-green-900 dark:text-green-100">Kunci Roda</flux:text>
                                    <flux:text class="text-sm text-green-700 dark:text-green-300">Tersedia</flux:text>
                                </div>
                            @else
                                <flux:icon.x-circle class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" />
                                <div>
                                    <flux:text class="font-medium text-red-900 dark:text-red-100">Kunci Roda</flux:text>
                                    <flux:text class="text-sm text-red-700 dark:text-red-300">Tidak tersedia</flux:text>
                                </div>
                            @endif
                        </div>

                        <!-- Ban Serep -->
                        <div class="flex items-center space-x-3 p-3 rounded-lg border @if($equipment->ban_serep) bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700 @else bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700 @endif">
                            @if($equipment->ban_serep)
                                <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" />
                                <div>
                                    <flux:text class="font-medium text-green-900 dark:text-green-100">Ban Serep</flux:text>
                                    <flux:text class="text-sm text-green-700 dark:text-green-300">Tersedia</flux:text>
                                </div>
                            @else
                                <flux:icon.x-circle class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" />
                                <div>
                                    <flux:text class="font-medium text-red-900 dark:text-red-100">Ban Serep</flux:text>
                                    <flux:text class="text-sm text-red-700 dark:text-red-300">Tidak tersedia</flux:text>
                                </div>
                            @endif
                        </div>

                        <!-- Kunci Serep -->
                        <div class="flex items-center space-x-3 p-3 rounded-lg border @if($equipment->kunci_serep) bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700 @else bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700 @endif">
                            @if($equipment->kunci_serep)
                                <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" />
                                <div>
                                    <flux:text class="font-medium text-green-900 dark:text-green-100">Kunci Serep</flux:text>
                                    <flux:text class="text-sm text-green-700 dark:text-green-300">Tersedia</flux:text>
                                </div>
                            @else
                                <flux:icon.x-circle class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" />
                                <div>
                                    <flux:text class="font-medium text-red-900 dark:text-red-100">Kunci Serep</flux:text>
                                    <flux:text class="text-sm text-red-700 dark:text-red-300">Tidak tersedia</flux:text>
                                </div>
                            @endif
                        </div>

                        <!-- Dongkrak -->
                        <div class="flex items-center space-x-3 p-3 rounded-lg border @if($equipment->dongkrak) bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700 @else bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700 @endif">
                            @if($equipment->dongkrak)
                                <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" />
                                <div>
                                    <flux:text class="font-medium text-green-900 dark:text-green-100">Dongkrak</flux:text>
                                    <flux:text class="text-sm text-green-700 dark:text-green-300">Tersedia</flux:text>
                                </div>
                            @else
                                <flux:icon.x-circle class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" />
                                <div>
                                    <flux:text class="font-medium text-red-900 dark:text-red-100">Dongkrak</flux:text>
                                    <flux:text class="text-sm text-red-700 dark:text-red-300">Tidak tersedia</flux:text>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Ringkasan Kelengkapan:</flux:text>
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="flex items-center space-x-1">
                                    <flux:icon.check-circle class="w-4 h-4 text-green-600" />
                                    <span>{{ collect([$equipment->stnk_asli, $equipment->kunci_roda, $equipment->ban_serep, $equipment->kunci_serep, $equipment->dongkrak])->filter()->count() }} tersedia</span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <flux:icon.x-circle class="w-4 h-4 text-red-600" />
                                    <span>{{ collect([$equipment->stnk_asli, $equipment->kunci_roda, $equipment->ban_serep, $equipment->kunci_serep, $equipment->dongkrak])->filter(function($item) { return !$item; })->count() }} tidak tersedia</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Financial Information -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="lg">Informasi Keuangan</flux:heading>
                        <div class="flex items-center gap-2">
                            @if($vehicle->purchasePayments->sum('amount') == 0)
                                <flux:badge icon="x-circle" color="red">Belum Lunas</flux:badge>
                            @elseif($vehicle->purchasePayments->sum('amount') == $vehicle->purchase_price)
                                <flux:badge icon="check-circle" color="green">Lunas</flux:badge>
                            @else
                                <flux:tooltip content="Sisa Pembayaran: Rp {{ number_format($vehicle->purchase_price - $vehicle->purchasePayments->sum('amount'), 0, ',', '.') }}">
                                    <flux:badge icon="exclamation-circle" color="yellow">Pembayaran Parsial</flux:badge>
                                </flux:tooltip>
                            @endif
                            @can('vehicle-purchase-payment.audit')
                                <flux:button variant="filled" size="sm" href="{{ route('purchase-payments.audit') }}?selectedVehicle={{ $vehicle->id }}" wire:navigate icon="document-text" tooltip="Audit Trail">Audit</flux:button>
                            @endcan
                            @can('vehicle-purchase-payment.create')
                                @if($vehicle->purchasePayments->sum('amount') < ($vehicle->purchase_price ?? 0))
                                    <flux:button wire:click="openPurchasePaymentModal" variant="filled" size="sm" icon="plus" class="cursor-pointer" tooltip="Tambah Pembayaran Pembelian">Tambah</flux:button>
                                @endif
                            @endcan
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:heading size="md">Tanggal Pembelian</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->purchase_date ? Carbon\Carbon::parse($vehicle->purchase_date)->format('M d, Y') : '-' }}</flux:text>
                        </div>

                        <div>
                            <flux:heading size="md">Harga Beli</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->purchase_price ? 'Rp ' . number_format($vehicle->purchase_price, 0, ',', '.') : '-' }}</flux:text>
                        </div>

                        <div>
                            <flux:heading size="md">Harga Tunai</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->display_price ? 'Rp ' . number_format($vehicle->display_price, 0, ',', '.') : '-' }}</flux:text>
                        </div>

                        <div>
                            <flux:heading size="md">Harga Kredit</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->loan_price ? 'Rp ' . number_format($vehicle->loan_price, 0, ',', '.') : '-' }}</flux:text>
                        </div>

                        <div>
                            <flux:heading size="md">Biaya Uang Jalan</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->roadside_allowance ? 'Rp ' . number_format($vehicle->roadside_allowance, 0, ',', '.') : '-' }}</flux:text>
                        </div>
                    </div>

                    <!-- Purchase Payments -->
                    @if(auth()->user()->can('vehicle-purchase-payment.view') || auth()->user()->can('vehicle-purchase-payment.create') || auth()->user()->can('vehicle-purchase-payment.edit') || auth()->user()->can('vehicle-purchase-payment.delete') || auth()->user()->can('vehicle-purchase-payment.audit'))
                        @if($vehicle->purchasePayments && $vehicle->purchasePayments->count() > 0)
                            @if($vehicle->purchasePayments->sum('amount') < $vehicle->purchase_price)
                                <flux:callout variant="warning" icon="exclamation-circle" class="mt-4" heading="Sisa Pembayaran: Rp {{ number_format($vehicle->purchase_price - $vehicle->purchasePayments->sum('amount'), 0, ',', '.') }}." />
                            @endif
                            <div class="flex items-center justify-between mt-6 mb-2">
                                <flux:heading size="md">Rincian Pembayaran Pembelian</flux:heading>
                                <flux:text class="text-sm">Total: Rp {{ number_format($vehicle->purchasePayments->sum('amount'), 0) }}</flux:text>
                            </div>
                            <div class="border border-gray-200 dark:border-zinc-700 rounded-lg overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-zinc-700 border-b border-gray-200 dark:border-zinc-700">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-white">Tanggal</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-white">Deskripsi</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-white">Jumlah</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-white">Dokumen</th>
                                            @if(auth()->user()->can('vehicle-purchase-payment.edit') || auth()->user()->can('vehicle-purchase-payment.delete'))
                                            <th class="px-4 py-2 text-center text-sm font-medium text-gray-900 dark:text-white">Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                        @foreach($vehicle->purchasePayments as $payment)
                                        <tr class="bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                                            <td class="px-4 py-1">
                                                <flux:text class="text-sm whitespace-nowrap">
                                                    {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') : '-' }}
                                                </flux:text>
                                            </td>
                                            <td class="px-4 py-1">
                                                <flux:text class="text-sm whitespace-nowrap md:whitespace-normal">
                                                    {{ $payment->description ?? '-' }}
                                                </flux:text>
                                            </td>
                                            <td class="px-4 py-1">
                                                <flux:text class="text-sm font-medium whitespace-nowrap">
                                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                                </flux:text>
                                            </td>
                                            <td class="px-4 py-1">
                                                @if($payment->document)
                                                    @php
                                                        $files = explode(',', $payment->document);
                                                    @endphp
                                                    <div class="flex space-x-1 space-y-1">
                                                        @foreach($files as $file)
                                                            @php
                                                                $fileName = trim($file);
                                                                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                            @endphp
                                                            <a href="{{ asset('documents/purchase-payments/' . $fileName) }}" target="_blank" class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                                                @if($extension === 'pdf')
                                                                    <flux:icon.document class="w-4 h-4" />
                                                                @elseif(in_array($extension, ['jpg', 'jpeg', 'png']))
                                                                    <flux:icon.photo class="w-4 h-4" />
                                                                @else
                                                                    <flux:icon.document class="w-4 h-4" />
                                                                @endif
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">-</flux:text>
                                                @endif
                                            </td>
                                            @if(auth()->user()->can('vehicle-purchase-payment.edit') || auth()->user()->can('vehicle-purchase-payment.delete'))
                                            <td class="px-4 py-1 text-center">
                                                <div class="flex items-center justify-center space-x-1">
                                                @can('vehicle-purchase-payment.edit')
                                                <flux:button
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="pencil-square"
                                                    tooltip="Edit"
                                                    wire:click="editPurchasePayment({{ $payment->id }})"
                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer"
                                                ></flux:button>
                                                @endcan
                                                @can('vehicle-purchase-payment.delete')
                                                <flux:modal.trigger name="delete-purchase-payment-{{ $payment->id }}">
                                                    <flux:button
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="trash"
                                                        tooltip="Hapus"
                                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 cursor-pointer"
                                                    ></flux:button>
                                                </flux:modal.trigger>
                                                @endcan
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <flux:callout variant="danger" icon="x-circle" class="mt-4" heading="Belum ada pembayaran pembelian untuk kendaraan ini. Silakan tambahkan pembayaran pembelian." />
                        @endif
                    @endif

                    <!-- Delete Purchase Payment Confirmation Modals -->
                    @if($vehicle->purchasePayments && $vehicle->purchasePayments->count() > 0)
                        @foreach($vehicle->purchasePayments as $payment)
                        <flux:modal name="delete-purchase-payment-{{ $payment->id }}" class="min-w-88">
                            <div class="space-y-6">
                                <div>
                                    <flux:heading size="lg">Hapus Pembayaran Pembelian?</flux:heading>
                                    <flux:text class="mt-2">
                                        Apakah Anda yakin ingin menghapus pembayaran pembelian ini? Tindakan ini tidak dapat dibatalkan.
                                    </flux:text>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <flux:modal.close>
                                        <flux:button variant="ghost">Batal</flux:button>
                                    </flux:modal.close>
                                    <flux:button
                                        wire:click="deletePurchasePayment({{ $payment->id }})"
                                        variant="danger"
                                        class="cursor-pointer"
                                    >
                                        Hapus Pembayaran
                                    </flux:button>
                                </div>
                            </div>
                        </flux:modal>
                        @endforeach
                    @endif
                </div>

                <!-- Buyer Information (only shown when status is Sold) -->
                @if($vehicle->status == 0)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Informasi Pembeli</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:heading size="md">Tanggal Penjualan</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->selling_date ? Carbon\Carbon::parse($vehicle->selling_date)->format('M d, Y') : '-' }}</flux:text>
                        </div>

                        <div>
                            <flux:heading size="md">Harga Penjualan</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->selling_price ? 'Rp ' . number_format($vehicle->selling_price, 0, ',', '.') : '-' }}</flux:text>
                        </div>

                        <div>
                            <flux:heading size="md">Nama Pembeli</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->buyer_name ?? '-' }}</flux:text>
                        </div>

                        <div>
                            <flux:heading size="md">Nomor Telepon</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->buyer_phone ?? '-' }}</flux:text>
                        </div>

                        <div class="md:col-span-2">
                            <flux:heading size="md">Alamat Pembeli</flux:heading>
                            <flux:text class="mt-1">{{ $vehicle->buyer_address ?? '-' }}</flux:text>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Commission Information -->
                @if(auth()->user()->can('vehicle-commission.view') || auth()->user()->can('vehicle-commission.create') || auth()->user()->can('vehicle-commission.edit') || auth()->user()->can('vehicle-commission.delete') || auth()->user()->can('vehicle-commission.audit'))
                    @if($vehicle->commissions && $vehicle->commissions->count() > 0)
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <flux:heading size="lg">Komisi</flux:heading>
                            <div class="flex items-center gap-2">
                                @can('vehicle-commission.audit')
                                    <flux:button variant="filled" size="sm" href="{{ route('commissions.audit') }}?selectedVehicle={{ $vehicle->id }}" wire:navigate icon="document-text" tooltip="Audit Trail">Audit</flux:button>
                                @endcan
                                @if($vehicle->commissions->where('type', 2)->count() < 4)
                                    <flux:button wire:click="openCommissionModal" size="sm" variant="filled" icon="plus" class="cursor-pointer" tooltip="Tambah Komisi">Tambah</flux:button>
                                @endif
                            </div>
                        </div>

                        <!-- Purchase Commissions -->
                        @if($vehicle->commissions->where('type', 2)->count() > 0)
                        <div class="mb-8">
                            <div class="flex items-center justify-between">
                                <flux:heading size="md" class="mb-2">Komisi Pembelian (Max 4)</flux:heading>
                                <flux:text class="text-sm">Total: Rp {{ number_format($vehicle->commissions->where('type', 2)->sum('amount'), 0) }}</flux:text>
                            </div>
                            <div class="border border-gray-200 dark:border-zinc-700 rounded-lg overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-zinc-700 border-b border-gray-200 dark:border-zinc-700">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-white">Tanggal</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-white">Deskripsi</th>
                                            <th class="px-4 py-2 text-right text-sm font-medium text-gray-900 dark:text-white">Jumlah</th>
                                            @if(auth()->user()->can('vehicle-commission.edit') || auth()->user()->can('vehicle-commission.delete'))
                                            <th class="px-4 py-2 text-center text-sm font-medium text-gray-900 dark:text-white">Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                        @foreach($vehicle->commissions->where('type', 2)->sortByDesc('commission_date') as $commission)
                                        <tr class="bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                                            <td class="px-4 py-1">
                                                <flux:text class="text-sm whitespace-nowrap">
                                                    {{ Carbon\Carbon::parse($commission->commission_date)->format('d-m-Y') }}
                                                </flux:text>
                                            </td>
                                            <td class="px-4 py-1">
                                                <flux:text class="text-sm whitespace-nowrap">
                                                    {{ $commission->description ?? '-' }}
                                                </flux:text>
                                            </td>
                                            <td class="px-4 py-1 text-right">
                                                <flux:text class="text-sm whitespace-nowrap">
                                                    Rp {{ number_format($commission->amount, 0, ',', '.') }}
                                                </flux:text>
                                            </td>
                                            @if(auth()->user()->can('vehicle-commission.edit') || auth()->user()->can('vehicle-commission.delete'))
                                            <td class="px-4 py-1 text-center">
                                                <div class="flex items-center justify-center space-x-1">
                                                    @can('vehicle-commission.edit')
                                                    <flux:button
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="pencil-square"
                                                        tooltip="Edit"
                                                        wire:click="openEditCommissionModal({{ $commission->id }})"
                                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer"
                                                    ></flux:button>
                                                    @endcan
                                                    @can('vehicle-commission.delete')
                                                    <flux:modal.trigger name="delete-commission-{{ $commission->id }}">
                                                        <flux:button
                                                            variant="ghost"
                                                            size="sm"
                                                            icon="trash"
                                                            tooltip="Delete"
                                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 cursor-pointer"
                                                        ></flux:button>
                                                    </flux:modal.trigger>
                                                    @endcan
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Delete Commission Confirmation Modals for Purchase -->
                        @foreach($vehicle->commissions->where('type', 2) as $commission)
                        <flux:modal name="delete-commission-{{ $commission->id }}" class="min-w-88">
                            <div class="space-y-6">
                                <div>
                                    <flux:heading size="lg">Hapus Komisi?</flux:heading>
                                    <flux:text class="mt-2">
                                        Anda akan menghapus komisi pembelian dengan jumlah Rp {{ number_format($commission->amount, 0, ',', '.') }}.<br>
                                        Tindakan ini tidak dapat dibatalkan.
                                    </flux:text>
                                </div>
                                <div class="flex gap-2">
                                    <flux:spacer />
                                    <flux:modal.close>
                                        <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
                                    </flux:modal.close>
                                    <flux:button
                                        wire:click="deleteCommission({{ $commission->id }})"
                                        variant="danger"
                                        class="cursor-pointer"
                                    >
                                        Hapus Komisi
                                    </flux:button>
                                </div>
                            </div>
                        </flux:modal>
                        @endforeach
                        @endif

                        <!-- Sales Commissions -->
                        @if($vehicle->commissions->where('type', 1)->count() > 0)
                        <div class="flex items-center justify-between">
                            <flux:heading size="md" class="mb-2">Komisi Penjualan</flux:heading>
                            <flux:text class="text-sm">Total: Rp {{ number_format($vehicle->commissions->where('type', 1)->sum('amount'), 0) }}</flux:text>
                        </div>
                        <div class="border border-gray-200 dark:border-zinc-700 rounded-lg overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-zinc-700 border-b border-gray-200 dark:border-zinc-700">
                                    <tr></tr>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-white">Tanggal</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-white">Deskripsi</th>
                                        <th class="px-4 py-2 text-right text-sm font-medium text-gray-900 dark:text-white">Jumlah</th>
                                        @if(auth()->user()->can('vehicle-commission.edit') || auth()->user()->can('vehicle-commission.delete'))
                                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-900 dark:text-white">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                    @foreach($vehicle->commissions->where('type', 1)->sortByDesc('commission_date') as $commission)
                                    <tr class="bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                                        <td class="px-4 py-1">
                                            <flux:text class="text-sm whitespace-nowrap">
                                                {{ Carbon\Carbon::parse($commission->commission_date)->format('d-m-Y') }}
                                            </flux:text>
                                        </td>
                                        <td class="px-4 py-1">
                                            <flux:text class="text-sm whitespace-nowrap">
                                                {{ $commission->description ?? '-' }}
                                            </flux:text>
                                        </td>
                                        <td class="px-4 py-1 text-right">
                                            <flux:text class="text-sm whitespace-nowrap">
                                                Rp {{ number_format($commission->amount, 0, ',', '.') }}
                                            </flux:text>
                                        </td>
                                        @if(auth()->user()->can('vehicle-commission.edit') || auth()->user()->can('vehicle-commission.delete'))
                                        <td class="px-4 py-1 text-center">
                                            <div class="flex items-center justify-center space-x-1">
                                                @can('vehicle-commission.edit')
                                                <flux:button
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="pencil-square"
                                                    tooltip="Edit"
                                                    wire:click="openEditCommissionModal({{ $commission->id }})"
                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer"
                                                ></flux:button>
                                                @endcan
                                                @can('vehicle-commission.delete')
                                                <flux:modal.trigger name="delete-commission-{{ $commission->id }}">
                                                    <flux:button
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="trash"
                                                        tooltip="Delete"
                                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 cursor-pointer"
                                                    ></flux:button>
                                                </flux:modal.trigger>
                                                @endcan
                                            </div>
                                            </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Delete Commission Confirmation Modals for Sales -->
                        @foreach($vehicle->commissions->where('type', 1) as $commission)
                        <flux:modal name="delete-commission-{{ $commission->id }}" class="min-w-88">
                            <div class="space-y-6">
                                <div>
                                    <flux:heading size="lg">Hapus Komisi?</flux:heading>
                                    <flux:text class="mt-2">
                                        Anda akan menghapus komisi penjualan dengan jumlah Rp {{ number_format($commission->amount, 0, ',', '.') }}.<br>
                                        Tindakan ini tidak dapat dibatalkan.
                                    </flux:text>
                                </div>
                                <div class="flex gap-2">
                                    <flux:spacer />
                                    <flux:modal.close>
                                        <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
                                    </flux:modal.close>
                                    <flux:button
                                        wire:click="deleteCommission({{ $commission->id }})"
                                        variant="danger"
                                        class="cursor-pointer"
                                    >
                                        Hapus Komisi
                                    </flux:button>
                                </div>
                            </div>
                        </flux:modal>
                        @endforeach
                        @endif
                    </div>
                    @endif
                @endif

                <!-- Loan Calculation -->
                @if(auth()->user()->can('vehicle-loan-calculation.view') || auth()->user()->can('vehicle-loan-calculation.create') || auth()->user()->can('vehicle-loan-calculation.edit') || auth()->user()->can('vehicle-loan-calculation.delete') || auth()->user()->can('vehicle-loan-calculation.audit'))
                    @if($vehicle->loanCalculations && $vehicle->loanCalculations->count() > 0)
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <flux:heading size="lg">Perhitungan Kredit</flux:heading>
                            <div class="flex items-center gap-2">
                                @can('vehicle-loan-calculation.audit')
                                    <flux:button variant="filled" size="sm" href="{{ route('loan-calculations.audit') }}?selectedVehicle={{ $vehicle->id }}" wire:navigate icon="document-text" tooltip="Audit Trail">Audit</flux:button>
                                @endcan
                                @can('vehicle-loan-calculation.create')
                                    <flux:button wire:click="openLoanCalculationModal" size="sm" variant="filled" icon="plus" class="cursor-pointer" tooltip="Tambah Perhitungan Kredit">Tambah</flux:button>
                                @endcan
                            </div>
                        </div>

                        <div class="border border-gray-200 dark:border-zinc-700 rounded-lg overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-zinc-700 border-b border-gray-200 dark:border-zinc-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-white">Leasing</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-white">Deskripsi</th>
                                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-900 dark:text-white">Tanggal Dibuat</th>
                                        @if(auth()->user()->can('vehicle-loan-calculation.edit') || auth()->user()->can('vehicle-loan-calculation.delete'))
                                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-900 dark:text-white">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                    @foreach($vehicle->loanCalculations->sortBy('leasing.name') as $loanCalculation)
                                    <tr class="bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                                        <td class="px-4 py-1">
                                            <flux:text class="text-sm whitespace-nowrap">
                                                {{ $loanCalculation->leasing->name ?? '-' }}
                                            </flux:text>
                                        </td>
                                        <td class="px-4 py-1">
                                            <flux:text class="text-sm">
                                                {{ $loanCalculation->description ?? '-' }}
                                            </flux:text>
                                        </td>
                                        <td class="px-4 py-1 text-center">
                                            <flux:text class="text-sm whitespace-nowrap">
                                                {{ Carbon\Carbon::parse($loanCalculation->created_at)->format('d-m-Y') }}
                                            </flux:text>
                                        </td>
                                        @if(auth()->user()->can('vehicle-loan-calculation.edit') || auth()->user()->can('vehicle-loan-calculation.delete'))
                                        <td class="px-4 py-1 text-center">
                                            <div class="flex items-center justify-center space-x-1">
                                                @can('vehicle-loan-calculation.edit')
                                                <flux:button
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="pencil-square"
                                                    tooltip="Edit"
                                                    wire:click="openEditLoanCalculationModal({{ $loanCalculation->id }})"
                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer"
                                                ></flux:button>
                                                @endcan
                                                @can('vehicle-loan-calculation.delete')
                                                <flux:modal.trigger name="delete-loan-calculation-{{ $loanCalculation->id }}">
                                                    <flux:button
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="trash"
                                                        tooltip="Delete"
                                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 cursor-pointer"
                                                    ></flux:button>
                                                </flux:modal.trigger>
                                                @endcan
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Delete Loan Calculation Confirmation Modals -->
                        @foreach($vehicle->loanCalculations as $loanCalculation)
                        <flux:modal name="delete-loan-calculation-{{ $loanCalculation->id }}" class="min-w-88">
                            <div class="space-y-6">
                                <div>
                                    <flux:heading size="lg">Hapus Perhitungan Kredit?</flux:heading>
                                    <flux:text class="mt-2">
                                        Apakah Anda yakin ingin menghapus perhitungan kredit ini? Tindakan ini tidak dapat dibatalkan.
                                    </flux:text>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <flux:modal.close>
                                        <flux:button variant="ghost">Batal</flux:button>
                                    </flux:modal.close>
                                    <flux:button
                                        wire:click="deleteLoanCalculation({{ $loanCalculation->id }})"
                                        variant="danger"
                                        class="cursor-pointer"
                                    >
                                        Hapus Perhitungan Kredit
                                    </flux:button>
                                </div>
                            </div>
                        </flux:modal>
                        @endforeach
                    </div>
                    @endif
                @endif

                <!-- Description -->
                @if($vehicle->description)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Deskripsi</flux:heading>
                    <flux:text>
                        @php
                            $allowed = "<p><b><i><u><br><a><strong><em><ul><ol><li><span><div><img>";
                            echo strip_tags($vehicle->description, $allowed);
                        @endphp
                    </flux:text>
                </div>
                @endif

                <!-- Vehicle Images -->
                @if($vehicle->images && $vehicle->images->count() > 0)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="lg">Foto Kendaraan</flux:heading>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $vehicle->images->count() }} foto
                        </span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($vehicle->images as $image)
                        <div class="relative group">
                            <div class="aspect-square bg-gray-100 dark:bg-zinc-700 rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-600">
                                <img src="{{ asset('photos/vehicles/' . $image->image) }}"
                                     alt="Vehicle Photo"
                                     class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-200"
                                     onclick="openImageModal('{{ asset('photos/vehicles/' . $image->image) }}', '{{ $vehicle->police_number }} - Photo {{ $loop->iteration }}')">
                            </div>
                            <!-- Download Button -->
                            <a href="{{ asset('photos/vehicles/' . $image->image) }}"
                               download="{{ $vehicle->police_number }}_photo_{{ $loop->iteration }}.jpg"
                               class="absolute top-2 right-2 bg-black/50 hover:bg-black/70 text-white rounded-full w-8 h-8 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                               title="Download photo">
                                <flux:icon.arrow-down-tray class="w-4 h-4" />
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar Information -->
            <div class="space-y-6">
                <!-- Vehicle Status Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Vehicle Status</flux:heading>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <flux:text>Current Status</flux:text>
                            @if($vehicle->status == 1)
                            <flux:badge icon="check-circle" size="sm" color="green">Available</flux:badge>
                            @else
                            <flux:badge icon="x-circle" size="sm" color="red">Sold</flux:badge>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <flux:text>Vehicle Age</flux:text>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                @if($vehicle->vehicle_registration_date)
                                    @php
                                        $registrationDate = \Carbon\Carbon::parse($vehicle->vehicle_registration_date);
                                        $vehicleAgeDate = \Carbon\Carbon::create($vehicle->year, $registrationDate->month, $registrationDate->day);
                                        $ageInYears = $vehicleAgeDate->diffInYears(now());
                                    @endphp
                                    {{ number_format($ageInYears, 1, ',', '.') }} years
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>

                        @if($vehicle->vehicle_registration_date)
                        <div class="flex items-center justify-between">
                            <flux:text>Registration</flux:text>
                            @php
                                $registrationDate = Carbon\Carbon::parse($vehicle->vehicle_registration_date);
                                $isPast = $registrationDate->isPast();
                                $daysLeft = $registrationDate->diffInDays() * -1;
                                $registrationClasses = $isPast
                                    ? 'text-red-600 dark:text-red-400'
                                    : ($daysLeft < 30
                                        ? 'text-yellow-600 dark:text-yellow-400'
                                        : 'text-green-600 dark:text-green-400');
                                $registrationText = $isPast
                                    ? 'Expired'
                                    : number_format($daysLeft, 0, ',', '.') . ' days left';
                            @endphp
                            <span class="text-sm font-medium {{ $registrationClasses }}">
                                {{ $registrationText }}
                            </span>
                        </div>
                        @endif

                        @if($vehicle->vehicle_registration_expiry_date)
                        <div class="flex items-center justify-between">
                            <flux:text>Registration Tax</flux:text>
                            @php
                                $expiryDate = Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date);
                                $isExpired = $expiryDate->isPast();
                                $daysLeftExpiry = $expiryDate->diffInDays() * -1;
                                $expiryClasses = $isExpired
                                    ? 'text-red-600 dark:text-red-400'
                                    : ($daysLeftExpiry < 30
                                        ? 'text-yellow-600 dark:text-yellow-400'
                                        : 'text-green-600 dark:text-green-400');
                                $expiryText = $isExpired
                                    ? 'Expired'
                                    : number_format($daysLeftExpiry, 0, ',', '.') . ' days left';
                            @endphp
                            <span class="text-sm font-medium {{ $expiryClasses }}">
                                {{ $expiryText }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                @can('vehicle-modal.view')
                    <!-- Financial Overview -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                        <flux:heading size="lg" class="mb-4">Financial Overview</flux:heading>

                        <div class="space-y-4">
                            @if($vehicle->purchase_price)
                            <div class="flex items-center justify-between">
                                <flux:text>Harga Pembelian</flux:text>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    Rp {{ number_format($vehicle->purchase_price, 0, ',', '.') }}
                                </span>
                            </div>
                            @endif

                            @if($costSummary['total'] > 0)
                            <div class="flex items-center justify-between">
                                <flux:text>Total Biaya</flux:text>
                                <span class="text-lg font-bold text-orange-600 dark:text-orange-400">
                                    Rp {{ number_format($costSummary['total'], 0, ',', '.') }}
                                </span>
                            </div>
                            @endif

                            @if($vehicle->selling_price)
                            <div class="flex items-center justify-between">
                                <flux:text>Harga Jual</flux:text>
                                <span class="text-lg font-bold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($vehicle->selling_price, 0, ',', '.') }}
                                </span>
                            </div>

                            @if($vehicle->purchase_price && $vehicle->selling_price)
                            <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-zinc-700">
                                <flux:text>Keuntungan/Kerugian</flux:text>
                                @php
                                    $totalModal = $vehicle->purchase_price + $costSummary['total'];
                                    $profit = $vehicle->selling_price - $totalModal;
                                    $profitClasses = $vehicle->selling_price > $totalModal
                                        ? 'text-green-600 dark:text-green-400'
                                        : 'text-red-600 dark:text-red-400';
                                @endphp
                                <span class="text-lg font-bold {{ $profitClasses }}">
                                    {{ $profit > 0 ? '+' : '' }}Rp {{ number_format($profit, 0, ',', '.') }}
                                </span>
                            </div>
                            @endif
                            @endif

                            <div class="flex items-center justify-between">
                                <flux:text>Nilai Saat Ini</flux:text>
                                <span class="text-sm text-gray-600 dark:text-zinc-400">
                                    @if($vehicle->selling_price)
                                        Berdasarkan harga jual
                                    @elseif($vehicle->purchase_price)
                                        Berdasarkan harga beli
                                    @else
                                        Tidak tersedia
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @endcan

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Quick Actions</flux:heading>

                    <div class="space-y-2">
                        @can('vehicle.edit')
                            <flux:button size="sm" href="{{ route('vehicles.edit', $vehicle->id) }}" wire:navigate icon="pencil-square" class="w-full justify-start p-6">
                                <div class="flex flex-col items-start">
                                    <span>Edit Vehicle</span>
                                    <span class="text-xs text-gray-500 dark:text-zinc-400">Modify vehicle details</span>
                                </div>
                            </flux:button>
                        @endcan

                        @can('vehicle.view')
                            <flux:button size="sm" href="{{ route('vehicles.audit') }}?selectedVehicle={{ $vehicle->id }}" wire:navigate icon="document-text" class="w-full justify-start p-6">
                                <div class="flex flex-col items-start">
                                    <span>View Audit Trail</span>
                                    <span class="text-xs text-gray-500 dark:text-zinc-400">Activity history</span>
                                </div>
                            </flux:button>
                        @endcan

                        @can('vehicle.create')
                            <flux:button size="sm" href="{{ route('vehicles.create') }}" wire:navigate icon="plus" class="w-full justify-start p-6">
                                <div class="flex flex-col items-start">
                                    <span>Create New Vehicle</span>
                                    <span class="text-xs text-gray-500 dark:text-zinc-400">Add another vehicle</span>
                                </div>
                            </flux:button>
                        @endcan

                        @if($vehicle->file_stnk && Storage::disk('public')->exists('photos/stnk/'. $vehicle->file_stnk))
                        <flux:button size="sm" href="{{ asset('photos/stnk/'.$vehicle->file_stnk) }}" target="_blank" icon="document" class="w-full justify-start p-6">
                            <div class="flex flex-col items-start">
                                <span>View STNK</span>
                                <span class="text-xs text-gray-500 dark:text-zinc-400">Registration document</span>
                            </div>
                        </flux:button>
                        @endif

                        @if($vehicle->status == 0)
                            @can('vehicle-receipt.print')
                                <flux:button size="sm" wire:click="openBuyerModal" icon="printer" class="w-full justify-start p-6 cursor-pointer" :loading="false">
                                    <div class="flex flex-col items-start">
                                        <span>Print Receipt</span>
                                        <span class="text-xs text-gray-500 dark:text-zinc-400">Download sales receipt</span>
                                    </div>
                                </flux:button>
                            @endcan
                        @endif

                        @can('vehicle-loan-calculation.create')
                            <flux:button wire:click="openLoanCalculationModal" size="sm" icon="calculator" class="w-full justify-start p-6 cursor-pointer" :loading="false">
                                <div class="flex flex-col items-start">
                                    <span>Create New Loan Calculation</span>
                                    <span class="text-xs text-gray-500 dark:text-zinc-400">Add a new loan calculation</span>
                                </div>
                            </flux:button>
                        @endcan

                        @if($vehicle->commissions->where('type', 2)->count() < 4)
                            @can('vehicle-commission.create')
                                <flux:button wire:click="openCommissionModal" size="sm" icon="currency-dollar" class="w-full justify-start p-6 cursor-pointer" :loading="false">
                                    <div class="flex flex-col items-start">
                                        <span>Create New Commission</span>
                                        <span class="text-xs text-gray-500 dark:text-zinc-400">Add a new commission (Max 4)</span>
                                    </div>
                                </flux:button>
                            @endcan
                        @endif
                    </div>
                </div>

                <!-- Vehicle Timeline -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Timeline</flux:heading>

                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div>
                                <flux:text class="text-sm font-medium">Vehicle Created</flux:text>
                                <flux:text class="text-xs text-gray-600 dark:text-zinc-400">{{ $vehicle->created_at->format('M d, Y \a\t H:i') }}</flux:text>
                            </div>
                        </div>

                        @if($vehicle->purchase_date)
                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div>
                                <flux:text class="text-sm font-medium">Purchase Date</flux:text>
                                <flux:text class="text-xs text-gray-600 dark:text-zinc-400">{{ Carbon\Carbon::parse($vehicle->purchase_date)->format('M d, Y') }}</flux:text>
                            </div>
                        </div>
                        @endif

                        @if($vehicle->selling_date)
                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                            <div>
                                <flux:text class="text-sm font-medium">Sold</flux:text>
                                <flux:text class="text-xs text-gray-600 dark:text-zinc-400">{{ Carbon\Carbon::parse($vehicle->selling_date)->format('M d, Y') }}</flux:text>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-2 h-2 bg-gray-400 rounded-full mt-2"></div>
                            <div>
                                <flux:text class="text-sm font-medium">Last Updated</flux:text>
                                <flux:text class="text-xs text-gray-600 dark:text-zinc-400">{{ $vehicle->updated_at->format('M d, Y \a\t H:i') }}</flux:text>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Summary -->
                @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="lg">Recent Activity</flux:heading>
                        @can('vehicle.view')
                            <flux:button size="sm" variant="filled" href="{{ route('vehicles.audit') }}?selectedVehicle={{ $vehicle->id }}" wire:navigate>
                                View All Activities
                            </flux:button>
                        @endcan
                    </div>

                    @if($recentActivities && $recentActivities->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentActivities as $activity)
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-zinc-700/50 rounded-lg">
                                <div class="shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                                        @switch($activity->description)
                                            @case('created vehicle')
                                                <flux:icon.plus class="w-4 h-4 text-green-600 dark:text-green-400" />
                                                @break
                                            @case('updated vehicle')
                                                <flux:icon.pencil-square class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                                                @break
                                            @case('deleted vehicle')
                                                <flux:icon.trash class="w-4 h-4 text-red-600 dark:text-red-400" />
                                                @break
                                            @default
                                                <flux:icon.document-text class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        @endswitch
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <flux:text class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $activity->description }}
                                    </flux:text>
                                    <div class="flex items-center space-x-2 mt-1">
                                        @if($activity->causer)
                                            <flux:text class="text-xs text-gray-600 dark:text-zinc-400">
                                                by {{ $activity->causer->name }}
                                            </flux:text>
                                        @endif
                                        <span class="text-xs text-gray-400 dark:text-zinc-400">•</span>
                                        <flux:text class="text-xs text-gray-400 dark:text-zinc-400">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </flux:text>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <flux:icon.document-text class="mx-auto h-8 w-8 text-gray-400 dark:text-gray-600" />
                            <flux:text class="mt-2 text-sm text-gray-600 dark:text-zinc-400">
                                No recent activities recorded for this vehicle.
                            </flux:text>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        @can('vehicle-modal.view')
            <!-- Rincian Modal Mobil -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mt-6">
                <flux:heading size="lg" class="mb-4">Rincian Modal Mobil</flux:heading>

            @if($costs && $costs->count() > 0)
                <div class="space-y-4">
                    <!-- Cost Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <flux:text class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Cost</flux:text>
                            <flux:text class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                                Rp {{ number_format($costSummary['total'], 0, ',', '.') }}
                            </flux:text>
                        </div>

                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <flux:text class="text-sm font-medium text-green-600 dark:text-green-400">Service & Parts</flux:text>
                            <flux:text class="text-2xl font-bold text-green-900 dark:text-green-100">
                                Rp {{ number_format($costSummary['service_parts'], 0, ',', '.') }}
                            </flux:text>
                        </div>

                        <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                            <flux:text class="text-sm font-medium text-orange-600 dark:text-orange-400">Other Costs</flux:text>
                            <flux:text class="text-2xl font-bold text-orange-900 dark:text-orange-100">
                                Rp {{ number_format($costSummary['other_cost'], 0, ',', '.') }}
                            </flux:text>
                        </div>

                        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <flux:text class="text-sm font-medium text-purple-600 dark:text-purple-400">Profit Margin</flux:text>
                                <flux:badge size="sm" color="purple">{{ number_format($priceAnalysis['display_profit_margin'], 1, ',', '.') }}%</flux:badge>
                            </div>
                            <flux:text class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                                Rp {{ number_format($priceAnalysis['display_price_difference'], 0, ',', '.') }}
                            </flux:text>
                        </div>
                    </div>

                        <!-- Price Analysis & Recommendation -->
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-zinc-700/50 rounded-lg border border-gray-200 dark:border-zinc-600">
                            <flux:heading size="md" class="mb-4 text-gray-900 dark:text-white">Analisis Harga Jual</flux:heading>

                            @php
                                $gridClasses = $priceAnalysis['has_selling_price'] ? 'md:grid-cols-3' : 'md:grid-cols-2';
                            @endphp
                            <div class="grid grid-cols-1 {{ $gridClasses }} gap-4 mb-4">
                                <div class="space-y-2">
                                    <flux:heading size="sm" class="text-gray-900 dark:text-white mb-3">Modal & Cost</flux:heading>
                                    <div class="flex justify-between">
                                        <flux:text class="text-sm">Harga Beli:</flux:text>
                                        <flux:text class="text-sm font-medium">Rp {{ number_format($priceAnalysis['purchase_price'], 0, ',', '.') }}</flux:text>
                                    </div>
                                    <div class="flex justify-between">
                                        <flux:text class="text-sm">Total Cost (All):</flux:text>
                                        <flux:text class="text-sm font-medium">Rp {{ number_format($priceAnalysis['total_cost_all'], 0, ',', '.') }}</flux:text>
                                    </div>
                                    <div class="flex justify-between">
                                        <flux:text class="text-sm">Total Cost (Approved):</flux:text>
                                        <flux:text class="text-sm font-medium">Rp {{ number_format($priceAnalysis['total_cost_approved'], 0, ',', '.') }}</flux:text>
                                    </div>
                                    <div class="flex justify-between border-t border-gray-300 dark:border-zinc-600 pt-2">
                                        <flux:text class="text-sm font-medium text-gray-900 dark:text-white">Total Modal:</flux:text>
                                        <flux:text class="text-sm font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($priceAnalysis['recommended_min_price'], 0, ',', '.') }}</flux:text>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <flux:heading size="sm" class="text-gray-900 dark:text-white mb-3">Harga Display</flux:heading>
                                    <div class="flex justify-between">
                                        <flux:text class="text-sm">Harga Jual Display:</flux:text>
                                        <flux:text class="text-sm font-medium">Rp {{ number_format($priceAnalysis['display_price'], 0, ',', '.') }}</flux:text>
                                    </div>
                                    <div class="flex justify-between">
                                        <flux:text class="text-sm">Selisih vs Modal:</flux:text>
                                        @php
                                            $differenceClasses = $priceAnalysis['display_price_difference'] >= 0
                                                ? 'text-green-600 dark:text-green-400'
                                                : 'text-red-600 dark:text-red-400';
                                            $differencePrefix = $priceAnalysis['display_price_difference'] >= 0 ? '+' : '';
                                        @endphp
                                        <flux:text class="text-sm font-medium {{ $differenceClasses }}">
                                            {{ $differencePrefix }}Rp {{ number_format($priceAnalysis['display_price_difference'], 0, ',', '.') }}
                                        </flux:text>
                                    </div>
                                    <div class="flex justify-between">
                                        <flux:text class="text-sm">Margin Keuntungan:</flux:text>
                                        @php
                                            $marginClasses = $priceAnalysis['display_profit_margin'] >= 20
                                                ? 'text-green-600 dark:text-green-400'
                                                : ($priceAnalysis['display_profit_margin'] >= 10
                                                    ? 'text-yellow-600 dark:text-yellow-400'
                                                    : 'text-red-600 dark:text-red-400');
                                        @endphp
                                        <flux:text class="text-sm font-medium {{ $marginClasses }}">
                                            {{ number_format($priceAnalysis['display_profit_margin'], 1, ',', '.') }}%
                                        </flux:text>
                                    </div>
                                </div>

                                @if($priceAnalysis['has_selling_price'])
                                <div class="space-y-2">
                                    <flux:heading size="sm" class="text-gray-900 dark:text-white mb-3">Harga Aktual Terjual</flux:heading>
                                    <div class="flex justify-between">
                                        <flux:text class="text-sm">Harga Jual Aktual:</flux:text>
                                        <flux:text class="text-sm font-medium text-purple-600 dark:text-purple-400">Rp {{ number_format($priceAnalysis['selling_price'], 0, ',', '.') }}</flux:text>
                                    </div>
                                    <div class="flex justify-between">
                                        <flux:text class="text-sm">Selisih vs Modal:</flux:text>
                                        @php
                                            $sellingDifferenceClasses = $priceAnalysis['selling_price_difference'] >= 0
                                                ? 'text-green-600 dark:text-green-400'
                                                : 'text-red-600 dark:text-red-400';
                                            $sellingDifferencePrefix = $priceAnalysis['selling_price_difference'] >= 0 ? '+' : '';
                                        @endphp
                                        <flux:text class="text-sm font-medium {{ $sellingDifferenceClasses }}">
                                            {{ $sellingDifferencePrefix }}Rp {{ number_format($priceAnalysis['selling_price_difference'], 0, ',', '.') }}
                                        </flux:text>
                                    </div>
                                    <div class="flex justify-between">
                                        <flux:text class="text-sm">Margin Keuntungan:</flux:text>
                                        @php
                                            $sellingMarginClasses = $priceAnalysis['selling_profit_margin'] >= 20
                                                ? 'text-green-600 dark:text-green-400'
                                                : ($priceAnalysis['selling_profit_margin'] >= 10
                                                    ? 'text-yellow-600 dark:text-yellow-400'
                                                    : 'text-red-600 dark:text-red-400');
                                        @endphp
                                        <flux:text class="text-sm font-medium {{ $sellingMarginClasses }}">
                                            {{ number_format($priceAnalysis['selling_profit_margin'], 1, ',', '.') }}%
                                        </flux:text>
                                    </div>
                                    <div class="flex justify-between">
                                        <flux:text class="text-sm">Gap Display vs Aktual:</flux:text>
                                        @php
                                            $gapClasses = $priceAnalysis['price_vs_selling_gap'] > 0
                                                ? 'text-orange-600 dark:text-orange-400'
                                                : ($priceAnalysis['price_vs_selling_gap'] < 0
                                                    ? 'text-blue-600 dark:text-blue-400'
                                                    : 'text-gray-600 dark:text-zinc-400');
                                            $gapText = $priceAnalysis['price_vs_selling_gap'] > 0
                                                ? '-Rp ' . number_format(abs($priceAnalysis['price_vs_selling_gap']), 0, ',', '.')
                                                : ($priceAnalysis['price_vs_selling_gap'] < 0
                                                    ? '+Rp ' . number_format(abs($priceAnalysis['price_vs_selling_gap']), 0, ',', '.')
                                                    : 'Rp 0');
                                        @endphp
                                        <flux:text class="text-sm font-medium {{ $gapClasses }}">
                                            {{ $gapText }}
                                        </flux:text>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Status & Recommendation -->
                            <div class="border-t border-gray-300 dark:border-zinc-600 pt-4">
                                @if($priceAnalysis['is_display_price_correct'])
                                    <div class="flex items-center space-x-2">
                                        <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-400" />
                                        <flux:text class="font-medium text-green-600 dark:text-green-400">Harga Display Sudah Optimal</flux:text>
                                    </div>
                                    <flux:text class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
                                        Harga display sudah mencakup total modal. Margin keuntungan {{ number_format($priceAnalysis['display_profit_margin'], 1, ',', '.') }}%.
                                    </flux:text>
                                @else
                                    <div class="flex items-center space-x-2">
                                        <flux:icon.exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400" />
                                        <flux:text class="font-medium text-red-600 dark:text-red-400">Harga Display Perlu Disesuaikan</flux:text>
                                    </div>
                                    <flux:text class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
                                        Harga display kurang Rp {{ number_format(abs($priceAnalysis['display_price_difference']), 0, ',', '.') }} dari total modal.
                                        <strong>Rekomendasi:</strong> Set harga minimal Rp {{ number_format($priceAnalysis['recommended_min_price'], 0, ',', '.') }}
                                        untuk mencapai breakeven point.
                                    </flux:text>
                                @endif

                                @if($priceAnalysis['has_selling_price'])
                                    <div class="mt-3 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-700">
                                        <div class="flex items-center space-x-2">
                                            <flux:icon.currency-dollar class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                            <flux:text class="font-medium text-purple-600 dark:text-purple-400">Status Penjualan Aktual</flux:text>
                                        </div>
                                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
                                            @if($priceAnalysis['is_selling_price_correct'])
                                                Harga aktual terjual sudah optimal dengan margin keuntungan {{ number_format($priceAnalysis['selling_profit_margin'], 1, ',', '.') }}%.
                                            @else
                                                Harga aktual terjual masih di bawah modal. Perlu review proses penjualan.
                                            @endif
                                            @if($priceAnalysis['price_vs_selling_gap'] > 0)
                                                <br><strong>Gap Harga:</strong> Harga display lebih tinggi Rp {{ number_format($priceAnalysis['price_vs_selling_gap'], 0, ',', '.') }} dari harga aktual terjual.
                                            @elseif($priceAnalysis['price_vs_selling_gap'] < 0)
                                                <br><strong>Gap Harga:</strong> Harga display lebih rendah Rp {{ number_format(abs($priceAnalysis['price_vs_selling_gap']), 0, ',', '.') }} dari harga aktual terjual.
                                            @endif
                                        </flux:text>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Cost Details -->
                        <div class="border border-gray-200 dark:border-zinc-700 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 dark:bg-zinc-700 px-4 py-3 border-b border-gray-200 dark:border-zinc-700">
                                <flux:text class="font-medium text-gray-900 dark:text-white">Detail Biaya</flux:text>
                            </div>

                        <div class="divide-y divide-gray-200 dark:divide-zinc-700">
                            @foreach($costs as $cost)
                            <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <flux:text class="font-medium text-gray-900 dark:text-white">{{ $cost->description }}</flux:text>
                                            @if($cost->cost_type == 'service_parts')
                                                <flux:badge size="sm" color="green">Service & Parts</flux:badge>
                                            @else
                                                <flux:badge size="sm" color="orange">Other Cost</flux:badge>
                                            @endif
                                            @if($cost->status === 'approved')
                                                <flux:badge size="sm" color="green">Approved</flux:badge>
                                            @elseif($cost->status === 'rejected')
                                                <flux:badge size="sm" color="red">Rejected</flux:badge>
                                            @else
                                                <flux:badge size="sm" color="yellow">Pending</flux:badge>
                                            @endif
                                        </div>

                                        <div class="mt-1 flex items-center space-x-4 text-sm text-gray-600 dark:text-zinc-400">
                                            <span>{{ Carbon\Carbon::parse($cost->cost_date)->format('M d, Y') }}</span>
                                            @if($cost->vendor)
                                                <span>•</span>
                                                <span>{{ $cost->vendor->name }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <flux:text class="font-bold text-gray-900 dark:text-white">
                                            Rp {{ number_format($cost->total_price, 0, ',', '.') }}
                                        </flux:text>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($costs->hasPages())
                        <div class="mt-4 flex justify-center">
                            {{ $costs->links(data: ['scrollTo' => false]) }}
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12">
                        <flux:icon.document-text class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" />
                        <flux:heading size="md" class="mt-4 text-gray-900 dark:text-white">Belum ada data biaya</flux:heading>
                        <flux:text class="mt-2 text-gray-600 dark:text-zinc-400">
                            Belum ada catatan biaya untuk kendaraan ini.
                        </flux:text>
                        @can('cost.create')
                            <flux:button size="sm" href="{{ route('costs.create') }}" wire:navigate class="mt-4">
                                Tambah Biaya Baru
                            </flux:button>
                        @endcan
                    </div>
                @endif
            </div>
        @endcan

        <!-- Buyer Information Modal -->
        <flux:modal name="buyer-info-modal" class="md:w-96" wire:model="showBuyerModal" @open="resetValidation(); resetErrorBag()">
            <form wire:submit.prevent="printReceipt">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Data Pembeli</flux:heading>
                        <flux:text class="mt-2">
                            Masukkan informasi pembeli sebelum mencetak kwitansi penjualan kendaraan.
                        </flux:text>
                    </div>

                    <!-- Buyer Name -->
                    <flux:field>
                        <flux:label>
                            Nama Pembeli
                            <span class="text-red-600 ml-1">*</span>
                        </flux:label>
                        <flux:input
                            wire:model="buyer_name"
                            placeholder="Masukkan nama lengkap pembeli"
                        />
                        <flux:error name="buyer_name" />
                    </flux:field>

                    <!-- Buyer Phone -->
                    <flux:field>
                        <flux:label>
                            Nomor Telepon
                            <span class="text-red-600 ml-1">*</span>
                        </flux:label>
                        <flux:input
                            wire:model="buyer_phone"
                            placeholder="Masukkan nomor telepon pembeli"
                        />
                        <flux:error name="buyer_phone" />
                    </flux:field>

                    <!-- Buyer Address -->
                    <flux:field>
                        <flux:label>
                            Alamat Pembeli
                            <span class="text-red-600 ml-1">*</span>
                        </flux:label>
                        <flux:textarea
                            wire:model="buyer_address"
                            placeholder="Masukkan alamat lengkap pembeli"
                            rows="3"
                        ></flux:textarea>
                        <flux:error name="buyer_address" />
                    </flux:field>

                    <div class="flex gap-2">
                        <flux:spacer />
                        <flux:modal.close>
                            <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
                        </flux:modal.close>
                        <flux:button
                            type="submit"
                            variant="primary"
                            wire:loading.attr="disabled"
                            class="cursor-pointer"
                        >
                            <span wire:loading.remove>Cetak Kwitansi</span>
                            <span wire:loading>Memproses...</span>
                        </flux:button>
                    </div>
                </div>
            </form>
        </flux:modal>

        <!-- Image Modal -->
        <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
            <div class="relative max-w-4xl max-h-full">
                <!-- Close Button -->
                <button onclick="closeImageModal()"
                        class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
                    <flux:icon.x-mark class="w-8 h-8" />
                </button>

                <!-- Image Container -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-2xl">
                    <img id="modal-image" src="" alt="" class="max-w-full max-h-[80vh] object-contain">
                    <div class="p-4">
                        <flux:heading id="modal-title" size="md" class="text-center"></flux:heading>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Create Modal -->
    <flux:modal wire:model.self="showCommissionModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Komisi Baru</flux:heading>
                <flux:text class="mt-2">Tambahkan komisi baru untuk kendaraan ini.</flux:text>
            </div>
            <flux:select wire:model="commission_type" label="Tipe Komisi">
                <flux:select.option value="1">Komisi Penjualan</flux:select.option>
                <flux:select.option value="2">Komisi Pembelian</flux:select.option>
            </flux:select>
            <flux:input
                wire:model="commission_date"
                type="date"
                label="Tanggal Komisi"
                placeholder="Pilih tanggal"
            />
            <flux:input
                wire:model="commission_amount"
                mask:dynamic="$money($input)"
                icon="currency-dollar"
                label="Jumlah Komisi"
                placeholder="188,000"
            />
            <flux:textarea
                wire:model="commission_description"
                label="Deskripsi"
                placeholder="Masukkan deskripsi komisi"
                rows="3"
            />
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
                </flux:modal.close>
                <flux:button
                    wire:click="createCommission"
                    variant="primary"
                    class="cursor-pointer"
                >
                    Simpan Komisi
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Commission Edit Modal -->
    <flux:modal wire:model.self="showEditCommissionModal" name="edit-commission" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Komisi</flux:heading>
                <flux:text class="mt-2">Perbarui informasi komisi.</flux:text>
            </div>
            <flux:select wire:model="commission_type" label="Tipe Komisi">
                <flux:select.option value="1">Komisi Penjualan</flux:select.option>
                <flux:select.option value="2">Komisi Pembelian</flux:select.option>
            </flux:select>
            <flux:input
                wire:model="commission_date"
                type="date"
                label="Tanggal Komisi"
                placeholder="Pilih tanggal"
            />
            <flux:input
                wire:model="commission_amount"
                mask:dynamic="$money($input)"
                icon="currency-dollar"
                label="Jumlah Komisi"
                placeholder="188,000"
            />
            <flux:textarea
                wire:model="commission_description"
                label="Deskripsi"
                placeholder="Masukkan deskripsi komisi (opsional)"
                rows="3"
            />
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
                </flux:modal.close>
                <flux:button
                    wire:click="updateCommission"
                    variant="primary"
                    class="cursor-pointer"
                >
                    Update Komisi
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Loan Calculation Create Modal -->
    <flux:modal wire:model.self="showLoanCalculationModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Perhitungan Kredit</flux:heading>
                <flux:text class="mt-2">Tambahkan perhitungan kredit baru untuk kendaraan ini.</flux:text>
            </div>
            <flux:select wire:model="loan_calculation_leasing_id" label="Leasing">
                <flux:select.option value="">Pilih Leasing</flux:select.option>
                @foreach(\App\Models\Leasing::orderBy('name')->get() as $leasing)
                    <flux:select.option value="{{ $leasing->id }}">{{ $leasing->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:textarea
                wire:model="loan_calculation_description"
                label="Perhitungan Kredit"
                placeholder="Masukkan informasi perhitungan kredit"
                rows="3"
            />
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
                </flux:modal.close>
                <flux:button
                    wire:click="createLoanCalculation"
                    variant="primary"
                    class="cursor-pointer"
                >
                    Simpan Perhitungan Kredit
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Loan Calculation Edit Modal -->
    <flux:modal wire:model.self="showEditLoanCalculationModal" name="edit-loan-calculation" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Perhitungan Kredit</flux:heading>
                <flux:text class="mt-2">Perbarui informasi perhitungan kredit.</flux:text>
            </div>
            <flux:select wire:model="loan_calculation_leasing_id" label="Leasing">
                @foreach(\App\Models\Leasing::all() as $leasing)
                    <flux:select.option value="{{ $leasing->id }}">{{ $leasing->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:textarea
                wire:model="loan_calculation_description"
                label="Deskripsi"
                placeholder="Masukkan deskripsi perhitungan kredit"
                rows="3"
            />
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
                </flux:modal.close>
                <flux:button
                    wire:click="updateLoanCalculation"
                    variant="primary"
                    class="cursor-pointer"
                >
                    Update Perhitungan Kredit
                </flux:button>
            </div>
        </div>
    </flux:modal>

<!-- Purchase Payment Modal -->
<flux:modal wire:model.self="showPurchasePaymentModal" class="md:w-96" @open="resetValidation(); resetErrorBag()">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Tambah Pembayaran Pembelian</flux:heading>
            <flux:text class="mt-2">Tambahkan pembayaran untuk pembelian kendaraan ini.</flux:text>
        </div>

        @if (session()->has('error'))
            <div class="mb-6">
                <flux:callout variant="warning" class="mb-4" icon="exclamation-circle" heading="{{ session('error') }}" />
            </div>
        @endif

        <form wire:submit="savePurchasePayment">
            <div class="space-y-4">
                <flux:input
                    label="Tanggal Pembayaran"
                    type="date"
                    wire:model="purchase_payment_date"
                />

                <flux:input
                    label="Deskripsi"
                    placeholder="Deskripsi pembayaran..."
                    wire:model="purchase_payment_description"
                />

                <flux:input
                    label="Jumlah"
                    mask:dynamic="$money($input)"
                    placeholder="188,000,000"
                    icon="currency-dollar"
                    wire:model="purchase_payment_amount"
                />

                <flux:input
                    type="file"
                    label="Dokumen (Opsional)"
                    accept=".pdf,.jpg,.jpeg,.png"
                    wire:model="purchase_payment_document"
                    multiple
                />
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" class="cursor-pointer">
                    Simpan Pembayaran
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>

<!-- Edit Purchase Payment Modal -->
<flux:modal wire:model.self="showEditPurchasePaymentModal" class="md:w-96" @open="resetValidation(); resetErrorBag()">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Edit Pembayaran Pembelian</flux:heading>
            <flux:text class="mt-2">Perbarui informasi pembayaran pembelian.</flux:text>
        </div>

        @if (session()->has('error'))
            <div class="mb-6">
                <flux:callout variant="warning" class="mb-4" icon="exclamation-circle" heading="{{ session('error') }}" />
            </div>
        @endif

        <form wire:submit="updatePurchasePayment">
            <div class="space-y-4">
                <flux:input
                    label="Tanggal Pembayaran"
                    type="date"
                    wire:model="purchase_payment_date"
                />

                <flux:input
                    label="Deskripsi"
                    placeholder="Deskripsi pembayaran..."
                    wire:model="purchase_payment_description"
                />

                <flux:input
                    label="Jumlah"
                    icon="currency-dollar"
                    mask:dynamic="$money($input)"
                    placeholder="188,000,000"
                    wire:model="purchase_payment_amount"
                />

                <flux:input
                    type="file"
                    label="Dokumen (Opsional)"
                    accept=".pdf,.jpg,.jpeg,.png"
                    wire:model="purchase_payment_document"
                    multiple
                />
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" class="cursor-pointer">
                    Perbarui Pembayaran
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>

</div>

<script>
    function openImageModal(imageSrc, title) {
        const modal = document.getElementById('image-modal');
        const modalImage = document.getElementById('modal-image');
        const modalTitle = document.getElementById('modal-title');

        modalImage.src = imageSrc;
        modalTitle.textContent = title;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        const modal = document.getElementById('image-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close image modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });

    // Close image modal when clicking outside
    document.getElementById('image-modal').addEventListener('click', (e) => {
        if (e.target.id === 'image-modal') {
            closeImageModal();
        }
    });

</script>
