<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Schedules') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage scheduled message delivery') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @if(!$wahaConfigured)
        <div class="grid gap-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">WAHA Configuration Required</h3>
                                <p class="text-sm text-gray-600 dark:text-zinc-400">Schedules cannot be managed yet</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:badge color="yellow" icon="x-mark" size="sm">Not Configured</flux:badge>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-700/50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-gray-200 dark:bg-zinc-600 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Base URL</p>
                                    <p class="text-sm text-gray-600 dark:text-zinc-400">WAHA_API_URL environment variable</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500 dark:text-zinc-400">Not set</p>
                                <p class="text-xs text-red-600 dark:text-red-400">✗ Missing</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-700/50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-gray-200 dark:bg-zinc-600 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">API Key</p>
                                    <p class="text-sm text-gray-600 dark:text-zinc-400">WAHA_API_KEY environment variable</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500 dark:text-zinc-400">Not set</p>
                                <p class="text-xs text-red-600 dark:text-red-400">✗ Missing</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Configuration Required</h4>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                    WAHA_API_URL and WAHA_API_KEY are not configured. Please configure them first before managing schedules.
                                    <a href="{{ route('waha.index') }}" class="font-medium underline underline-offset-2 hover:text-yellow-800 dark:hover:text-yellow-100">Configure WAHA</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
    <div>
        @session('success')
            <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
        @endsession

        @session('error')
            <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
        @endsession

        <!-- Search & Filters -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 mb-6">
            <div class="p-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
                <div class="space-y-4 lg:space-y-0 lg:flex lg:flex-wrap lg:items-center lg:justify-between lg:gap-4">
                    <!-- Search -->
                    <div class="w-full lg:flex-1 lg:max-w-md">
                        <div class="relative">
                            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search schedules..." clearable />
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-center">
                        <!-- Session Filter -->
                        <div class="flex items-center">
                            <label for="session-filter" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mr-2 min-w-fit">Session:</label>
                            <flux:select wire:model.live="selectedSession" class="min-w-32">
                                <flux:select.option value="">All Sessions</flux:select.option>
                                @foreach($sessions as $session)
                                    <flux:select.option value="{{ $session->id }}">{{ $session->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Status Filter -->
                        <div class="flex items-center">
                            <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mr-2 min-w-fit">Status:</label>
                            <flux:select wire:model.live="statusFilter" class="min-w-24">
                                <flux:select.option value="">All Status</flux:select.option>
                                <flux:select.option value="active">Active</flux:select.option>
                                <flux:select.option value="inactive">Inactive</flux:select.option>
                            </flux:select>
                        </div>

                        <!-- Frequency Filter -->
                        <div class="flex items-center">
                            <label for="frequency-filter" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mr-2 min-w-fit">Frequency:</label>
                            <flux:select wire:model.live="frequencyFilter" class="min-w-28">
                                <flux:select.option value="">All</flux:select.option>
                                <flux:select.option value="daily">Daily</flux:select.option>
                                <flux:select.option value="weekly">Weekly</flux:select.option>
                                <flux:select.option value="monthly">Monthly</flux:select.option>
                            </flux:select>
                        </div>

                        <!-- Per Page Filter -->
                        <div class="flex items-center">
                            <label for="per-page" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mr-2 min-w-fit">Show:</label>
                            <flux:select wire:model.live="perPage" class="w-20">
                                <flux:select.option value="10">10</flux:select.option>
                                <flux:select.option value="25">25</flux:select.option>
                                <flux:select.option value="50">50</flux:select.option>
                                <flux:select.option value="100">100</flux:select.option>
                            </flux:select>
                        </div>

                        <!-- Clear Filters -->
                        @if($search || $statusFilter || $selectedSession || $frequencyFilter)
                            <div class="flex justify-start sm:justify-end">
                                <flux:button wire:click="clearFilters" variant="ghost" class="cursor-pointer" tooltip="Clear Filters">
                                    Clear Filters
                                </flux:button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Bar -->
            <div class="p-4 border-b border-gray-200 dark:border-zinc-700">
                <div class="flex flex-wrap gap-2 items-center">
                    @can('schedule.create')
                        <flux:button variant="primary" href="{{ route('schedules.create') }}" size="sm" wire:navigate icon="plus" tooltip="Create Schedule">Create</flux:button>
                    @endcan

                    @can('schedule.audit')
                        <flux:button variant="ghost" href="{{ route('schedules.audit') }}" size="sm" wire:navigate icon="document-text" tooltip="Audit Trail">Audit</flux:button>
                    @endcan

                    <div wire:loading>
                        <flux:icon.loading class="text-red-600" />
                    </div>

                    <div class="ml-auto">
                        <flux:button wire:click="$refresh" variant="ghost" size="sm" icon="arrow-path" tooltip="Refresh" class="cursor-pointer" wire:loading.attr="disabled">
                            <span wire:loading.remove>Refresh</span>
                            <span wire:loading>Please wait...</span>
                        </flux:button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="text-xs text-gray-700 bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                <button wire:click="sortBy('name')" class="flex items-center space-x-1 cursor-pointer uppercase hover:text-gray-700 dark:hover:text-gray-300">
                                    <span>Name</span>
                                    @if($sortField === 'name')
                                        <flux:icon.chevron-up class="h-4 w-4 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                        <flux:icon.chevron-down class="h-4 w-4 -mt-2 {{ $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Recipient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Frequency</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Next Run</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Usage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                        @if(isset($schedules) && $schedules->count() > 0)
                            @foreach($schedules as $index => $schedule)
                                <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400 text-center">
                                        {{ $schedules->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $schedule->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-zinc-400">{{ Str::limit($schedule->description, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        @if($schedule->group_wa_id)
                                            @if($schedule->group)
                                                <span class="font-medium">Group:</span> {{ $schedule->group->name }}
                                            @else
                                                <span class="font-medium">Group:</span> {{ Str::limit($schedule->group_wa_id, 30) }}
                                            @endif
                                        @elseif($schedule->received_number)
                                            @if($schedule->contact)
                                                <span class="font-medium">Contact:</span> {{ $schedule->contact->name ?? $schedule->contact->wa_id }}
                                            @else
                                                <span class="font-medium">Number:</span> {{ $schedule->received_number }}
                                            @endif
                                        @elseif($schedule->wa_id)
                                            <span class="font-medium">Number:</span> {{ $schedule->wa_id }}
                                        @elseif($schedule->group)
                                            <span class="font-medium">Group:</span> {{ $schedule->group->name }}
                                        @elseif($schedule->contact)
                                            <span class="font-medium">Contact:</span> {{ $schedule->contact->name ?? $schedule->contact->wa_id }}
                                        @else
                                            <span class="text-gray-400">No recipient</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        <flux:badge color="blue" size="sm">{{ ucfirst($schedule->frequency) }}</flux:badge>
                                        @if($schedule->frequency === 'weekly' && $schedule->day_of_week !== null)
                                            <div class="text-xs text-gray-400 mt-1">
                                                @php
                                                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                                @endphp
                                                {{ $days[$schedule->day_of_week] ?? '' }}
                                            </div>
                                        @endif
                                        @if($schedule->frequency === 'monthly' && $schedule->day_of_month !== null)
                                            <div class="text-xs text-gray-400 mt-1">Day {{ $schedule->day_of_month }}</div>
                                        @endif
                                        @php
                                            $userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');
                                        @endphp
                                        <div class="text-xs text-gray-400 mt-1">{{ $schedule->time ? $schedule->time->format('H:i') : '' }} ({{ $userTimezone }})</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        @if($schedule->next_run)
                                            @php
                                                $userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');
                                                $nextRunInUserTz = $schedule->next_run->setTimezone($userTimezone);
                                            @endphp
                                            {{ $nextRunInUserTz->format('M d, Y H:i') }}
                                            <div class="text-xs text-gray-400">{{ $schedule->next_run->diffForHumans() }}</div>
                                            <div class="text-xs text-gray-400">({{ $userTimezone }})</div>
                                        @else
                                            <span class="text-gray-400">Not scheduled</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        {{ $schedule->usage_count }}
                                        @if($schedule->last_run)
                                            <div class="text-xs text-gray-400">Last: {{ $schedule->last_run->diffForHumans() }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($schedule->is_active)
                                            <flux:badge color="green">Active</flux:badge>
                                        @else
                                            <flux:badge color="red">Inactive</flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            @can('schedule.view')
                                                <flux:button variant="ghost" size="xs" square href="{{ route('schedules.show', $schedule->id) }}" wire:navigate tooltip="Show">
                                                    <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                                </flux:button>
                                            @endcan

                                            @can('schedule.edit')
                                                <flux:button variant="ghost" size="xs" square href="{{ route('schedules.edit', $schedule->id) }}" wire:navigate tooltip="Edit">
                                                    <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                                </flux:button>
                                                <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="toggleActive({{ $schedule->id }})" tooltip="{{ $schedule->is_active ? 'Deactivate' : 'Activate' }}">
                                                    @if($schedule->is_active)
                                                        <flux:icon.pause variant="mini" class="text-red-500 dark:text-red-300" />
                                                    @else
                                                        <flux:icon.play variant="mini" class="text-green-500 dark:text-green-300" />
                                                    @endif
                                                </flux:button>
                                            @endcan

                                            @can('schedule.delete')
                                                <flux:modal.trigger name="delete-schedule">
                                                    <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setScheduleToDelete({{ $schedule->id }})" tooltip="Delete">
                                                        <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-300" />
                                                    </flux:button>
                                                </flux:modal.trigger>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-zinc-400">
                                    @if(isset($search) && !empty($search))
                                        No results found for "{{ $search }}"
                                    @else
                                        No schedules available
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($schedules) && $schedules->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
                    {{ $schedules->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
    </div>

    @endif

    <flux:modal name="delete-schedule" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete schedule?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this schedule.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger" class="cursor-pointer">Delete Schedule</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
