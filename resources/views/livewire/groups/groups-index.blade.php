<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Groups') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage all your groups') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

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
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <!-- Search -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search groups..." clearable />
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex items-center gap-3">
                        <!-- Session Filter -->
                        <div class="flex items-center">
                            <label for="session-filter" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mr-2">Session:</label>
                            <flux:select wire:model.live="sessionFilter">
                                <flux:select.option value="">All Sessions</flux:select.option>
                                @foreach($availableSessions as $session)
                                    <flux:select.option value="{{ $session->id }}">{{ $session->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Community Filter -->
                        <div class="flex items-center">
                            <label for="community-filter" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mr-2">Type:</label>
                            <flux:select wire:model.live="communityFilter">
                                <flux:select.option value="">All Types</flux:select.option>
                                <flux:select.option value="community">Community</flux:select.option>
                                <flux:select.option value="group">Group</flux:select.option>
                            </flux:select>
                        </div>

                        <!-- Per Page Filter -->
                        <div class="flex items-center">
                            <label for="per-page" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mr-2">Show:</label>
                            <flux:select wire:model.live="perPage" class="w-20">
                                <flux:select.option value="10">10</flux:select.option>
                                <flux:select.option value="25">25</flux:select.option>
                                <flux:select.option value="50">50</flux:select.option>
                                <flux:select.option value="100">100</flux:select.option>
                            </flux:select>
                        </div>

                        <!-- Clear Filters -->
                        @if($search || $sessionFilter || $communityFilter)
                            <flux:button wire:click="clearFilters" variant="ghost" class="cursor-pointer" tooltip="Clear Filters">
                                Clear Filters
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Bar -->
            @if (env('WAHA_API_URL') && env('WAHA_API_KEY'))
            <div class="p-4 border-b border-gray-200 dark:border-zinc-700">
                <div class="flex flex-wrap gap-2 justify-end">
                    <div class="flex flex-wrap gap-2">
                        @can('group.sync')
                            <flux:modal.trigger name="sync-groups-modal">
                                <flux:button variant="ghost" size="sm" icon="arrow-path" class="cursor-pointer" tooltip="Sync Groups">Sync Groups</flux:button>
                            </flux:modal.trigger>
                        @endcan

                        <div wire:loading>
                            <flux:icon.loading class="text-red-600" />
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                No.
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                <button wire:click="sortBy('name')" class="flex items-center space-x-1 cursor-pointer uppercase hover:text-gray-700 dark:hover:text-zinc-300">
                                    <span>Group Name</span>
                                    @if($sortField === 'name')
                                        <flux:icon.chevron-up class="h-4 w-4 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                        <flux:icon.chevron-down class="h-4 w-4 -mt-2 {{ $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">WhatsApp ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Session</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 cursor-pointer uppercase hover:text-gray-700 dark:hover:text-zinc-300">
                                    <span>Created</span>
                                    @if($sortField === 'created_at')
                                        <flux:icon.chevron-up class="h-4 w-4 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                        <flux:icon.chevron-down class="h-4 w-4 -mt-2 {{ $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                <button wire:click="sortBy('updated_at')" class="flex items-center space-x-1 cursor-pointer uppercase hover:text-gray-700 dark:hover:text-zinc-300">
                                    <span>Updated</span>
                                    @if($sortField === 'updated_at')
                                        <flux:icon.chevron-up class="h-4 w-4 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                        <flux:icon.chevron-down class="h-4 w-4 -mt-2 {{ $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                        @if(isset($groups) && $groups->count() > 0)
                            @foreach($groups as $index => $group)
                                <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400 text-center">
                                        {{ $groups->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            @if($group->picture_url)
                                                <flux:modal.trigger name="group-preview-{{ $group->id }}">
                                                    <img src="{{ $group->picture_url }}" alt="Group" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-zinc-600 shrink-0 cursor-pointer hover:opacity-80 transition-opacity">
                                                </flux:modal.trigger>
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center shrink-0">
                                                    <flux:icon.user-group class="w-4 h-4 text-gray-400 dark:text-zinc-500" />
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $group->name }}</div>
                                                @if($group->detail && isset($group->detail['desc']))
                                                    <div class="text-sm text-gray-500 dark:text-zinc-400">{{ Str::limit($group->detail['desc'], 50) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        {{ $group->group_wa_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        {{ $group->wahaSession->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($group->detail && isset($group->detail['isCommunity']) && $group->detail['isCommunity'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                Community
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Group
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        {{ $group->detail['size'] ?? 'N/A' }} members
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        @if($group->detail && isset($group->detail['owner']))
                                            {{ substr($group->detail['owner'], 0, 12) }}...
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        {{ $group->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        {{ $group->updated_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-start space-x-2">
                                            @can('group.view')
                                                <flux:button variant="ghost" size="xs" square href="{{ route('groups.show', $group->id) }}" wire:navigate tooltip="Show">
                                                    <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                                </flux:button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-zinc-400">
                                    @if(isset($search) && !empty($search))
                                        No results found for "{{ $search }}"
                                    @else
                                        No data available in table
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($groups) && $groups->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
                    {{ $groups->links() }}
                </div>
            @endif
        </div>
    </div>

    <flux:modal name="sync-groups-modal" class="min-w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Sync Groups</flux:heading>
                <flux:text class="mt-2">Select a session to synchronize groups from WhatsApp.</flux:text>
            </div>

            <form wire:submit="syncGroups" class="space-y-6">
                <flux:select wire:model="selectedSessionId" label="Session">
                    <flux:select.option value="">Select a session to sync groups from</flux:select.option>
                    @foreach($syncableSessions as $session)
                        <flux:select.option value="{{ $session->id }}">{{ $session->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" color="green" wire:loading.attr="disabled" class="cursor-pointer">
                        <span wire:loading.remove>Sync Groups</span>
                        <span wire:loading>Syncing...</span>
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    @foreach($groups as $group)
        @if($group->picture_url)
            <flux:modal name="group-preview-{{ $group->id }}" class="md:w-96">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Group Picture') }}</flux:heading>
                        <flux:subheading>
                            <flux:text class="mt-1">{{ $group->name }}</flux:text>
                            @if($group->detail && isset($group->detail['desc']))
                                <flux:text class="mt-1">{{ Str::limit($group->detail['desc'], 100) }}</flux:text>
                            @endif
                            @if($group->detail && isset($group->detail['size']))
                                <flux:text class="mt-1">{{ $group->detail['size'] }} members</flux:text>
                            @endif
                        </flux:subheading>
                    </div>
                    <div class="flex justify-center">
                        <img src="{{ $group->picture_url }}" alt="Group" class="max-w-full max-h-96 rounded-lg object-contain">
                    </div>
                    <div class="flex justify-end gap-2">
                        <flux:modal.close>
                            <flux:button variant="ghost" class="cursor-pointer">Close</flux:button>
                        </flux:modal.close>
                    </div>
                </div>
            </flux:modal>
        @endif
    @endforeach
</div>
