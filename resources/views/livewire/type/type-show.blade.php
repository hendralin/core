@php
    use Illuminate\Support\Str;
@endphp

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show Type') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Type details and vehicle information') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('types.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Tipe">Back</flux:button>
        @can('type.edit')
            <flux:button variant="filled" size="sm" href="{{ route('types.edit', $type->id) }}" wire:navigate icon="pencil-square" class="ml-1">Edit</flux:button>
        @endcan

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Type Information</flux:heading>

                    <div class="space-y-4">
                        <div>
                            <flux:heading size="md">Brand</flux:heading>
                            <flux:text class="mt-1">{{ $type->brand->name ?? '-' }}</flux:text>
                        </div>

                        <div>
                            <flux:heading size="md">Name</flux:heading>
                            <flux:text class="mt-1">{{ $type->name }}</flux:text>
                        </div>

                        @if($type->description)
                        <div>
                            <flux:heading size="md">Description</flux:heading>
                            <flux:text class="mt-1">{!! nl2br(e($type->description)) !!}</flux:text>
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                            <div>
                                <flux:heading size="sm">Created</flux:heading>
                                <flux:text class="text-sm">{{ $type->created_at->format('M d, Y \a\t H:i') }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm">Last Updated</flux:heading>
                                <flux:text class="text-sm">{{ $type->updated_at->format('M d, Y \a\t H:i') }}</flux:text>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicles with this Type -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <flux:heading size="lg">
                            Vehicles
                            @if(!empty($search))
                                <span class="text-sm font-normal text-gray-600 dark:text-zinc-400">(filtered)</span>
                            @endif
                        </flux:heading>
                        <div class="flex items-center gap-2">
                            @if(isset($paginationInfo))
                                @if($paginationInfo['is_filtered'])
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                        {{ $paginationInfo['start'] }}-{{ $paginationInfo['end'] }} of {{ $paginationInfo['total'] }} results
                                    </span>
                                    @if($paginationInfo['total'] != ($typeTotalVehicles ?? 0))
                                        <span class="text-sm text-gray-600 dark:text-zinc-400">
                                            ({{ $typeTotalVehicles ?? 0 }} total)
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        {{ $paginationInfo['start'] }}-{{ $paginationInfo['end'] }} of {{ $paginationInfo['total'] }} vehicles
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    {{ $typeTotalVehicles ?? 0 }} total vehicles
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Search and Per Page Controls -->
                    <div class="space-y-4 mb-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="flex items-center">
                                <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300 mr-2">Per Page:</label>
                                <select id="per-page" wire:model.live="perPage"
                                        class="text-sm rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach ($this->perPageOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <flux:spacer class="hidden md:inline" />
                            <flux:spacer class="hidden md:inline" />
                            <div class="flex items-center">
                                <label for="search-vehicles" class="text-sm text-gray-700 dark:text-zinc-300 mr-2">Search:</label>
                                <flux:input wire:model.live.debounce.500ms="search" id="search-vehicles" placeholder="Vehicle name or police number..." clearable />
                            </div>
                        </div>
                    </div>

                    <!-- Vehicles List -->
                    @if(isset($vehicles) && $vehicles->count() > 0)
                        <div class="space-y-3">
                            @foreach($vehicles as $vehicle)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700/70 transition-colors">
                                <div class="flex-1">
                                    <flux:text class="font-medium text-gray-900 dark:text-white">{{ $vehicle->police_number ?? 'N/A' }}</flux:text>
                                    <div class="flex items-center gap-4 mt-1">
                                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">{{ $vehicle->vehicle_model->name ?? 'Unknown Model' }}</flux:text>
                                        @if($vehicle->brand)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                {{ $vehicle->brand->name }}
                                            </span>
                                        @endif
                                        @if($vehicle->category)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                                {{ $vehicle->category->name }}
                                            </span>
                                        @endif
                                        @if($vehicle->description)
                                            <span class="text-xs text-gray-500 dark:text-zinc-500">•</span>
                                            <flux:text class="text-xs text-gray-500 dark:text-zinc-500 line-clamp-1">{{ Str::limit($vehicle->description, 50) }}</flux:text>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <flux:text class="text-sm text-gray-500 dark:text-zinc-400">{{ $vehicle->updated_at->format('M d, Y') }}</flux:text>
                                    <flux:text class="text-xs text-gray-400 dark:text-zinc-500 block">{{ $vehicle->updated_at->format('H:i') }}</flux:text>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="text-sm text-gray-600 dark:text-zinc-400">
                                @if(isset($paginationInfo))
                                    Page {{ $vehicles->currentPage() }} of {{ $vehicles->lastPage() }}
                                    @if($paginationInfo['is_filtered'])
                                        (filtered results)
                                    @endif
                                @endif
                            </div>
                            <div>
                                {{ $vehicles->links(data: ['scrollTo' => false]) }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <flux:icon.cube class="mx-auto h-12 w-12 text-gray-400 dark:text-zinc-600" />
                            <flux:heading size="md" class="mt-2 text-gray-600 dark:text-zinc-400">
                                @if(!empty($search))
                                    No vehicles found for "{{ $search }}"
                                @else
                                    @if(($typeTotalVehicles ?? 0) > 0)
                                        No vehicles to display
                                    @else
                                        No vehicles with this type
                                    @endif
                                @endif
                            </flux:heading>
                            <flux:text class="text-sm text-gray-500 dark:text-zinc-500">
                                @if(!empty($search))
                                    Try adjusting your search terms or clear the search to see all vehicles.
                                @elseif(($typeTotalVehicles ?? 0) > 0)
                                    All vehicles with this type are filtered out by current settings.
                                @else
                                    Vehicles will appear here when they are assigned to this type.
                                @endif
                            </flux:text>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Sidebar -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Statistics</flux:heading>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <flux:text>Total Vehicles (All)</flux:text>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                {{ $totalVehiclesCount }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <flux:text>Vehicles with Type</flux:text>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                {{ $typeTotalVehicles }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <flux:text>Type Age</flux:text>
                            <span class="text-sm text-gray-600 dark:text-zinc-400">
                                {{ $type->created_at->diffForHumans() }}
                            </span>
                        </div>

                        @if($type->updated_at != $type->created_at)
                        <div class="flex items-center justify-between">
                            <flux:text>Last Modified</flux:text>
                            <span class="text-sm text-gray-600 dark:text-zinc-400">
                                {{ $type->updated_at->diffForHumans() }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
