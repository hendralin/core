<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="lg">Activity Log</flux:heading>
            <flux:text class="text-gray-600 dark:text-zinc-400 mt-1">
                Track all activities performed by {{ $user->name }}
            </flux:text>
        </div>
        <flux:button wire:click="clearFilters" size="sm" class="cursor-pointer">
            Clear Filters
        </flux:button>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4 border border-gray-200 dark:border-zinc-700">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="activity-search" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">
                    Search Activities
                </label>
                <input type="text"
                       id="activity-search"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search activities..."
                       class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Log Type Filter -->
            <div>
                <label for="log-filter" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">
                    Activity Type
                </label>
                <select id="log-filter"
                        wire:model.live="logFilter"
                        class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Activities</option>
                    @foreach($logTypes as $logType)
                        <option value="{{ $logType }}">{{ ucwords(str_replace('_', ' ', $logType)) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Filter -->
            <div>
                <label for="date-filter" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">
                    Time Period
                </label>
                <select id="date-filter"
                        wire:model.live="dateFilter"
                        class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="1">Last 24 Hours</option>
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="all">All Time</option>
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <label for="per-page" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">
                    Show
                </label>
                <select id="per-page"
                        wire:model.live="perPage"
                        class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                </select>
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
                                <div class="w-10 h-10 rounded-full bg-{{ $activity->color }}-100 dark:bg-{{ $activity->color }}-900/20 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-{{ $activity->color }}-600 dark:text-{{ $activity->color }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @switch($activity->icon)
                                            @case('user')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                @break
                                            @case('shield-check')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                @break
                                            @case('building-storefront')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25A2.25 2.25 0 010 18.75V10.5a2.25 2.25 0 012.25-2.25H6m7.5-3v2.25m0 0l3-3m-3 3l-3-3m-3 9.75h7.5"></path>
                                                @break
                                            @case('cube')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
                                                @break
                                            @case('shopping-bag')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                                @break
                                            @case('truck')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 18.75a1.5 1.5 0 01-3 0V15a1.5 1.5 0 013 0v3.75zM15.75 18.75a1.5 1.5 0 01-3 0V15a1.5 1.5 0 013 0v3.75zM12 12.75a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5zM19.5 8.25H15a2.25 2.25 0 01-2.25-2.25V4.5a2.25 2.25 0 012.25-2.25h4.5a2.25 2.25 0 012.25 2.25v1.5a2.25 2.25 0 01-2.25 2.25z"></path>
                                                @break
                                            @case('wrench-screwdriver')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.53 3.53a2.25 2.25 0 01-3.18 0l-3.53-3.53a4.5 4.5 0 00-6.336 4.486c.091.581.115 1.194-.14 1.743m5.108-.233l-.082.044a4.5 4.5 0 00-1.743.14"></path>
                                                @break
                                            @case('arrow-right-left')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0l-4.5 4.5M21 7.5H7.5"></path>
                                                @break
                                            @default
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        @endswitch
                                    </svg>
                                </div>
                            </div>

                            <!-- Activity Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <flux:text class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                                        {{ $activity->formatted_description }}
                                    </flux:text>
                                    <flux:text class="text-xs text-gray-500 dark:text-zinc-400">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </flux:text>
                                </div>

                                <!-- Changes Details -->
                                @if($activity->properties && isset($activity->properties['attributes']))
                                    <div class="mt-2 text-xs text-gray-600 dark:text-zinc-400">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach(array_keys($activity->properties['attributes']) as $field)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 dark:bg-zinc-700 text-gray-800 dark:text-zinc-200">
                                                    {{ ucwords(str_replace('_', ' ', $field)) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- IP Address & User Agent if available -->
                                @if(isset($activity->properties['ip']) || isset($activity->properties['user_agent']))
                                    <div class="mt-2 text-xs text-gray-500 dark:text-zinc-500">
                                        @if(isset($activity->properties['ip']))
                                            <span>IP: {{ $activity->properties['ip'] }}</span>
                                        @endif
                                        @if(isset($activity->properties['user_agent']))
                                            <span class="ml-4">Browser: {{ Str::limit($activity->properties['user_agent'], 50) }}</span>
                                        @endif
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
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <flux:heading size="md" class="mt-4 text-gray-900 dark:text-zinc-100">No Activities Found</flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-zinc-400">
                    @if($search || $logFilter || $dateFilter !== '7')
                        No activities match your current filters. Try adjusting your search criteria.
                    @else
                        This user hasn't performed any activities yet.
                    @endif
                </flux:text>
            </div>
        @endif
    </div>
</div>
