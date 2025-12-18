<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Message Audit Trail') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Track all activities performed on messages') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <flux:button variant="primary" size="sm" href="{{ route('messages.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Messages" class="mb-4">Back</flux:button>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
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

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.arrow-path class="h-8 w-8 text-purple-600 dark:text-purple-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['resent_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Resent</flux:text>
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
                                        @case('failed to send message')
                                        @case('failed to resend message')
                                            <flux:icon.exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400" />
                                            @break
                                        @case('resent a message')
                                            <flux:icon.arrow-path class="w-5 h-5 text-purple-600 dark:text-purple-400" />
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
                                            <!-- Status Badge -->
                                            @if($activity->subject->status)
                                                <div class="mb-2">
                                                    <strong class="text-xs text-gray-700 dark:text-zinc-300">Status:</strong>
                                                    @if($activity->subject->status === 'sent')
                                                        <flux:badge color="green" size="sm">Sent</flux:badge>
                                                    @elseif($activity->subject->status === 'failed')
                                                        <flux:badge color="red" size="sm">Failed</flux:badge>
                                                    @else
                                                        <flux:badge color="gray" size="sm">Pending</flux:badge>
                                                    @endif
                                                </div>
                                            @endif

                                            @if($activity->subject->contact && ($activity->subject->contact->name || $activity->subject->contact->push_name))
                                                <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">
                                                    <strong>Recipient:</strong> {{ $activity->subject->contact->name ?? $activity->subject->contact->push_name }}
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
                                                    <strong>Group:</strong>
                                                    @if($activity->subject->group && $activity->subject->group->name)
                                                        {{ $activity->subject->group->name }} ({{ $activity->subject->group_wa_id }})
                                                    @else
                                                        {{ $activity->subject->group_wa_id }}
                                                    @endif
                                                </div>
                                            @endif

                                            @if($activity->subject->wahaSession)
                                                <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">
                                                    <strong>Session:</strong> {{ $activity->subject->wahaSession->name }}
                                                </div>
                                            @endif

                                            @if($activity->subject->template)
                                                <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">
                                                    <strong>Template:</strong> {{ $activity->subject->template->name }}
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

                                <!-- Activity Details -->
                                @if($activity->properties && isset($activity->properties['attributes']))
                                    <div class="mt-3">
                                        <flux:text class="text-xs font-medium text-gray-700 dark:text-zinc-300 mb-2">Activity Details:</flux:text>
                                        <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-3">
                                            @if(isset($activity->properties['attributes']['error']))
                                                <div class="mb-2 p-2 bg-red-50 dark:bg-red-900/20 rounded border border-red-200 dark:border-red-800">
                                                    <flux:text class="text-xs text-red-600 dark:text-red-400 font-medium mb-1">Error:</flux:text>
                                                    <div class="text-xs text-red-700 dark:text-red-300">
                                                        {{ $activity->properties['attributes']['error'] }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if(isset($activity->properties['attributes']['action']))
                                                <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">
                                                    <strong>Action:</strong>
                                                    @if($activity->properties['attributes']['action'] === 'resent')
                                                        <flux:badge color="purple" size="sm">Resent</flux:badge>
                                                    @elseif($activity->properties['attributes']['action'] === 'resend_failed')
                                                        <flux:badge color="red" size="sm">Resend Failed</flux:badge>
                                                    @endif
                                                </div>
                                            @endif

                                            @if(isset($activity->properties['attributes']['recipient']))
                                                <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">
                                                    <strong>Recipient ID:</strong> {{ $activity->properties['attributes']['recipient'] }}
                                                </div>
                                            @endif

                                            @if(isset($activity->properties['attributes']['recipient_type']))
                                                <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">
                                                    <strong>Recipient Type:</strong> {{ ucfirst($activity->properties['attributes']['recipient_type']) }}
                                                </div>
                                            @endif

                                            @if(isset($activity->properties['attributes']['message_type']))
                                                <div class="text-xs text-gray-600 dark:text-zinc-400 mb-1">
                                                    <strong>Message Type:</strong> {{ ucfirst($activity->properties['attributes']['message_type']) }}
                                                </div>
                                            @endif

                                            @if(isset($activity->properties['old']))
                                                <div class="mb-2 mt-2 pt-2 border-t border-gray-300 dark:border-zinc-600">
                                                    <flux:text class="text-xs text-red-600 dark:text-red-400 mb-1 font-medium">Before:</flux:text>
                                                    @foreach($activity->properties['old'] as $field => $value)
                                                        <div class="text-xs text-gray-600 dark:text-zinc-400">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong> {{ $value ?? 'N/A' }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if(isset($activity->properties['attributes']) && !isset($activity->properties['attributes']['error']) && !isset($activity->properties['attributes']['action']))
                                                <div class="mt-2 pt-2 border-t border-gray-300 dark:border-zinc-600">
                                                    <flux:text class="text-xs text-green-600 dark:text-green-400 mb-1 font-medium">After:</flux:text>
                                                    @foreach($activity->properties['attributes'] as $field => $value)
                                                        @if(!in_array($field, ['recipient', 'recipient_type', 'message_type', 'session_id', 'template_id', 'action', 'error']))
                                                            <div class="text-xs text-gray-600 dark:text-zinc-400">
                                                                <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong> {{ is_array($value) ? json_encode($value) : ($value ?? 'N/A') }}
                                                            </div>
                                                        @endif
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
