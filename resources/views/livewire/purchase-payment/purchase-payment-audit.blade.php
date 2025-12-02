<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Purchase Payment Audit Trail') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Track all activities performed on purchase payments') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <flux:button variant="primary" size="sm" href="{{ route('vehicles.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Vehicle" class="mb-4">Back</flux:button>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.document-text class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_activities'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Total Activities</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.clock class="h-8 w-8 text-green-600 dark:text-green-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['today_activities'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Today</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.plus class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['created_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Created</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.pencil-square class="h-8 w-8 text-yellow-600 dark:text-yellow-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['updated_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Updated</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.trash class="h-8 w-8 text-red-600 dark:text-red-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['deleted_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Deleted</flux:text>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <flux:input type="text" label="Search Activities" wire:model.live.debounce.300ms="search" placeholder="Search activities..." clearable />

            <!-- Vehicle Filter -->
            <flux:select label="Vehicle" wire:model.live="selectedVehicle">
                <flux:select.option value="">All Vehicles</flux:select.option>
                @foreach($vehicles as $vehicle)
                    <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->police_number }}</flux:select.option>
                @endforeach
            </flux:select>

            <!-- Per Page -->
            <flux:select label="Show" wire:model.live="perPage">
                @foreach($this->perPageOptions as $option)
                    <flux:select.option value="{{ $option }}">{{ $option }} per page</flux:select.option>
                @endforeach
            </flux:select>

            <!-- Clear Filters -->
            <div class="flex items-end">
                <flux:button wire:click="clearFilters" class="w-full cursor-pointer">
                    Clear Filters
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
                            <!-- Activity Icon -->
                            <div class="shrink-0">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                                    @switch($activity->description)
                                        @case('created purchase payment')
                                            <flux:icon.plus class="w-5 h-5 text-green-600 dark:text-green-400" />
                                            @break
                                        @case('updated purchase payment')
                                            <flux:icon.pencil-square class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                                            @break
                                        @case('deleted purchase payment')
                                            <flux:icon.trash class="w-5 h-5 text-red-600 dark:text-red-400" />
                                            @break
                                        @default
                                            <flux:icon.currency-dollar class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    @endswitch
                                </div>
                            </div>

                            <!-- Activity Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <flux:text class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                                        {{ $activity->description }}
                                    </flux:text>
                                    <flux:text class="text-xs text-gray-500 dark:text-zinc-400">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </flux:text>
                                </div>

                                <!-- User Info -->
                                @if($activity->causer)
                                    <div class="mt-1 text-xs text-gray-600 dark:text-zinc-400">
                                        by <span class="font-medium">{{ $activity->causer->name }}</span>
                                        @if($activity->subject)
                                            on purchase payment <span class="font-medium">{{ $activity->subject->payment_number }}</span>
                                            @if($activity->subject->vehicle)
                                                ({{ $activity->subject->vehicle->police_number }})
                                            @endif
                                        @else
                                            on <span class="font-medium italic">Deleted Purchase Payment</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="mt-1 text-xs text-gray-600 dark:text-zinc-400">
                                        System activity
                                        @if($activity->subject)
                                            on purchase payment <span class="font-medium">{{ $activity->subject->payment_number }}</span>
                                            @if($activity->subject->vehicle)
                                                ({{ $activity->subject->vehicle->police_number }})
                                            @endif
                                        @else
                                            on <span class="font-medium italic">Deleted Purchase Payment</span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Payment Amount Badge -->
                                @if($activity->subject)
                                    <div class="mt-2">
                                        <flux:badge
                                            color="green"
                                            size="sm"
                                        >
                                            Rp {{ number_format($activity->subject->amount, 0, ',', '.') }}
                                        </flux:badge>
                                    </div>
                                @endif

                                <!-- Changes Details -->
                                @if($activity->properties && isset($activity->properties['attributes']))
                                    <div class="mt-3">
                                        <flux:text class="text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Details:</flux:text>
                                        <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-3">
                                            @if($activity->description === 'updated purchase payment' && isset($activity->properties['old']))
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <flux:text class="text-xs font-medium text-red-600 dark:text-red-400 mb-1">Before:</flux:text>
                                                        @foreach($activity->properties['old'] as $field => $value)
                                                            @if($field !== 'updated_at' && $field !== 'created_at')
                                                                <div class="text-xs text-gray-600 dark:text-zinc-400">
                                                                    <span class="font-medium">{{ ucwords(str_replace('_', ' ', $field)) }}:</span>
                                                                    @if(is_array($value))
                                                                        <pre class="inline text-xs">{{ json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                                                    @else
                                                                        @if($field === 'amount')
                                                                            Rp {{ number_format($value, 0, ',', '.') }}
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <div>
                                                        <flux:text class="text-xs font-medium text-green-600 dark:text-green-400 mb-1">After:</flux:text>
                                                        @foreach($activity->properties['attributes'] as $field => $value)
                                                            @if($field !== 'updated_at' && $field !== 'created_at')
                                                                <div class="text-xs text-gray-600 dark:text-zinc-400">
                                                                    <span class="font-medium">{{ ucwords(str_replace('_', ' ', $field)) }}:</span>
                                                                    @if(is_array($value))
                                                                        <pre class="inline text-xs">{{ json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                                                    @else
                                                                        @if($field === 'amount')
                                                                            Rp {{ number_format($value, 0, ',', '.') }}
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                @foreach($activity->properties['attributes'] as $field => $value)
                                                    @if($field !== 'updated_at' && $field !== 'created_at')
                                                        <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">
                                                            <span class="font-medium">{{ ucwords(str_replace('_', ' ', $field)) }}:</span>
                                                            @if(is_array($value))
                                                                <pre class="inline text-xs">{{ json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                                            @else
                                                                @if($field === 'amount')
                                                                    Rp {{ number_format($value, 0, ',', '.') }}
                                                                @else
                                                                    {{ $value }}
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700">
                {{ $activities->links(data: ['scrollTo' => false]) }}
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <flux:icon.currency-dollar class="mx-auto h-12 w-12 text-gray-400 dark:text-zinc-600" />
                <flux:heading size="md" class="mt-4 text-gray-900 dark:text-zinc-100">No Purchase Payment Activities Found</flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-zinc-400">
                    @if($search || $selectedVehicle)
                        No purchase payment activities match your current filters. Try adjusting your search criteria.
                    @else
                        No purchase payment activities have been recorded yet.
                    @endif
                </flux:text>
            </div>
        @endif
    </div>
</div>
