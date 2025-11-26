<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Session Audit Trail') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Track all activities performed on WhatsApp HTTP API sessions') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <flux:button variant="primary" size="sm" href="{{ route('sessions.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Sessions" class="mb-4">Back</flux:button>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.document-text class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $activities->total() }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Total Activities</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.device-phone-mobile class="h-8 w-8 text-green-600 dark:text-green-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $apiSessions ? count($apiSessions) : 0 }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Total Sessions</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.play class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $apiSessions ? collect($apiSessions)->where('status', 'WORKING')->count() : 0 }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Working</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.stop class="h-8 w-8 text-red-600 dark:text-red-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $apiSessions ? collect($apiSessions)->where('status', 'STOPPED')->count() : 0 }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Stopped</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.qr-code class="h-8 w-8 text-yellow-600 dark:text-yellow-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $apiSessions ? collect($apiSessions)->where('status', 'SCAN_QR_CODE')->count() : 0 }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Scan QR</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.user-group class="h-8 w-8 text-purple-600 dark:text-purple-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $apiSessions ? collect($apiSessions)->where('presence', 'online')->count() : 0 }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Online</flux:text>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:input wire:model.live="search" label="Search Activities" placeholder="Search activities..." />

            <flux:select wire:model.live="selectedSession" label="Session">
                <option value="">All Sessions</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}">{{ $session->name }} ({{ $session->session_id }})</option>
                @endforeach
            </flux:select>

            <div class="flex items-end gap-2">
                <flux:button wire:click="clearFilters" class="w-full cursor-pointer">
                    Clear Filters
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Activities Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
            <flux:heading size="lg">Activity Log</flux:heading>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 tracking-wider">Session</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 tracking-wider">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($activities as $activity)
                        <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="shrink-0 h-10 w-10">
                                        @if($activity->causer)
                                            <img class="h-10 w-10 rounded-full" src="{{ $activity->causer->avatar_url }}" alt="{{ $activity->causer->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-zinc-600 flex items-center justify-center">
                                                <flux:icon.user class="h-6 w-6 text-gray-600 dark:text-zinc-400" />
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $activity->causer ? $activity->causer->name : 'System' }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-zinc-400">
                                            {{ $activity->causer ? $activity->causer->email : 'Automated' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge color="{{ $activity->event === 'created' ? 'green' : ($activity->event === 'updated' ? 'blue' : ($activity->event === 'deleted' ? 'red' : 'gray')) }}">
                                    {{ ucfirst($activity->event) }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($activity->subject)
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $activity->subject->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-zinc-400">{{ $activity->subject->session_id }}</div>
                                @else
                                    <span class="text-gray-400 dark:text-zinc-400">Deleted Session</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate">
                                    {{ $activity->description }}
                                </div>
                                @if($activity->properties && count($activity->properties) > 0)
                                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                                        <details>
                                            <summary class="cursor-pointer hover:text-gray-700 dark:hover:text-gray-300">View Details</summary>
                                            <pre class="mt-2 p-2 bg-gray-50 dark:bg-zinc-900 rounded text-xs overflow-x-auto">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                                        </details>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                {{ $activity->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                {{ $activity->properties['ip'] ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-zinc-400">
                                No activities found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700">
            {{ $activities->links(data: ['scrollTo' => false]) }}
        </div>
    </div>
</div>
