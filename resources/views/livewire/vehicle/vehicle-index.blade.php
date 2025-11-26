<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Vehicles') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage all your vehicles') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <div class="space-y-4 mb-2">
        <!-- Actions Section -->
        <div class="flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between">
            <div class="flex flex-wrap gap-2">
                @can('vehicle.create')
                    <flux:button variant="primary" size="sm" href="{{ route('vehicles.create') }}" wire:navigate icon="plus" class="w-full sm:w-auto" tooltip="Tambah Kendaraan">Tambah</flux:button>
                @endcan

                @can('vehicle.audit')
                    <flux:button variant="ghost" size="sm" href="{{ route('vehicles.audit') }}" wire:navigate icon="document-text" class="w-full sm:w-auto" tooltip="Audit Trail">Audit</flux:button>
                @endcan
            </div>

            <!-- Export Actions -->
            <div class="flex gap-2">
                <flux:button variant="ghost" size="sm" wire:click="exportExcel" icon="document-arrow-down" tooltip="Export to Excel" class="flex-1 sm:flex-none cursor-pointer">
                    <span class="hidden sm:inline">Excel</span>
                    <span class="sm:hidden">Excel</span>
                </flux:button>
                <flux:button variant="ghost" size="sm" wire:click="exportPdf" icon="document-arrow-down" tooltip="Export to PDF" class="flex-1 sm:flex-none cursor-pointer">
                    <span class="hidden sm:inline">PDF</span>
                    <span class="sm:hidden">PDF</span>
                </flux:button>

                <div wire:loading class="flex items-center justify-center p-2">
                    <flux:icon.loading class="text-red-600 w-4 h-4" />
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="grid grid-cols-1 md:grid-cols-4 mb-3 mt-4">
        <div class="flex items-center gap-2 w-44 mb-2 md:mb-0">
            <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">Show:</label>
            <flux:select id="per-page" wire:model.live="perPage">
                @foreach ($this->perPageOptions as $option)
                <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                @endforeach
            </flux:select>
            <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">entries</label>
        </div>
        <flux:spacer class="hidden md:inline" />
        <flux:spacer class="hidden md:inline" />
        <div class="flex items-center gap-2 flex-1">
            <label for="search-input" class="text-sm text-gray-700 dark:text-zinc-300 whitespace-nowrap">Search:</label>
            <flux:input id="search-input" wire:model.live.debounce.500ms="search" placeholder="Vehicles..." clearable class="flex-1" />
        </div>
    </div>

    <!-- Desktop Table Section -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 border dark:border-zinc-700 dark:text-zinc-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-4 py-3 w-10 text-center">No.</th>
                    <th scope="col" class="px-4 py-3 w-16 text-center">Foto</th>
                    <th scope="col" class="px-4 py-3 w-32">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'police_number') {{ $sortDirection }} @endif" wire:click="sortBy('police_number')">
                            Nomor Polisi
                            @if ($sortField == 'police_number' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'police_number' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-32">Brand</th>
                    <th scope="col" class="px-4 py-3 w-32">Type</th>
                    <th scope="col" class="px-4 py-3 w-32">Model</th>
                    <th scope="col" class="px-4 py-3 w-24">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'year') {{ $sortDirection }} @endif" wire:click="sortBy('year')">
                            Tahun
                            @if ($sortField == 'year' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'year' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-32">Warehouse</th>
                    <th scope="col" class="px-4 py-3 w-32">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'status') {{ $sortDirection }} @endif" wire:click="sortBy('status')">
                            Status
                            @if ($sortField == 'status' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'status' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-32">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'created_at') {{ $sortDirection }} @endif" wire:click="sortBy('created_at')">
                            Created
                            @if ($sortField == 'created_at' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'created_at' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-1/12">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($vehicles) && $vehicles->count() > 0)
                    @foreach($vehicles as $index => $vehicle)
                        <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:key="vehicle-{{ $vehicle->id }}">
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-white">{{ $vehicles->firstItem() + $index }}</td>
                            <td class="px-2 py-2 text-center">
                                @if($vehicle->images && $vehicle->images->count() > 0)
                                    <div class="relative w-10 h-10 rounded-md overflow-hidden border border-gray-200 dark:border-zinc-600 bg-gray-100 dark:bg-zinc-700 mx-auto cursor-pointer hover:shadow-md transition-shadow"
                                         onclick="openImageModal('{{ asset('photos/vehicles/' . $vehicle->images->first()->image) }}', '{{ $vehicle->police_number }}', {{ json_encode($vehicle->images->map(fn($img) => asset('photos/vehicles/' . $img->image))->values()) }})">
                                        <img src="{{ asset('photos/vehicles/' . $vehicle->images->first()->image) }}"
                                             alt="Vehicle thumbnail"
                                             class="w-full h-full object-cover"
                                             onerror="this.style.display='none'">
                                        @if($vehicle->images->count() > 1)
                                            <div class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold text-[10px]">
                                                {{ $vehicle->images->count() }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-md bg-gray-100 dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 flex items-center justify-center mx-auto">
                                        <flux:icon.photo class="w-4 h-4 text-gray-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <flux:modal.trigger name="vehicle-details-{{ $vehicle->id }}">
                                        <button type="button" class="cursor-pointer text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors bg-transparent border-none p-0">
                                            <flux:icon.eye class="w-4 h-4" />
                                        </button>
                                    </flux:modal.trigger>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-600 dark:text-zinc-300 font-medium">{{ $vehicle->police_number }}</span>
                                            @if($vehicle->images && $vehicle->images->count() > 0)
                                                <flux:icon.photo class="w-4 h-4 text-blue-500 dark:text-blue-400" title="{{ $vehicle->images->count() }} images" />
                                            @endif
                                        </div>
                                        @if($vehicle->created_at->diffInDays() <= 7)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 ml-2">
                                                New
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">{{ $vehicle->brand?->name ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">{{ $vehicle->type?->name ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">{{ $vehicle->vehicle_model?->name ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-center text-gray-600 dark:text-zinc-300">{{ $vehicle->year }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">{{ $vehicle->warehouse?->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($vehicle->status == 1)
                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                        @else
                                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                        @endif">
                                        {{ $vehicle->status == 1 ? 'Available' : 'Sold' }}
                                    </span>
                                    @if($vehicle->display_price)
                                        <div class="text-xs text-green-600 dark:text-green-400 font-medium">
                                            Rp {{ number_format($vehicle->display_price / 1000000, 2) }}Jt
                                        </div>
                                    @endif
                                    @if($vehicle->kilometer)
                                        <div class="text-xs text-gray-500 dark:text-zinc-400">
                                            {{ number_format($vehicle->kilometer / 1000, 0) }}k km
                                        </div>
                                    @endif
                                    @if($vehicle->vehicle_registration_expiry_date && Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->isPast())
                                        <div class="text-xs text-red-600 dark:text-red-400">STNK Expired</div>
                                    @elseif($vehicle->vehicle_registration_expiry_date && Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->diffInDays() * -1 < 30)
                                        <div class="text-xs text-orange-600 dark:text-orange-400">STNK Expiring</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-zinc-400">
                                {{ $vehicle->created_at->format('M d, Y') }}
                                <div class="text-gray-400 dark:text-zinc-500">{{ $vehicle->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                @can('vehicle.view')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('vehicles.show', $vehicle->id) }}" wire:navigate tooltip="Show">
                                        <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                    </flux:button>
                                @endcan

                                @can('vehicle.edit')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('vehicles.edit', $vehicle->id) }}" wire:navigate tooltip="Edit">
                                        <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                    </flux:button>
                                @endcan

                                @can('vehicle.delete')
                                    <flux:modal.trigger name="delete-vehicle">
                                        <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setVehicleToDelete({{ $vehicle->id }})" tooltip="Delete">
                                            <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-300" />
                                        </flux:button>
                                    </flux:modal.trigger>
                                @endcan
                            </td>
                        </tr>
                        <!-- Modal for vehicle details -->
                        <flux:modal name="vehicle-details-{{ $vehicle->id }}" class="w-full max-w-4xl">
                            <div class="space-y-6">
                                <div>
                                    <flux:heading size="lg">Detail Kendaraan: {{ $vehicle->police_number }}</flux:heading>
                                    <flux:text class="mt-2">
                                        Informasi lengkap kendaraan
                                    </flux:text>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                                    <!-- Specifications -->
                                    <div class="bg-white dark:bg-zinc-800 rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                            <flux:icon.cog class="w-4 h-4 mr-2 text-gray-600 dark:text-zinc-400" />
                                            Spesifikasi
                                        </h4>
                                        <div class="space-y-1 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-zinc-400">Kapasitas Silinder:</span>
                                                <span class="text-gray-900 dark:text-white font-medium">{{ $vehicle->cylinder_capacity ? number_format($vehicle->cylinder_capacity, 0) . ' cc' : '-' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-zinc-400">Tipe Bahan Bakar:</span>
                                                <span class="text-gray-900 dark:text-white font-medium">{{ $vehicle->fuel_type ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-zinc-400">Kilometer:</span>
                                                <span class="text-gray-900 dark:text-white font-medium">{{ number_format($vehicle->kilometer ?? 0, 0, ',', '.') }} km</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-zinc-400">Warna:</span>
                                                <span class="text-gray-900 dark:text-white font-medium">{{ $vehicle->color ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Registration & Dates -->
                                    <div class="bg-white dark:bg-zinc-800 rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                            <flux:icon.document-text class="w-4 h-4 mr-2 text-gray-600 dark:text-zinc-400" />
                                            Registrasi
                                        </h4>
                                        <div class="space-y-1 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-zinc-400">Tgl. Registrasi:</span>
                                                <span class="text-gray-900 dark:text-white font-medium">{{ $vehicle->vehicle_registration_date ? Carbon\Carbon::parse($vehicle->vehicle_registration_date)->format('M d, Y') : '-' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-zinc-400">Tgl. Kadaluarsa:</span>
                                                <span class="text-gray-900 dark:text-white font-medium
                                                    @if($vehicle->vehicle_registration_expiry_date && Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->isPast())
                                                        text-red-600 dark:text-red-400
                                                    @elseif($vehicle->vehicle_registration_expiry_date && Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->diffInDays() * -1 < 30)
                                                        text-orange-600 dark:text-orange-400
                                                    @endif">
                                                    {{ $vehicle->vehicle_registration_expiry_date ? Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->format('M d, Y') : '-' }}
                                                </span>
                                            </div>
                                            @if($vehicle->vehicle_registration_expiry_date)
                                                <div class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                                                    @if(Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->isPast())
                                                        Kadaluarsa {{ Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->diffForHumans() }}
                                                    @else
                                                        Kadaluarsa {{ Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->diffForHumans() }}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Pricing Information -->
                                    <div class="bg-white dark:bg-zinc-800 rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                            <flux:icon.currency-dollar class="w-4 h-4 mr-2 text-gray-600 dark:text-zinc-400" />
                                            Informasi Harga
                                        </h4>
                                        <div class="space-y-1 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-zinc-400">Harga Beli:</span>
                                                <span class="text-gray-900 dark:text-white font-medium">{{ $vehicle->purchase_price ? 'Rp ' . number_format($vehicle->purchase_price, 0, ',', '.') : '-' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-zinc-400">Harga Jual:</span>
                                                <span class="text-green-600 dark:text-green-400 font-medium">{{ $vehicle->display_price ? 'Rp ' . number_format($vehicle->display_price, 0, ',', '.') : '-' }}</span>
                                            </div>
                                            @if($vehicle->selling_price)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-zinc-400">Harga Penjualan:</span>
                                                <span class="text-blue-600 dark:text-blue-400 font-medium">{{ $vehicle->selling_price ? 'Rp ' . number_format($vehicle->selling_price, 0, ',', '.') : '-' }}</span>
                                            </div>
                                            @endif
                                            @if($vehicle->purchase_date)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-zinc-400">Tgl. Pembelian:</span>
                                                <span class="text-gray-900 dark:text-white font-medium">{{ Carbon\Carbon::parse($vehicle->purchase_date)->format('M d, Y') }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Additional Info -->
                                    @if($vehicle->description || $vehicle->chassis_number)
                                    <div class="bg-white dark:bg-zinc-800 rounded-lg p-3 border border-gray-200 dark:border-zinc-700 md:col-span-2 lg:col-span-1">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                            <flux:icon.information-circle class="w-4 h-4 mr-2 text-gray-600 dark:text-zinc-400" />
                                            Informasi Tambahan
                                        </h4>
                                        <div class="space-y-1 text-sm">
                                            @if($vehicle->chassis_number)
                                            <div>
                                                <span class="text-gray-600 dark:text-zinc-400">Nomor Rangka:</span>
                                                <span class="text-gray-900 dark:text-white font-medium font-mono text-xs">{{ $vehicle->chassis_number }}</span>
                                            </div>
                                            @endif
                                            @if($vehicle->engine_number)
                                            <div>
                                                <span class="text-gray-600 dark:text-zinc-400">Nomor Mesin:</span>
                                                <span class="text-gray-900 dark:text-white font-medium font-mono text-xs">{{ $vehicle->engine_number }}</span>
                                            </div>
                                            @endif
                                            @if($vehicle->description)
                                            <div class="mt-2">
                                                <span class="text-gray-600 dark:text-zinc-400 block mb-1">Deskripsi:</span>
                                                <p class="text-gray-900 dark:text-white text-xs">
                                                    @php
                                                        $allowed = "<p><b><i><u><br><a><strong><em><ul><ol><li><span><div><img>";
                                                        $description = strip_tags($vehicle->description, $allowed);
                                                    @endphp
                                                    {!! Str::limit($description, 100) !!}
                                                </p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div class="flex gap-2">
                                    <flux:spacer />
                                    <flux:modal.close>
                                        <flux:button variant="ghost">Tutup</flux:button>
                                    </flux:modal.close>
                                </div>
                            </div>
                        </flux:modal>
                    </div>
                </div>
            @endforeach
        @else
            <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 border-gray-200">
                <td class="px-4 py-2 text-gray-600 dark:text-zinc-300 text-center" colspan="10">
                    @if(isset($search) && !empty($search))
                        No results found for "{{ $search }}"
                    @else
                        No data available in table
                    @endif
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 mb-2">
        {{ $vehicles->links(data: ['scrollTo' => false]) }}
    </div>

    <!-- Mobile Card Layout -->
    <div class="md:hidden space-y-4">
        @if(isset($vehicles) && $vehicles->count() > 0)
            @foreach($vehicles as $index => $vehicle)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                    <!-- Card Header -->
                    <div class="p-4 border-b border-gray-200 dark:border-zinc-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $vehicle->police_number }}
                                    </h3>
                                    @if($vehicle->images && $vehicle->images->count() > 0)
                                        <flux:icon.photo class="w-5 h-5 text-blue-500 dark:text-blue-400" title="{{ $vehicle->images->count() }} images" />
                                    @endif
                                    @if($vehicle->created_at->diffInDays() <= 7)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                            New
                                        </span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500 dark:text-zinc-400">Brand:</span>
                                        <span class="text-gray-900 dark:text-white font-medium ml-1">{{ $vehicle->brand?->name ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-zinc-400">Type:</span>
                                        <span class="text-gray-900 dark:text-white font-medium ml-1">{{ $vehicle->type?->name ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-zinc-400">Model:</span>
                                        <span class="text-gray-900 dark:text-white font-medium ml-1">{{ $vehicle->vehicle_model?->name ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-zinc-400">Year:</span>
                                        <span class="text-gray-900 dark:text-white font-medium ml-1">{{ $vehicle->year }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($vehicle->status == 1)
                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                        @else
                                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                        @endif">
                                        {{ $vehicle->status == 1 ? 'Available' : 'Sold' }}
                                    </span>
                                    @if($vehicle->images && $vehicle->images->count() > 0)
                                        <div class="relative w-12 h-12 rounded-lg overflow-hidden border-2 border-white dark:border-zinc-600 shadow-sm cursor-pointer hover:shadow-lg transition-shadow"
                                             onclick="openImageModal('{{ asset('photos/vehicles/' . $vehicle->images->first()->image) }}', '{{ $vehicle->police_number }}', {{ json_encode($vehicle->images->map(fn($img) => asset('photos/vehicles/' . $img->image))->values()) }})">
                                            <img src="{{ asset('photos/vehicles/' . $vehicle->images->first()->image) }}"
                                                 alt="Vehicle thumbnail"
                                                 class="w-full h-full object-cover"
                                                 onerror="this.style.display='none'">
                                            @if($vehicle->images->count() > 1)
                                                <div class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                                                    {{ $vehicle->images->count() }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                @if($vehicle->display_price)
                                    <div class="text-lg font-bold text-green-600 dark:text-green-400">
                                        Rp {{ number_format($vehicle->display_price / 1000000, 1) }}M
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Card Body - Collapsible -->
                    <div class="p-4 space-y-4">
                        <!-- Quick Info Row -->
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <flux:icon.building-storefront class="w-4 h-4 text-gray-400" />
                                <div>
                                    <div class="text-gray-500 dark:text-zinc-400 text-xs">Warehouse</div>
                                    <div class="text-gray-900 dark:text-white font-medium">{{ $vehicle->warehouse?->name ?? '-' }}</div>
                                </div>
                            </div>
                            @if($vehicle->kilometer)
                                <div class="flex items-center gap-2">
                                    <flux:icon.wrench-screwdriver class="w-4 h-4 text-gray-400" />
                                    <div>
                                        <div class="text-gray-500 dark:text-zinc-400 text-xs">Kilometer</div>
                                        <div class="text-gray-900 dark:text-white font-medium">{{ number_format($vehicle->kilometer) }} km</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Expandable Details -->
                        <details class="space-y-3">
                            <summary class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors cursor-pointer list-none">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Lihat Detail</span>
                                <flux:icon.chevron-down class="w-5 h-5 text-gray-500 transition-transform duration-200" />
                            </summary>

                            <div class="space-y-4">
                                <!-- Specifications -->
                                <div class="bg-gray-50 dark:bg-zinc-700/30 rounded-lg p-3">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                        <flux:icon.cog class="w-4 h-4 mr-2 text-gray-600 dark:text-zinc-400" />
                                        Spesifikasi
                                    </h4>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-gray-500 dark:text-zinc-400">Kapasitas Silinder:</span>
                                            <div class="text-gray-900 dark:text-white font-medium">{{ $vehicle->cylinder_capacity ? number_format($vehicle->cylinder_capacity, 0) . ' cc' : '-' }}</div>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-zinc-400">Tipe Bahan Bakar:</span>
                                            <div class="text-gray-900 dark:text-white font-medium">{{ $vehicle->fuel_type ?? '-' }}</div>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-zinc-400">Warna:</span>
                                            <div class="text-gray-900 dark:text-white font-medium">{{ $vehicle->color ?? '-' }}</div>
                                        </div>
                                        @if($vehicle->chassis_number)
                                            <div class="col-span-2">
                                                <span class="text-gray-500 dark:text-zinc-400">Nomor Rangka:</span>
                                                <div class="text-gray-900 dark:text-white font-medium font-mono text-xs">{{ $vehicle->chassis_number }}</div>
                                            </div>
                                        @endif
                                        @if($vehicle->engine_number)
                                            <div class="col-span-2">
                                                <span class="text-gray-500 dark:text-zinc-400">Nomor Mesin:</span>
                                                <div class="text-gray-900 dark:text-white font-medium font-mono text-xs">{{ $vehicle->engine_number }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Registration & Pricing -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Registration -->
                                    <div class="bg-gray-50 dark:bg-zinc-700/30 rounded-lg p-3">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                            <flux:icon.document-text class="w-4 h-4 mr-2 text-gray-600 dark:text-zinc-400" />
                                            Registrasi
                                        </h4>
                                        <div class="space-y-2 text-sm">
                                            <div>
                                                <span class="text-gray-500 dark:text-zinc-400">Tgl. Registrasi:</span>
                                                <div class="text-gray-900 dark:text-white font-medium">{{ $vehicle->vehicle_registration_date ? Carbon\Carbon::parse($vehicle->vehicle_registration_date)->format('M d, Y') : '-' }}</div>
                                            </div>
                                            <div>
                                                <span class="text-gray-500 dark:text-zinc-400">Tgl. Kadaluarsa:</span>
                                                <div class="text-gray-900 dark:text-white font-medium
                                                    @if($vehicle->vehicle_registration_expiry_date && Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->isPast())
                                                        text-red-600 dark:text-red-400
                                                    @elseif($vehicle->vehicle_registration_expiry_date && Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->diffInDays() * -1 < 30)
                                                        text-orange-600 dark:text-orange-400
                                                    @endif">
                                                    {{ $vehicle->vehicle_registration_expiry_date ? Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->format('M d, Y') : '-' }}
                                                </div>
                                                @if($vehicle->vehicle_registration_expiry_date)
                                                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                                                        @if(Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->isPast())
                                                            Kadaluarsa {{ Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->diffForHumans() }}
                                                        @else
                                                            Kadaluarsa {{ Carbon\Carbon::parse($vehicle->vehicle_registration_expiry_date)->diffForHumans() }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pricing -->
                                    <div class="bg-gray-50 dark:bg-zinc-700/30 rounded-lg p-3">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                            <flux:icon.currency-dollar class="w-4 h-4 mr-2 text-gray-600 dark:text-zinc-400" />
                                            Informasi Harga
                                        </h4>
                                        <div class="space-y-2 text-sm">
                                            <div>
                                                <span class="text-gray-500 dark:text-zinc-400">Harga Beli:</span>
                                                <div class="text-gray-900 dark:text-white font-medium">{{ $vehicle->purchase_price ? 'Rp ' . number_format($vehicle->purchase_price, 0, ',', '.') : '-' }}</div>
                                            </div>
                                            <div>
                                                <span class="text-gray-500 dark:text-zinc-400">Harga Jual:</span>
                                                <div class="text-green-600 dark:text-green-400 font-medium">{{ $vehicle->display_price ? 'Rp ' . number_format($vehicle->display_price, 0, ',', '.') : '-' }}</div>
                                            </div>
                                            @if($vehicle->selling_price)
                                            <div>
                                                <span class="text-gray-500 dark:text-zinc-400">Harga Penjualan:</span>
                                                <div class="text-blue-600 dark:text-blue-400 font-medium">{{ $vehicle->selling_price ? 'Rp ' . number_format($vehicle->selling_price, 0, ',', '.') : '-' }}</div>
                                            </div>
                                            @endif
                                            @if($vehicle->purchase_date)
                                            <div>
                                                <span class="text-gray-500 dark:text-zinc-400">Tgl. Pembelian:</span>
                                                <div class="text-gray-900 dark:text-white font-medium">{{ Carbon\Carbon::parse($vehicle->purchase_date)->format('M d, Y') }}</div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                @if($vehicle->description)
                                <div class="bg-gray-50 dark:bg-zinc-700/30 rounded-lg p-3">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                        <flux:icon.information-circle class="w-4 h-4 mr-2 text-gray-600 dark:text-zinc-400" />
                                        Deskripsi
                                    </h4>
                                    <p class="text-sm text-gray-700 dark:text-zinc-300">
                                        @php
                                            $allowed = "<p><b><i><u><br><a><strong><em><ul><ol><li><span><div><img>";
                                            $description = strip_tags($vehicle->description, $allowed);
                                        @endphp
                                        {!! $description !!}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </details>
                    </div>

                    <!-- Card Footer - Actions -->
                    <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-700/50 border-t border-gray-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <div class="text-xs text-gray-500 dark:text-zinc-400">
                                Dibuat: {{ $vehicle->created_at->format('M d, Y H:i') }}
                            </div>
                            <div class="flex items-center gap-2">
                                @can('vehicle.view')
                                    <flux:button variant="ghost" size="sm" href="{{ route('vehicles.show', $vehicle->id) }}" wire:navigate class="p-2">
                                        <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                    </flux:button>
                                @endcan

                                @can('vehicle.edit')
                                    <flux:button variant="ghost" size="sm" href="{{ route('vehicles.edit', $vehicle->id) }}" wire:navigate class="p-2">
                                        <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                    </flux:button>
                                @endcan

                                @can('vehicle.delete')
                                    <flux:modal.trigger name="delete-vehicle">
                                        <flux:button variant="ghost" size="sm" class="p-2" wire:click="setVehicleToDelete({{ $vehicle->id }})">
                                            <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-300" />
                                        </flux:button>
                                    </flux:modal.trigger>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-8 text-center">
                <flux:icon.exclamation-triangle class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada kendaraan ditemukan</h3>
                <p class="text-gray-500 dark:text-zinc-400">
                    @if(isset($search) && !empty($search))
                        Tidak ada hasil untuk "{{ $search }}"
                    @else
                        Belum ada kendaraan tersedia di sistem
                    @endif
                </p>
            </div>
        @endif
    </div>

    <flux:modal name="delete-vehicle" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus kendaraan?</flux:heading>
                <flux:text class="mt-2">
                    <p>Anda akan menghapus kendaraan ini.</p>
                    <p>Tindakan ini tidak dapat dibatalkan.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger" class="cursor-pointer">Hapus Kendaraan</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Image Preview Modal with Carousel -->
    <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-90 hidden z-50 flex items-center justify-center p-4">
        <div class="relative w-full max-w-5xl">
            <!-- Close Button -->
            <button onclick="closeImageModal()"
                    class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors z-10">
                <flux:icon.x-mark class="w-8 h-8" />
            </button>

            <!-- Image Counter -->
            <div class="absolute -top-12 left-0 text-white text-sm font-medium">
                <span id="current-image-index">1</span> / <span id="total-images">1</span>
            </div>

            <!-- Main Image Container -->
            <div class="relative bg-white dark:bg-zinc-800 rounded-lg overflow-hidden shadow-2xl">
                <!-- Navigation Buttons -->
                <button id="prev-btn" onclick="navigateImage(-1)"
                        class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/75 text-white rounded-full p-3 transition-all z-10 hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button id="next-btn" onclick="navigateImage(1)"
                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/75 text-white rounded-full p-3 transition-all z-10 hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <!-- Image Display -->
                <div class="relative bg-gray-100 dark:bg-zinc-900">
                    <img id="modal-image" src="" alt="" class="w-full h-[70vh] object-contain">
                </div>

                <!-- Image Title and Info -->
                <div class="p-4 border-t border-gray-200 dark:border-zinc-700">
                    <flux:heading id="modal-title" size="md" class="text-center"></flux:heading>
                </div>

                <!-- Thumbnail Navigation -->
                <div id="thumbnail-container" class="p-4 border-t border-gray-200 dark:border-zinc-700 hidden">
                    <div id="thumbnails" class="flex gap-2 overflow-x-auto pb-2">
                        <!-- Thumbnails will be injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentImageIndex = 0;
    let imageGallery = [];
    let vehicleTitle = '';

    function openImageModal(imageSrc, title, allImages = null) {
        const modal = document.getElementById('image-modal');
        const modalImage = document.getElementById('modal-image');
        const modalTitle = document.getElementById('modal-title');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const thumbnailContainer = document.getElementById('thumbnail-container');

        // If allImages is provided (array of image URLs), use it for gallery
        if (allImages && Array.isArray(allImages) && allImages.length > 0) {
            imageGallery = allImages;
            vehicleTitle = title;

            // Find the index of the clicked image
            currentImageIndex = imageGallery.findIndex(img => img === imageSrc);
            if (currentImageIndex === -1) currentImageIndex = 0;

            // Show/hide navigation buttons
            if (imageGallery.length > 1) {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.remove('hidden');
                thumbnailContainer.classList.remove('hidden');
                updateThumbnails();
            } else {
                prevBtn.classList.add('hidden');
                nextBtn.classList.add('hidden');
                thumbnailContainer.classList.add('hidden');
            }
        } else {
            // Single image mode
            imageGallery = [imageSrc];
            vehicleTitle = title;
            currentImageIndex = 0;
            prevBtn.classList.add('hidden');
            nextBtn.classList.add('hidden');
            thumbnailContainer.classList.add('hidden');
        }

        updateModalImage();
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function updateModalImage() {
        const modalImage = document.getElementById('modal-image');
        const modalTitle = document.getElementById('modal-title');
        const currentIndexEl = document.getElementById('current-image-index');
        const totalImagesEl = document.getElementById('total-images');

        modalImage.src = imageGallery[currentImageIndex];
        modalTitle.textContent = `${vehicleTitle} - Photo ${currentImageIndex + 1}`;
        currentIndexEl.textContent = currentImageIndex + 1;
        totalImagesEl.textContent = imageGallery.length;

        // Update thumbnail active state
        updateThumbnailActive();
    }

    function updateThumbnails() {
        const thumbnailsContainer = document.getElementById('thumbnails');
        thumbnailsContainer.innerHTML = '';

        imageGallery.forEach((imgSrc, index) => {
            const thumb = document.createElement('div');
            thumb.className = `flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 cursor-pointer transition-all ${
                index === currentImageIndex
                    ? 'border-blue-500 ring-2 ring-blue-300'
                    : 'border-gray-300 dark:border-zinc-600 hover:border-blue-400'
            }`;
            thumb.onclick = () => jumpToImage(index);

            const img = document.createElement('img');
            img.src = imgSrc;
            img.className = 'w-full h-full object-cover';
            img.alt = `Thumbnail ${index + 1}`;

            thumb.appendChild(img);
            thumbnailsContainer.appendChild(thumb);
        });
    }

    function updateThumbnailActive() {
        const thumbnails = document.querySelectorAll('#thumbnails > div');
        thumbnails.forEach((thumb, index) => {
            if (index === currentImageIndex) {
                thumb.className = 'flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 border-blue-500 ring-2 ring-blue-300 cursor-pointer transition-all';
            } else {
                thumb.className = 'flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 border-gray-300 dark:border-zinc-600 hover:border-blue-400 cursor-pointer transition-all';
            }
        });
    }

    function navigateImage(direction) {
        currentImageIndex += direction;

        // Loop around
        if (currentImageIndex < 0) {
            currentImageIndex = imageGallery.length - 1;
        } else if (currentImageIndex >= imageGallery.length) {
            currentImageIndex = 0;
        }

        updateModalImage();
    }

    function jumpToImage(index) {
        currentImageIndex = index;
        updateModalImage();
    }

    function closeImageModal() {
        const modal = document.getElementById('image-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        imageGallery = [];
        currentImageIndex = 0;
    }

    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        const modal = document.getElementById('image-modal');
        if (!modal.classList.contains('hidden')) {
            if (e.key === 'Escape') {
                closeImageModal();
            } else if (e.key === 'ArrowLeft') {
                navigateImage(-1);
            } else if (e.key === 'ArrowRight') {
                navigateImage(1);
            }
        }
    });

    // Close modal when clicking outside
    document.getElementById('image-modal').addEventListener('click', (e) => {
        if (e.target.id === 'image-modal') {
            closeImageModal();
        }
    });
</script>
