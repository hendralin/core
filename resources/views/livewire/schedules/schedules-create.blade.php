<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Schedule') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for create new schedule') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('schedules.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Schedules">Back</flux:button>

        <div class="mt-4 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Create Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                        <flux:heading size="lg" class="mb-6">Create Schedule</flux:heading>

                        <form wire:submit="submit" class="space-y-6">
                            <flux:select wire:model.live="waha_session_id" label="Session" description="Select the session for this schedule">
                                <flux:select.option value="" label="Select Session" />
                                @foreach($sessions as $session)
                                    <flux:select.option value="{{ $session->id }}">{{ $session->name }}</flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:input wire:model="name" label="Schedule Name" placeholder="e.g., Daily Reminder" />

                            <flux:input wire:model="description" label="Description" placeholder="Brief description of this schedule" />

                            <flux:textarea wire:model.live="message" label="Message Content" placeholder="Enter the message to be sent..." rows="6" />

                            <!-- Recipient Type -->
                            <flux:radio.group wire:model.live="recipientType" label="Recipient Type" description="Select where the message will be sent (you can select multiple recipients)">
                                <flux:radio
                                    value="contact"
                                    label="Contact"
                                    description="Send message to one or more contacts from your contact list"
                                />
                                <flux:radio
                                    value="group"
                                    label="Group"
                                    description="Send message to one or more WhatsApp groups"
                                />
                                <flux:radio
                                    value="number"
                                    label="Phone Number"
                                    description="Send message directly to one or more phone numbers"
                                />
                            </flux:radio.group>

                            <!-- Recipient Selection -->
                            @if($recipientType === 'contact')
                                <div>
                                    <flux:label>Select Contacts <span class="text-red-500">*</span></flux:label>
                                    <flux:text class="text-sm text-gray-600 dark:text-zinc-400 mb-2">Select one or more contacts to receive the message</flux:text>
                                    @if(count($contacts) > 0)
                                        <div class="mt-2 max-h-60 overflow-y-auto border border-gray-200 dark:border-zinc-700 rounded-lg p-3">
                                            <flux:checkbox.group wire:model="contact_ids">
                                                <flux:checkbox.all label="Select all contacts" />
                                                @foreach($contacts as $contact)
                                                    <flux:checkbox
                                                        value="{{ $contact->id }}"
                                                        label="{{ $contact->name ?? $contact->wa_id }}"
                                                    />
                                                @endforeach
                                            </flux:checkbox.group>
                                        </div>
                                    @else
                                        <div class="mt-2 p-3 border border-gray-200 dark:border-zinc-700 rounded-lg bg-gray-50 dark:bg-zinc-800/50">
                                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">No contacts available. Please select a session first.</flux:text>
                                        </div>
                                    @endif
                                    @error('contact_ids') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @elseif($recipientType === 'group')
                                <div>
                                    <flux:label>Select Groups <span class="text-red-500">*</span></flux:label>
                                    <flux:text class="text-sm text-gray-600 dark:text-zinc-400 mb-2">Select one or more groups to receive the message</flux:text>
                                    @if(count($groups) > 0)
                                        <div class="mt-2 max-h-60 overflow-y-auto border border-gray-200 dark:border-zinc-700 rounded-lg p-3">
                                            <flux:checkbox.group wire:model="group_ids">
                                                <flux:checkbox.all label="Select all groups" />
                                                @foreach($groups as $group)
                                                    <flux:checkbox
                                                        value="{{ $group->id }}"
                                                        label="{{ $group->name }}"
                                                    />
                                                @endforeach
                                            </flux:checkbox.group>
                                        </div>
                                    @else
                                        <div class="mt-2 p-3 border border-gray-200 dark:border-zinc-700 rounded-lg bg-gray-50 dark:bg-zinc-800/50">
                                            <flux:text class="text-sm text-gray-600 dark:text-zinc-400">No groups available. Please select a session first.</flux:text>
                                        </div>
                                    @endif
                                    @error('group_ids') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @elseif($recipientType === 'number')
                                <div>
                                    <flux:label>WhatsApp Numbers <span class="text-red-500">*</span></flux:label>
                                    <flux:text class="text-sm text-gray-600 dark:text-zinc-400 mb-2">Enter one or more phone numbers with country code</flux:text>
                                    <div class="space-y-2">
                                        @foreach($wa_ids as $index => $waId)
                                            <div class="flex gap-2">
                                                <flux:input
                                                    wire:model="wa_ids.{{ $index }}"
                                                    placeholder="e.g., 6281234567890"
                                                    class="flex-1"
                                                />
                                                @if(count($wa_ids) > 1)
                                                    <flux:button
                                                        type="button"
                                                        variant="ghost"
                                                        size="sm"
                                                        wire:click="removeWaId({{ $index }})"
                                                        icon="x-mark"
                                                        tooltip="Remove this number"
                                                    />
                                                @endif
                                            </div>
                                        @endforeach
                                        <flux:button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            wire:click="addWaId"
                                            icon="plus"
                                        >
                                            Add Another Number
                                        </flux:button>
                                    </div>
                                    @error('wa_ids') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                    @error('wa_ids.*') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Frequency -->
                            <flux:radio.group wire:model.live="frequency" label="Frequency" description="How often the message should be sent">
                                <flux:radio
                                    value="daily"
                                    label="Daily"
                                    description="Send message every day at the specified time"
                                />
                                <flux:radio
                                    value="weekly"
                                    label="Weekly"
                                    description="Send message once per week on the selected day"
                                />
                                <flux:radio
                                    value="monthly"
                                    label="Monthly"
                                    description="Send message once per month on the selected day"
                                />
                            </flux:radio.group>

                            <!-- Day of Week (for weekly) -->
                            @if($frequency === 'weekly')
                                <flux:select wire:model="day_of_week" label="Day of Week">
                                    <flux:select.option value="0">Sunday</flux:select.option>
                                    <flux:select.option value="1">Monday</flux:select.option>
                                    <flux:select.option value="2">Tuesday</flux:select.option>
                                    <flux:select.option value="3">Wednesday</flux:select.option>
                                    <flux:select.option value="4">Thursday</flux:select.option>
                                    <flux:select.option value="5">Friday</flux:select.option>
                                    <flux:select.option value="6">Saturday</flux:select.option>
                                </flux:select>
                            @endif

                            <!-- Day of Month (for monthly) -->
                            @if($frequency === 'monthly')
                                <flux:select wire:model="day_of_month" label="Day of Month">
                                    @for($i = 1; $i <= 28; $i++)
                                        <flux:select.option value="{{ $i }}">Day {{ $i }}</flux:select.option>
                                    @endfor
                                </flux:select>
                            @endif

                            <flux:input type="time" wire:model="time" label="Time" description="Time to send the message (Timezone: {{ $userTimezone ?? config('app.timezone', 'UTC') }})" />

                            <flux:checkbox wire:model="is_active" label="Active" description="Enable this schedule" />

                            <div class="flex gap-3 pt-4">
                                <flux:button type="submit" variant="primary" class="cursor-pointer">Create Schedule</flux:button>
                                <flux:button variant="ghost" href="{{ route('schedules.index') }}" wire:navigate>Cancel</flux:button>
                            </div>
                        </form>
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
                                        $previewMessage = $message ?? '';
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

                                <!-- Schedule Info -->
                                <div class="text-xs text-gray-600 dark:text-zinc-400 space-y-1">
                                    <div>• Frequency: {{ ucfirst($frequency) }}</div>
                                    @if($frequency === 'weekly' && $day_of_week !== null)
                                        @php
                                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                        @endphp
                                        <div>• Day: {{ $days[$day_of_week] ?? '' }}</div>
                                    @endif
                                    @if($frequency === 'monthly' && $day_of_month !== null)
                                        <div>• Day of month: {{ $day_of_month }}</div>
                                    @endif
                                    @if($time)
                                        <div>• Time: {{ $time }}</div>
                                    @endif
                                    <div>• Supports formatting: *bold*, _italic_</div>
                                    <div>• Character count: {{ strlen($message ?? '') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
