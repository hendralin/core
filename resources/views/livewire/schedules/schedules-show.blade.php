<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show Schedule') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Schedule details') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('schedules.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Schedules">Back</flux:button>
        @can('schedule.edit')
            <flux:button variant="filled" size="sm" href="{{ route('schedules.edit', $schedule->id) }}" wire:navigate icon="pencil-square" class="ml-1">Edit</flux:button>
        @endcan

        <div class="mt-4 w-full max-w-7xl">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Schedule Information -->
                <div class="lg:col-span-2">
                    <!-- Schedule Basic Info -->
                    <div class="mb-6">
                        <flux:heading size="xl">{{ $schedule->name }}</flux:heading>
                        <div class="mt-2">
                            @if ($schedule->is_active)
                                <flux:badge color="green">Active</flux:badge>
                            @else
                                <flux:badge color="red">Inactive</flux:badge>
                            @endif
                            <flux:badge color="blue" class="ml-2">{{ ucfirst($schedule->frequency) }}</flux:badge>
                        </div>
                    </div>

                    <flux:heading size="lg">Schedule Information</flux:heading>
                    <div class="grid grid-cols-2 gap-4 mt-3 mb-6">
                        <div>
                            <flux:heading size="sm">Created</flux:heading>
                            <flux:text class="mt-1">{{ $schedule->created_at->format('M d, Y H:i') }}</flux:text>
                            <flux:text class="text-sm text-gray-500 dark:text-zinc-500">by {{ $schedule->createdBy->name ?? 'Unknown' }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Updated</flux:heading>
                            <flux:text class="mt-1">{{ $schedule->updated_at->format('M d, Y H:i') }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Session</flux:heading>
                            <flux:text class="mt-1">
                                @if($schedule->wahaSession)
                                    {{ $schedule->wahaSession->name }}
                                @else
                                    <span class="text-gray-400 dark:text-zinc-400">No session assigned</span>
                                @endif
                            </flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Usage Count</flux:heading>
                            <flux:text class="mt-1">{{ $schedule->usage_count }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Last Run</flux:heading>
                            <flux:text class="mt-1">
                                @if($schedule->last_run)
                                    @php
                                        $userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');
                                        $lastRunInUserTz = $schedule->last_run->setTimezone($userTimezone);
                                    @endphp
                                    {{ $lastRunInUserTz->format('M d, Y H:i') }}
                                    <div class="text-xs text-gray-400">{{ $schedule->last_run->diffForHumans() }}</div>
                                    <div class="text-xs text-gray-400">({{ $userTimezone }})</div>
                                @else
                                    <span class="text-gray-400 dark:text-zinc-400">Never</span>
                                @endif
                            </flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Next Run</flux:heading>
                            <flux:text class="mt-1">
                                @if($schedule->next_run)
                                    @php
                                        $userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');
                                        $nextRunInUserTz = $schedule->next_run->setTimezone($userTimezone);
                                    @endphp
                                    {{ $nextRunInUserTz->format('M d, Y H:i') }}
                                    <div class="text-xs text-gray-400">{{ $schedule->next_run->diffForHumans() }}</div>
                                    <div class="text-xs text-gray-400">({{ $userTimezone }})</div>
                                @else
                                    <span class="text-gray-400 dark:text-zinc-400">Not scheduled</span>
                                @endif
                            </flux:text>
                        </div>
                    </div>

                    <flux:heading size="lg" class="mt-6">Recipients</flux:heading>
                    <div class="mt-3 p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg border">
                        @php
                            $recipients = $schedule->recipients;
                        @endphp

                        @if($recipients->isNotEmpty())
                            {{-- New way: Display from pivot table --}}
                            @php
                                $contacts = $recipients->where('recipient_type', 'contact');
                                $groups = $recipients->where('recipient_type', 'group');
                                $numbers = $recipients->where('recipient_type', 'number');
                            @endphp

                            @if($contacts->isNotEmpty())
                                <div class="mb-4">
                                    <flux:heading size="sm" class="mb-2">Contacts ({{ $contacts->count() }})</flux:heading>
                                    <div class="space-y-2">
                                        @foreach($contacts as $recipient)
                                            <div class="p-2 bg-white dark:bg-zinc-700 rounded border border-gray-200 dark:border-zinc-600">
                                                @if($recipient->contact)
                                                    <flux:text><strong>{{ $recipient->contact->name ?? 'Unknown' }}</strong></flux:text>
                                                    <div class="mt-1"><flux:text class="text-xs text-gray-500">{{ $recipient->contact->wa_id }}</flux:text></div>
                                                @else
                                                    <flux:text><strong>Contact ID:</strong> {{ $recipient->contact_id }}</flux:text>
                                                    @if($recipient->received_number)
                                                        <div class="mt-1"><flux:text class="text-xs text-gray-500">{{ $recipient->received_number }}</flux:text></div>
                                                    @endif
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($groups->isNotEmpty())
                                <div class="mb-4">
                                    <flux:heading size="sm" class="mb-2">Groups ({{ $groups->count() }})</flux:heading>
                                    <div class="space-y-2">
                                        @foreach($groups as $recipient)
                                            <div class="p-2 bg-white dark:bg-zinc-700 rounded border border-gray-200 dark:border-zinc-600">
                                                @if($recipient->group)
                                                    <flux:text><strong>{{ $recipient->group->name }}</strong></flux:text>
                                                    <div class="mt-1"><flux:text class="text-xs text-gray-500">Group WA ID: {{ $recipient->group_wa_id }}</flux:text></div>
                                                @else
                                                    <flux:text><strong>Group ID:</strong> {{ $recipient->group_id }}</flux:text>
                                                    @if($recipient->group_wa_id)
                                                        <div class="mt-1"><flux:text class="text-xs text-gray-500">Group WA ID: {{ $recipient->group_wa_id }}</flux:text></div>
                                                    @endif
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($numbers->isNotEmpty())
                                <div class="mb-4">
                                    <flux:heading size="sm" class="mb-2">Phone Numbers ({{ $numbers->count() }})</flux:heading>
                                    <div class="space-y-2">
                                        @foreach($numbers as $recipient)
                                            <div class="p-2 bg-white dark:bg-zinc-700 rounded border border-gray-200 dark:border-zinc-600">
                                                <flux:text><strong>{{ $recipient->received_number ?? preg_replace('/@.+$/', '', $recipient->wa_id) }}</strong></flux:text>
                                                @if($recipient->wa_id)
                                                    <div class="mt-1"><flux:text class="text-xs text-gray-500">WA ID: {{ $recipient->wa_id }}</flux:text></div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="mt-3 pt-3 border-t border-gray-300 dark:border-zinc-600">
                                <flux:text class="text-sm text-gray-600 dark:text-zinc-400">
                                    <strong>Total Recipients:</strong> {{ $recipients->count() }}
                                </flux:text>
                            </div>
                        @else
                            {{-- Legacy support: Display from old fields --}}
                            @if($schedule->group_wa_id)
                                <div class="mb-3">
                                    <flux:badge color="yellow" class="mb-2">Legacy Format</flux:badge>
                                    @if($schedule->group)
                                        <flux:text><strong>Group:</strong> {{ $schedule->group->name }}</flux:text>
                                        <div class="mt-2"><flux:text class="text-xs text-gray-500"><strong>Group WA ID:</strong> {{ $schedule->group_wa_id }}</flux:text></div>
                                    @else
                                        <flux:text><strong>Group WA ID:</strong> {{ $schedule->group_wa_id }}</flux:text>
                                    @endif
                                </div>
                            @elseif($schedule->received_number)
                                <div class="mb-3">
                                    <flux:badge color="yellow" class="mb-2">Legacy Format</flux:badge>
                                    @if($schedule->contact)
                                        <flux:text><strong>Contact:</strong> {{ $schedule->contact->name ?? $schedule->contact->wa_id }}</flux:text>
                                        <div class="mt-2"><flux:text class="text-xs text-gray-500"><strong>Received Number:</strong> {{ $schedule->received_number }}</flux:text></div>
                                    @else
                                        <flux:text><strong>Phone Number:</strong> {{ $schedule->received_number }}</flux:text>
                                    @endif
                                </div>
                            @elseif($schedule->wa_id)
                                <div class="mb-3">
                                    <flux:badge color="yellow" class="mb-2">Legacy Format</flux:badge>
                                    <flux:text><strong>Phone Number:</strong> {{ $schedule->wa_id }}</flux:text>
                                </div>
                            @elseif($schedule->group)
                                <div class="mb-3">
                                    <flux:badge color="yellow" class="mb-2">Legacy Format</flux:badge>
                                    <flux:text><strong>Group:</strong> {{ $schedule->group->name }}</flux:text>
                                </div>
                            @elseif($schedule->contact)
                                <div class="mb-3">
                                    <flux:badge color="yellow" class="mb-2">Legacy Format</flux:badge>
                                    <flux:text><strong>Contact:</strong> {{ $schedule->contact->name ?? $schedule->contact->wa_id }}</flux:text>
                                </div>
                            @else
                                <flux:text class="text-gray-400">No recipient set</flux:text>
                            @endif
                        @endif
                    </div>

                    <flux:heading size="lg" class="mt-6">Schedule Details</flux:heading>
                    <div class="mt-3 p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg border">
                        <flux:text><strong>Frequency:</strong> {{ ucfirst($schedule->frequency) }}</flux:text>
                        @if($schedule->frequency === 'weekly' && $schedule->day_of_week !== null)
                            @php
                                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            @endphp
                            <div class="mt-2"><flux:text><strong>Day of Week:</strong> {{ $days[$schedule->day_of_week] ?? '' }}</flux:text></div>
                        @endif
                        @if($schedule->frequency === 'monthly' && $schedule->day_of_month !== null)
                            <div class="mt-2"><flux:text><strong>Day of Month:</strong> {{ $schedule->day_of_month }}</flux:text></div>
                        @endif
                        @php
                            $userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');
                        @endphp
                        <div class="mt-2"><flux:text><strong>Time:</strong> {{ $schedule->time ? $schedule->time->format('H:i') : 'Not set' }} ({{ $userTimezone }})</flux:text></div>
                    </div>

                    <flux:heading size="lg" class="mt-6">Description</flux:heading>
                    <div class="mt-3 p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg border">
                        <flux:text>{{ $schedule->description }}</flux:text>
                    </div>

                    <flux:heading size="lg" class="mt-6">Message Content</flux:heading>
                    <div class="mt-3 p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg border">
                        <flux:text class="whitespace-pre-wrap">{{ $schedule->message }}</flux:text>
                    </div>
                </div>

                <!-- Right Column - Message Preview -->
                <div class="lg:col-span-1">
                    <div class="sticky top-6">
                        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden">
                            <div class="p-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
                                <flux:heading size="md">Message Preview</flux:heading>
                                <flux:text class="text-sm text-gray-600 dark:text-zinc-400 mt-1">How the message will appear</flux:text>
                            </div>

                            <div class="p-4 space-y-4">
                                <!-- WhatsApp-like message bubble -->
                                <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-3 ml-auto">
                                    @php
                                        $previewMessage = $schedule->message;
                                        // Replace *text* with <strong>text</strong>
                                        $previewMessage = preg_replace('/\*(.+?)\*/s', '<strong>$1</strong>', $previewMessage);
                                        // Replace _text_ with <em>text</em>
                                        $previewMessage = preg_replace('/\_(.+?)\_/s', '<em>$1</em>', $previewMessage);
                                    @endphp
                                    <div class="text-sm text-gray-900 dark:text-zinc-100 whitespace-pre-wrap">{!! $previewMessage !!}</div>
                                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-2 text-right">
                                        12:34 PM ✓✓
                                    </div>
                                </div>

                                <!-- Additional info -->
                                <div class="text-xs text-gray-600 dark:text-zinc-400 space-y-1">
                                    <div>• Frequency: {{ ucfirst($schedule->frequency) }}</div>
                                    @if($schedule->next_run)
                                        <div>• Next run: {{ $schedule->next_run->diffForHumans() }}</div>
                                    @endif
                                    <div>• Supports formatting: *bold*, _italic_</div>
                                    <div>• Character count: {{ strlen($schedule->message ?? '') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
