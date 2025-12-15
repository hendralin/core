<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Message Audit Trail') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Track all activities performed on messages') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <flux:button variant="primary" size="sm" href="{{ route('messages.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Messages" class="mb-4">Back</flux:button>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 mb-6">
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
                <flux:icon.paper-airplane class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['sent_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Messages Sent</flux:text>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.exclamation-triangle class="h-8 w-8 text-red-600 dark:text-red-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['failed_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Failed</flux:text>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4 mb-6 border border-gray-200 dark:border-zinc-700">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Search -->
            <flux:input type="text" label="Search Activities" wire:model.live.debounce.300ms="search" placeholder="Search activities..." clearable />

            <!-- Session Filter -->
            <flux:select label="Session" wire:model.live="selectedSession">
                <flux:select.option value="">All Sessions</flux:select.option>
                @foreach($sessions as $session)
                    <flux:select.option value="{{ $session->id }}">{{ $session->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <!-- Per Page -->
            <flux:select label="Show" wire:model.live="perPage" placeholder="Select per page...">
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
                                        @case('sent a message')
                                            <flux:icon.paper-airplane class="w-5 h-5 text-green-600 dark:text-green-400" />
                                            @break
                                        @case('message delivery failed')
                                            <flux:icon.exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400" />
                                            @break
                                        @default
                                            <flux:icon.document-text class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    @endswitch
                                </div>
                            </div>

                            <!-- Activity Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <flux:text class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                                        {{ $activity->formatted_description ?? $activity->description }}
                                    </flux:text>
                                    <flux:text class="text-xs text-gray-500 dark:text-zinc-400">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </flux:text>
                                </div>

                                <!-- User Info -->
                                @if($activity->causer)
                                    <div class="mt-1 text-xs text-gray-600 dark:text-zinc-400">
                                        by <span class="font-medium">{{ $activity->causer->name }}</span>
                                    </div>
                                @endif

                                <!-- Message Details -->
                                @if($activity->subject)
                                    <div class="mt-3">
                                        <flux:text class="text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Message Details:</flux:text>
                                        <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-3">
                                            @if($activity->subject->contact && $activity->subject->contact->name)
                                                <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">
                                                    <strong>Recipient:</strong> {{ $activity->subject->contact->name }}
                                                    @if($activity->subject->received_number)
                                                        ({{ $activity->subject->received_number }})
                                                    @endif
                                                </div>
                                            @elseif($activity->subject->received_number)
                                                <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">
                                                    <strong>Recipient:</strong> {{ $activity->subject->received_number }}
                                                </div>
                                            @endif

                                            @if($activity->subject->group_wa_id)
                                                <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">
                                                    <strong>Group:</strong> {{ $activity->subject->group_wa_id }}
                                                </div>
                                            @endif

                                            @if($activity->subject->wahaSession)
                                                <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">
                                                    <strong>Session:</strong> {{ $activity->subject->wahaSession->name }}
                                                </div>
                                            @endif

                                            @if($activity->subject->message)
                                                <div class="text-xs text-gray-600 dark:text-zinc-400">
                                                    <strong>Message:</strong> {{ Str::limit($activity->subject->message, 100) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Changes Details -->
                                @if($activity->properties && isset($activity->properties['attributes']))
                                    <div class="mt-3">
                                        <flux:text class="text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Changes:</flux:text>
                                        <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-3">
                                            @if(isset($activity->properties['old']))
                                                <div class="mb-2">
                                                    <flux:text class="text-xs text-red-600 dark:text-red-400 mb-1">Before:</flux:text>
                                                    @foreach($activity->properties['old'] as $field => $value)
                                                        <div class="text-xs text-gray-600 dark:text-zinc-400">
                                                            <strong>{{ ucfirst($field) }}:</strong> {{ $value ?? 'N/A' }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if(isset($activity->properties['attributes']))
                                                <div>
                                                    <flux:text class="text-xs text-green-600 dark:text-green-400 mb-1">After:</flux:text>
                                                    @foreach($activity->properties['attributes'] as $field => $value)
                                                        <div class="text-xs text-gray-600 dark:text-zinc-400">
                                                            <strong>{{ ucfirst($field) }}:</strong> {{ $value ?? 'N/A' }}
                                                        </div>
                                                    @endforeach
                                                </div>
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
            @if($activities->hasPages())
                <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700">
                    {{ $activities->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <flux:icon.document-text class="mx-auto h-12 w-12 text-gray-400" />
                <flux:heading size="lg" class="mt-4 text-gray-900 dark:text-zinc-100">No activities found</flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-zinc-400">
                    @if($search || $selectedSession)
                        No activities match your current filters.
                    @else
                        Message activities will appear here once messages are sent.
                    @endif
                </flux:text>
                @if($search || $selectedSession)
                    <flux:button wire:click="clearFilters" variant="outline" class="mt-4">
                        Clear Filters
                    </flux:button>
                @endif
            </div>
        @endif
    </div>
</div>
