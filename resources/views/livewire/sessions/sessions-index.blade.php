<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Sessions') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage your WhatsApp HTTP API sessions') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @if(!$wahaConfigured)
        <!-- WAHA Configuration Required Card -->
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
                                <p class="text-sm text-gray-600 dark:text-zinc-400">WhatsApp sessions cannot be managed yet</p>
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
                                    You must configure WAHA_API_URL and WAHA_API_KEY in your environment variables before you can manage WhatsApp sessions.
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
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <!-- Search -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search sessions..." clearable />
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex items-center gap-3">
                        <!-- Status Filter -->
                        <div class="flex items-center">
                            <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mr-2">Status:</label>
                            <flux:select wire:model.live="statusFilter">
                                <flux:select.option value="">All Status</flux:select.option>
                                <flux:select.option value="WORKING">Working</flux:select.option>
                                <flux:select.option value="SCAN_QR_CODE">Scan QR Code</flux:select.option>
                                <flux:select.option value="STARTING">Starting</flux:select.option>
                                <flux:select.option value="FAILED">Failed</flux:select.option>
                                <flux:select.option value="UNKNOWN">Unknown</flux:select.option>
                                <flux:select.option value="ERROR">Error</flux:select.option>
                            </flux:select>
                        </div>

                        <!-- Clear Filters -->
                        @if($search || $statusFilter)
                            <flux:button wire:click="clearFilters" variant="ghost" class="cursor-pointer" tooltip="Clear Filters">
                                Clear Filters
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Bar -->
            <div class="p-4 border-b border-gray-200 dark:border-zinc-700">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap gap-2">
                        @can('session.create')
                            <flux:button variant="primary" size="sm" href="{{ route('sessions.create') }}" wire:navigate icon="plus" tooltip="Create Session">Create</flux:button>
                        @endcan

                        @can('session.audit')
                            <flux:button variant="ghost" size="sm" href="{{ route('sessions.audit') }}" wire:navigate icon="document-text" tooltip="Audit Trail">Audit</flux:button>
                        @endcan

                        <div wire:loading>
                            <flux:icon.loading class="text-red-600" />
                        </div>
                    </div>

                    <flux:button variant="ghost" size="sm" wire:click="clearCache" icon="arrow-path" class="cursor-pointer" tooltip="Refresh">Refresh</flux:button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                No.
                            </th>
                            <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 tracking-wider">
                                <button wire:click="sortBy('name')" class="flex items-center space-x-1 cursor-pointer uppercase hover:text-gray-700 dark:hover:text-gray-300">
                                    <span>Name</span>
                                    @if($sortField === 'name')
                                        <flux:icon.chevron-up class="h-4 w-4 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                        <flux:icon.chevron-down class="h-4 w-4 -mt-2 {{ $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                <button wire:click="sortBy('session_id')" class="flex items-center space-x-1 cursor-pointer uppercase hover:text-gray-700 dark:hover:text-gray-300">
                                    <span>Session ID</span>
                                    @if($sortField === 'session_id')
                                        <flux:icon.chevron-up class="h-4 w-4 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                        <flux:icon.chevron-down class="h-4 w-4 -mt-2 {{ $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                        @if(isset($sessions) && $sessions->count() > 0)
                            @foreach($sessions as $index => $session)
                                <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400 text-center">
                                        {{ $sessions->firstItem() + $index }}
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="shrink-0">
                                                <svg class="h-8 w-8 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.742.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $session->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-zinc-400">{{ $session->session_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">{{ $session->session_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        <div class="flex items-center space-x-3">
                                            <div class="shrink-0">
                                                @if($session->profile_picture)
                                                    <img src="{{ $session->profile_picture }}" alt="Profile Picture" class="h-10 w-10 rounded-full object-cover">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-zinc-600 flex items-center justify-center">
                                                        <flux:icon.user class="h-6 w-6 text-gray-500 dark:text-zinc-400" />
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                @if($session->me && isset($session->me['id']) && isset($session->me['pushName']))
                                                    <div class="text-sm">
                                                        <div class="font-medium text-gray-900 dark:text-zinc-100">{{ $session->me['pushName'] }}</div>
                                                        <div class="text-gray-400 dark:text-zinc-400">{{ $session->me['id'] }}</div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 dark:text-zinc-400">Not available</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($session->status == 'WORKING')
                                            <flux:badge color="green">Working</flux:badge>
                                        @elseif ($session->status == 'SCAN_QR_CODE')
                                            <flux:badge color="yellow">Scan QR Code</flux:badge>
                                        @elseif ($session->status == 'STOPPED')
                                            <flux:badge color="gray">STOPPED</flux:badge>
                                        @elseif ($session->status == 'STARTING')
                                            <flux:badge color="blue">STARTING</flux:badge>
                                        @elseif ($session->status == 'UNKNOWN')
                                            <flux:badge color="gray">Unknown</flux:badge>
                                        @elseif ($session->status == 'ERROR')
                                            <flux:badge color="red">Error</flux:badge>
                                        @else
                                            <flux:badge color="red">{{ $session->status }}</flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">{{ $session->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            @can('session.view')
                                                <flux:button variant="ghost" size="xs" square href="{{ route('sessions.show', $session->id) }}" wire:navigate tooltip="Show">
                                                    <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                                </flux:button>
                                            @endcan

                                            @can('session.edit')
                                                <flux:button variant="ghost" size="xs" square href="{{ route('sessions.edit', $session->id) }}" wire:navigate tooltip="Edit">
                                                    <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                                </flux:button>
                                            @endcan

                                            @can('session.delete')
                                                <flux:modal.trigger name="delete-session">
                                                    <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setSessionToDelete({{ $session->id }})" tooltip="Delete">
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
                                <td colspan="6" class="px-6 py-8 text-center">
                                    <flux:icon.inbox-stack class="mx-auto h-12 w-12 text-gray-400" />
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-zinc-100">No sessions</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                                        @if(isset($search) && !empty($search))
                                            No results found for "{{ $search }}"
                                        @else
                                            Get started by creating a new session.
                                        @endif
                                    </p>
                                    @if(empty($search))
                                        <div class="mt-6">
                                            <flux:button variant="primary" href="{{ route('sessions.create') }}" wire:navigate icon="plus">
                                                Create Session
                                            </flux:button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($sessions) && $sessions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>

    <flux:modal name="delete-session" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete session?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this session.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger">Delete Session</flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
