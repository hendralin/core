<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Template Audit Trail') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Track all activities performed on templates') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.document-text class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_activities'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-gray-400">Total Activities</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.clock class="h-8 w-8 text-green-600 dark:text-green-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['today_activities'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-gray-400">Today</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.plus class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['created_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-gray-400">Created</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.pencil-square class="h-8 w-8 text-yellow-600 dark:text-yellow-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['updated_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-gray-400">Updated</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.trash class="h-8 w-8 text-red-600 dark:text-red-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['deleted_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-gray-400">Deleted</flux:text>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <flux:input
                wire:model.live.debounce.300ms="search"
                label="Search Activities"
                placeholder="Search activities..." clearable />

            <!-- Template Filter -->
            <div>
                <label for="template-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Template
                </label>
                <flux:select wire:model.live="selectedTemplate">
                    <flux:select.option value="">All Templates</flux:select.option>
                    @foreach($templates as $template)
                        <flux:select.option value="{{ $template->id }}">{{ $template->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Per Page -->
            <div>
                <label for="per-page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Show
                </label>
                <flux:select wire:model.live="perPage" placeholder="Select per page...">
                    @foreach($this->perPageOptions as $option)
                        <flux:select.option value="{{ $option }}">{{ $option }} per page</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Clear Filters -->
            <div class="flex items-end">
                <flux:button wire:click="clearFilters" class="w-full mb-0.5">
                    Clear Filters
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Activities List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($activities->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($activities as $activity)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-start space-x-4">
                            <!-- Activity Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                                    @switch($activity->description)
                                        @case('created a new template')
                                            <flux:icon.plus class="w-5 h-5 text-green-600 dark:text-green-400" />
                                            @break
                                        @case('updated template information')
                                            <flux:icon.pencil-square class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                                            @break
                                        @case('deleted template')
                                            <flux:icon.trash class="w-5 h-5 text-red-600 dark:text-red-400" />
                                            @break
                                        @default
                                            <flux:icon.document-text class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    @endswitch
                                </div>
                            </div>

                            <!-- Activity Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <flux:text class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $activity->formatted_description }}
                                    </flux:text>
                                    <flux:text class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </flux:text>
                                </div>

                                <!-- User Info -->
                                @if($activity->causer)
                                    <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                        by <span class="font-medium">{{ $activity->causer->name }}</span>
                                    </div>
                                @endif

                                <!-- Changes Details -->
                                @if($activity->properties && isset($activity->properties['attributes']))
                                    <div class="mt-3">
                                        <flux:text class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Details:</flux:text>
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                            @if($activity->description === 'updated template information' && isset($activity->properties['old']))
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <flux:text class="text-xs font-medium text-red-600 dark:text-red-400 mb-1">Before:</flux:text>
                                                        @foreach($activity->properties['old'] as $field => $value)
                                                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                                                <span class="font-medium">{{ ucwords(str_replace('_', ' ', $field)) }}:</span>
                                                                @if($field === 'is_active')
                                                                    {{ $value ? 'Active' : 'Inactive' }}
                                                                @else
                                                                    {{ $value ?: 'N/A' }}
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div>
                                                        <flux:text class="text-xs font-medium text-green-600 dark:text-green-400 mb-1">After:</flux:text>
                                                        @foreach($activity->properties['attributes'] as $field => $value)
                                                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                                                <span class="font-medium">{{ ucwords(str_replace('_', ' ', $field)) }}:</span>
                                                                @if($field === 'is_active')
                                                                    {{ $value ? 'Active' : 'Inactive' }}
                                                                @else
                                                                    {{ $value ?: 'N/A' }}
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                @foreach($activity->properties['attributes'] as $field => $value)
                                                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                                                        <span class="font-medium">{{ ucwords(str_replace('_', ' ', $field)) }}:</span>
                                                        @if($field === 'is_active')
                                                            {{ $value ? 'Active' : 'Inactive' }}
                                                        @else
                                                            {{ $value ?: 'N/A' }}
                                                        @endif
                                                    </div>
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
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                {{ $activities->links(data: ['scrollTo' => false]) }}
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <flux:icon.document-text class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" />
                <flux:heading size="md" class="mt-4 text-gray-900 dark:text-gray-100">No Activities Found</flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-gray-400">
                    @if($search || $selectedTemplate)
                        No activities match your current filters. Try adjusting your search criteria.
                    @else
                        No template activities have been recorded yet.
                    @endif
                </flux:text>
            </div>
        @endif
    </div>
</div>
