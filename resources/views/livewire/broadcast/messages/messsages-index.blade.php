<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Broadcast Messages') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Send and manage WhatsApp messages via WAHA') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        @session('success')
            <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
        @endsession

        @session('error')
            <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
        @endsession

        <!-- WAHA Status -->
        @if(!$wahaStatus['connected'])
            <flux:callout variant="warning" icon="exclamation-triangle" heading="WAHA API Status" class="mb-4">
                <flux:heading size="lg">{{ $wahaStatus['message'] }}</flux:heading>
                <flux:text>
                    Messages cannot be sent until WAHA API connection is restored.
                    Please check your WAHA configuration and network connectivity.
                </flux:text>
            </flux:callout>
        @endif

        <!-- Search & Filters -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 mb-6">
            <div class="p-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
                <div class="space-y-4 lg:space-y-0 lg:flex lg:flex-wrap lg:items-center lg:justify-between lg:gap-4">
                    <!-- Search -->
                    <div class="w-full lg:flex-1 lg:max-w-md">
                        <div class="relative">
                            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search messages..." clearable />
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
                        @if($search || $selectedSession)
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
                <div class="flex flex-wrap gap-2">
                    @if($wahaStatus['connected'])
                        @can('message.create')
                            <flux:button wire:click="openSendModal" variant="primary" size="sm" icon="paper-airplane" tooltip="Send Message" class="cursor-pointer">Send Message</flux:button>
                        @endcan
                    @endif

                    @can('message.audit')
                        <flux:button variant="ghost" href="{{ route('messages.audit') }}" size="sm" wire:navigate icon="document-text" tooltip="Audit Trail" class="cursor-pointer">Audit</flux:button>
                    @endcan

                    <div wire:loading>
                        <flux:icon.loading class="text-red-600" />
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="text-xs text-gray-700 bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                No.
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider w-1/3 min-w-[300px]">
                                <button wire:click="sortBy('message')" class="flex items-center space-x-1 cursor-pointer uppercase hover:text-gray-700 dark:hover:text-gray-300">
                                    <span>Message</span>
                                    @if($sortField === 'message')
                                        <flux:icon.chevron-up class="h-4 w-4 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                        <flux:icon.chevron-down class="h-4 w-4 -mt-2 {{ $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Recipient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Session</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Template</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                <button wire:click="sortBy('status')" class="flex items-center space-x-1 cursor-pointer uppercase hover:text-gray-700 dark:hover:text-gray-300">
                                    <span>Status</span>
                                    @if($sortField === 'status')
                                        <flux:icon.chevron-up class="h-4 w-4 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                        <flux:icon.chevron-down class="h-4 w-4 -mt-2 {{ $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 cursor-pointer uppercase hover:text-gray-700 dark:hover:text-gray-300">
                                    <span>Sent At</span>
                                    @if($sortField === 'created_at')
                                        <flux:icon.chevron-up class="h-4 w-4 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                        <flux:icon.chevron-down class="h-4 w-4 -mt-2 {{ $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                    @endif
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                        @if(isset($messages) && $messages->count() > 0)
                            @foreach($messages as $index => $message)
                                <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400 text-center">
                                        {{ $messages->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $msg = Str::limit($message->message, 100);

                                            // Replace *text* with <strong>text</strong>
                                            $msg = preg_replace('/\*(.+?)\*/s', '<strong>$1</strong>', $msg);

                                            // Replace _text_ with <em>text</em>
                                            $msg = preg_replace('/\_(.+?)\_/s', '<em>$1</em>', $msg);
                                        @endphp
                                        <flux:modal.trigger name="preview-message">
                                            <div class="text-sm max-w-xs truncate md:max-w-none md:whitespace-normal text-gray-900 dark:text-zinc-100 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors" wire:click="setMessageToPreview({{ $message->id }})" title="Click to preview message">
                                                {!! $msg !!}
                                            </div>
                                        </flux:modal.trigger>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        @if($message->received_number)
                                            @if($message->contact && ($message->contact->name || $message->contact->push_name))
                                                <div class="font-semibold text-gray-900 dark:text-zinc-100">{{ $message->contact->name ?? $message->contact->push_name }}</div>
                                            @endif
                                            <div>{{ $message->received_number }}</div>
                                            <div class="text-xs">
                                                @php
                                                    $numberRaw = $message->received_number;
                                                    // Check pattern: if ends with @c.us, it is a "Contact", otherwise "Number"
                                                    if (is_string($numberRaw) && preg_match('/^\d+@c\.us$/', $numberRaw)) {
                                                        $recipientTypeLabel = "Contact";
                                                    } else {
                                                        $recipientTypeLabel = "Number";
                                                    }
                                                @endphp
                                                {{ $recipientTypeLabel }}
                                            </div>
                                        @elseif($message->group_wa_id)
                                            @if($message->group && $message->group->name)
                                                <div class="font-semibold text-gray-900 dark:text-zinc-100">{{ $message->group->name }}</div>
                                            @endif
                                            <div>{{ $message->group_wa_id }}</div>
                                            <div class="text-xs">Group</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        @if($message->wahaSession)
                                            <div>{{ $message->wahaSession->name }}</div>
                                            <div class="text-xs">{{ $message->wahaSession->session_id }}</div>
                                        @else
                                            No session
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        @if($message->template)
                                            {{ $message->template->name }}
                                        @else
                                            Direct
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($message->status === 'sent')
                                            <flux:badge color="green" size="sm">Sent</flux:badge>
                                        @elseif($message->status === 'failed')
                                            <flux:modal.trigger name="resend-message-modal">
                                                <flux:badge
                                                    color="red"
                                                    size="sm"
                                                    class="cursor-pointer hover:opacity-80 transition-opacity"
                                                    wire:click="setMessageToResend({{ $message->id }})"
                                                    title="Click to resend message"
                                                >
                                                    Failed
                                                </flux:badge>
                                            </flux:modal.trigger>
                                        @else
                                            <flux:badge color="gray" size="sm">Pending</flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        {{ $message->created_at->format('M d, Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-zinc-400">
                                        <flux:icon.inbox class="mx-auto h-12 w-12 text-gray-400 dark:text-zinc-400" />
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-zinc-100">No messages</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Get started by sending your first message.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($messages) && $messages->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Send Message Modal -->
    <flux:modal name="send-message-modal" class="md:w-2xl">
        <form wire:submit="sendMessage" class="space-y-6">
            <div>
                <flux:heading size="lg">Send Message</flux:heading>
                <flux:text class="mt-2">Compose and send a WhatsApp message.</flux:text>
            </div>

            {{-- @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/10 rounded-lg p-4 border border-red-200 dark:border-red-800">
                    <flux:heading size="sm" class="mb-3 text-red-800 dark:text-red-200">Errors</flux:heading>
                    <ul class="list-disc list-inside text-sm text-red-500 dark:text-red-400">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}

            <!-- Session Selection -->
            <div>
                <flux:label for="selectedSession">WAHA Session <span class="text-red-500">*</span></flux:label>
                <flux:select wire:model.live.debounce.300ms="messageSession" class="mt-1">
                    <flux:select.option value="">Select a session...</flux:select.option>
                    @foreach($sessions as $session)
                        <flux:select.option value="{{ $session->id }}">{{ $session->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                @error('messageSession') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            @if($messageSession)
                <!-- Message Type -->
                <flux:fieldset>
                    <flux:legend class="text-sm">Message Type <span class="text-red-500">*</span></flux:legendc>
                    <flux:radio.group wire:model.live="messageType">
                        <flux:radio value="direct" label="Direct Message" description="Compose your message directly" checked />
                        <flux:radio value="template" label="Use WhatsApp Template" description="Select from predefined message templates" />
                    </flux:radio.group>
                </flux:fieldset>

                <!-- Direct Message -->
                @if($messageType === 'direct' && $recipientType !== 'recipients')
                    <div>
                        <flux:label for="directMessage">Message Content <span class="text-red-500">*</span></flux:label>
                        <flux:textarea wire:model.live="directMessage" rows="4" class="mt-1" placeholder="Type your message here (max 1024 characters)" maxlength="1024"></flux:textarea>
                        @error('directMessage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <div class="mt-2 text-xs text-gray-600 dark:text-zinc-400 space-y-1">
                            <div>• Supports formatting: *bold*, _italic_</div>
                            <div>• Character count: {{ strlen($directMessage ?? '') }}</div>
                        </div>
                    </div>
                @endif

                <!-- Template Selection -->
                @if($messageType === 'template')
                    <div>
                        <flux:label for="selectedTemplate">Select Template <span class="text-red-500">*</span></flux:label>
                        <flux:select wire:model.live="selectedTemplate" class="mt-1">
                            <flux:select.option value="">Choose a template...</flux:select.option>
                            @foreach($templates as $template)
                                <flux:select.option value="{{ $template->id }}">{{ $template->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('selectedTemplate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                        @if($selectedTemplate)
                            @php
                                $template = $templates->find($selectedTemplate);
                            @endphp
                            @if($template)
                                <div class="mt-3 bg-green-50 dark:bg-green-900/10 rounded-lg p-4 border border-green-200 dark:border-green-800">
                                    <flux:heading size="sm" class="mb-3 text-green-800 dark:text-green-200">Template Preview</flux:heading>

                                    <!-- WhatsApp-like message bubble -->
                                    <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-3">
                                        <div class="text-sm text-gray-900 dark:text-zinc-100">
                                            @if($template->header)
                                                <div class="font-semibold mb-2">{{ $template->header }}</div>
                                            @endif
                                            <div class="whitespace-pre-wrap">{{ $template->body }}</div>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-zinc-400 mt-2 text-right">
                                            12:34 PM ✓✓
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Parameter Values -->
                    @if($selectedTemplate && $recipientType !== 'recipients' && (!empty($templateParams['header']) || !empty($templateParams['body'])))
                        <div>
                            <flux:label>Parameter Values</flux:label>
                            <div class="mt-3 space-y-4">
                                <!-- Header Parameters -->
                                @if(!empty($templateParams['header']))
                                    <div>
                                        <flux:label class="text-sm font-medium text-gray-700 dark:text-zinc-300">Header Parameters</flux:label>
                                        <div class="mt-2 space-y-3">
                                            @foreach($templateParams['header'] as $param => $value)
                                                <div>
                                                    <flux:label for="header_param_{{ $param }}" class="text-sm">Parameter {{ ucfirst($param) }} <span class="text-red-500">*</span></flux:label>
                                                    <flux:input wire:model="templateParams.header.{{ $param }}" id="header_param_{{ $param }}" class="mt-1" placeholder="Enter value for header parameter {{ $param }}" />
                                                    @error("templateParams.header.{$param}") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Body Parameters -->
                                @if(!empty($templateParams['body']))
                                    <div>
                                        <flux:label class="text-sm font-medium text-gray-700 dark:text-zinc-300">Body Parameters</flux:label>
                                        <div class="mt-2 space-y-3">
                                            @foreach($templateParams['body'] as $param => $value)
                                                <div>
                                                    <flux:label for="body_param_{{ $param }}" class="text-sm">Parameter {{ ucfirst($param) }} <span class="text-red-500">*</span></flux:label>
                                                    <flux:input wire:model="templateParams.body.{{ $param }}" id="body_param_{{ $param }}" class="mt-1" placeholder="Enter value for body parameter {{ $param }}" />
                                                    @error("templateParams.body.{$param}") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Recipient Type -->
                <flux:fieldset>
                    <flux:legend class="text-sm">Recipient Type <span class="text-red-500">*</span></flux:legend>
                <flux:radio.group wire:model.live="recipientType">
                    <flux:radio value="number" label="Send to Number" description="Send message to WhatsApp number" checked />
                    <flux:radio value="contact" label="Send to Contact" description="Send message to individual phone number" checked />
                    <flux:radio value="group" label="Send to Group(s)" description="Send message to WhatsApp groups" />
                    <flux:radio value="recipients" label="Send to Recipient(s)" description="Send message to recipients list" />
                </flux:radio.group>
                </flux:fieldset>

                <!-- Number Selection -->
                @if($recipientType === 'number')
                    <div>
                        <flux:label for="contactNumber">Enter Number <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="contactNumber" class="mt-1" placeholder="Enter WhatsApp number" />
                        @error('contactNumber') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <div class="mt-2 text-xs text-gray-600 dark:text-zinc-400 space-y-1">
                            <div>• Format: 6281234567890 (without '+' prefix)</div>
                        </div>
                    </div>
                @endif

                <!-- Contact Selection -->
                @if($recipientType === 'contact')
                    <div>
                        <flux:label for="selectedContactId">Select Contact <span class="text-red-500">*</span></flux:label>
                        <flux:select wire:model="selectedContactId" class="mt-1">
                            <flux:select.option value="">Choose a contact...</flux:select.option>
                            @if($contacts && $contacts->count() > 0)
                                @foreach($contacts as $contact)
                                    <flux:select.option value="{{ $contact->id }}">
                                        {{ $contact->name ?: $contact->push_name ?: $contact->wa_id }}
                                        @if($contact->name || $contact->push_name)
                                            ({{ $contact->wa_id }})
                                        @endif
                                    </flux:select.option>
                                @endforeach
                            @endif
                        </flux:select>

                        @if($contacts && $contacts->count() === 0)
                            <div class="mt-2 text-sm text-gray-500 dark:text-zinc-400">
                                @if($messageSession)
                                    No contacts found for the selected session.
                                @else
                                    Select a session above to see available contacts.
                                @endif
                            </div>
                        @endif

                        @error('selectedContactId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Group Selection -->
                @if($recipientType === 'group')
                    <div>
                        <flux:label>Select Groups <span class="text-red-500">*</span></flux:label>
                        <div class="mt-2 max-h-40 overflow-y-auto border border-gray-200 dark:border-zinc-700 rounded-lg p-3">
                            @if($groups && $groups->count() > 0)
                                <flux:checkbox.group wire:model="selectedGroups" label="{{ $messageSession ? $sessions->find($messageSession)->name  : 'available' }} ({{ $groups->count() }} groups found)">
                                    <flux:checkbox.all label="Select all groups in the session" />
                                    @foreach($groups as $group)
                                        <flux:checkbox
                                            value="{{ $group->id }}"
                                            label="{{ $group->name }}"
                                        />
                                    @endforeach
                                </flux:checkbox.group>
                            @else
                                <div class="text-center py-6">
                                    <div class="text-gray-500 dark:text-zinc-400">
                                        <flux:icon.users class="mx-auto h-8 w-8 text-gray-400 dark:text-zinc-400 mb-2" />
                                        <p class="text-sm font-medium">No groups available</p>
                                        <p class="text-xs mt-1">
                                            @if($messageSession)
                                                No groups found for the selected session. Try selecting a different session or create a new group.
                                            @else
                                                Select a session above to see available groups, or create your first group.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @error('selectedGroups') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Recipients Bulk Upload -->
                @if($recipientType === 'recipients')
                    <div>
                        <flux:label>Bulk Recipients Upload</flux:label>
                        <div class="mt-3 space-y-4">
                            <!-- Download Template -->
                            <div>
                                <flux:label class="text-sm font-medium text-gray-700 dark:text-zinc-300">1. Download Template</flux:label>
                                <div class="mt-2">
                                    <flux:button wire:click="downloadTemplate" variant="outline" icon="cloud-arrow-down" class="cursor-pointer">
                                        Download Template
                                    </flux:button>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-zinc-400">
                                        Download an Excel template with the correct format for {{ $messageType === 'direct' ? 'direct message' : 'template message' }}.
                                    </p>
                                </div>
                            </div>

                            <!-- Upload File -->
                            <div>
                                <flux:label for="recipientsFile" class="text-sm font-medium text-gray-700 dark:text-zinc-300">2. Upload Recipients File <span class="text-red-500">*</span></flux:label>
                                <div class="mt-2">
                                    <flux:input type="file" wire:model="recipientsFile" accept=".xlsx,.xls,.csv" class="cursor-pointer" />
                                    <div class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                                        Upload an Excel (.xlsx, .xls) or CSV file with a list of phone numbers and messages.
                                    </div>
                                    @error('recipientsFile') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Preview -->
                            @if($recipientsFile)
                                <div>
                                    <flux:label class="text-sm font-medium text-gray-700 dark:text-zinc-300">3. Preview Recipients</flux:label>
                                    <div class="mt-2 max-h-40 overflow-y-auto border border-gray-200 dark:border-zinc-700 rounded-lg p-3 bg-gray-50 dark:bg-zinc-800">
                                        @if($parsedRecipients && count($parsedRecipients) > 0)
                                            <div class="space-y-2">
                                                <div class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                                                    {{ count($parsedRecipients) }} recipients found
                                                </div>
                                                @foreach(array_slice($parsedRecipients, 0, 5) as $recipient)
                                                    <div class="text-sm text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-700 p-2 rounded">
                                                        <div class="font-medium">{{ $recipient['phone'] }}</div>
                                                        @if(isset($recipient['message']))
                                                            <div class="text-xs mt-1 truncate">{{ Str::limit($recipient['message'], 50) }}</div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                @if(count($parsedRecipients) > 5)
                                                    <div class="text-xs text-gray-500 dark:text-zinc-400 text-center pt-2">
                                                        ... and {{ count($parsedRecipients) - 5 }} more recipients
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <div class="text-gray-500 dark:text-zinc-400">
                                                    <flux:icon.document class="mx-auto h-8 w-8 mb-2" />
                                                    <p class="text-sm">No valid recipients found in the file.</p>
                                                    <p class="text-xs mt-1">Please check your file format and try again.</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-zinc-700">
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
                </flux:modal.close>
                @if($wahaStatus['connected'])
                <flux:button type="submit" variant="primary" class="cursor-pointer" wire:loading.attr="disabled">Send Message</flux:button>
                @endif
            </div>
        </form>
    </flux:modal>

    <!-- Resend Message Confirmation Modal -->
    <flux:modal name="resend-message-modal" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Resend message?</flux:heading>
                <flux:text class="mt-2">
                    You're about to resend this failed message.<br>
                    This will attempt to send the message again to the same recipient.
                </flux:text>
            </div>

            @if($messageToResend)
                <div class="space-y-4">
                    <!-- Message Info -->
                    <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <flux:heading size="md">Message Details</flux:heading>
                                @if($messageToResend->template)
                                    <flux:badge color="blue" size="sm">Template</flux:badge>
                                @else
                                    <flux:badge color="gray" size="sm">Direct</flux:badge>
                                @endif
                            </div>
                            <div class="text-sm text-gray-600 dark:text-zinc-400 space-y-1">
                                <div>Recipient: {{ $messageToResend->received_number ?? $messageToResend->group_wa_id ?? 'Unknown' }}</div>
                                @if($messageToResend->template)
                                    <div>Template: {{ $messageToResend->template->name }}</div>
                                @endif
                                @if($messageToResend->wahaSession)
                                    <div>Session: {{ $messageToResend->wahaSession->name }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Message Preview -->
                    <div class="bg-green-50 dark:bg-green-900/10 rounded-lg p-4 border border-green-200 dark:border-green-800">
                        <flux:heading size="sm" class="mb-3 text-green-800 dark:text-green-200">Message Preview</flux:heading>

                        <!-- WhatsApp-like message bubble -->
                        <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-3 max-w-sm">
                            <div class="text-sm text-gray-900 dark:text-zinc-100">
                                @php
                                    $previewMsg = $messageToResend->message;
                                    // Replace *text* with <strong>text</strong>
                                    $previewMsg = preg_replace('/\*(.+?)\*/s', '<strong>$1</strong>', $previewMsg);
                                    // Replace _text_ with <em>text</em>
                                    $previewMsg = preg_replace('/\_(.+?)\_/s', '<em>$1</em>', $previewMsg);
                                @endphp
                                <div class="whitespace-pre-wrap">{!! $previewMsg !!}</div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-zinc-400 mt-2 text-right">
                                {{ $messageToResend->created_at->format('g:i A') }} ✓✓
                            </div>
                        </div>

                        <!-- Additional info -->
                        <div class="text-xs text-gray-600 dark:text-zinc-400 mt-3 space-y-1">
                            <div>• Supports formatting: *bold*, _italic_</div>
                            <div>• Character count: {{ strlen($messageToResend->message) }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
                </flux:modal.close>
                <flux:button
                    wire:click="resendMessage"
                    variant="primary"
                    class="cursor-pointer"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Resend Message</span>
                    <span wire:loading>Please wait...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Preview Message Modal -->
    <flux:modal name="preview-message" class="min-w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Message Preview</flux:heading>
                <flux:text class="text-sm text-gray-600 dark:text-zinc-400 mt-1">How the message appears</flux:text>
            </div>

            @if($messageToPreview)
                <div class="space-y-4">
                    <!-- Message Info -->
                    <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <flux:heading size="md">Message Details</flux:heading>
                                @if($messageToPreview->template)
                                    <flux:badge color="blue" size="sm">Template</flux:badge>
                                @else
                                    <flux:badge color="gray" size="sm">Direct</flux:badge>
                                @endif
                            </div>
                            <div class="text-sm text-gray-600 dark:text-zinc-400 space-y-1">
                                <div>Sent: {{ $messageToPreview->created_at->format('M d, Y H:i') }}</div>
                                @if($messageToPreview->template)
                                    <div>Template: {{ $messageToPreview->template->name }}</div>
                                @endif
                                @if($messageToPreview->wahaSession)
                                    <div>Session: {{ $messageToPreview->wahaSession->name }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Message Preview -->
                    <div class="bg-green-50 dark:bg-green-900/10 rounded-lg p-4 border border-green-200 dark:border-green-800">
                        <flux:heading size="sm" class="mb-3 text-green-800 dark:text-green-200">Message Preview</flux:heading>

                        <!-- WhatsApp-like message bubble -->
                        <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-3 max-w-sm">
                            <div class="text-sm text-gray-900 dark:text-zinc-100">
                                @php
                                    $previewMsg = $messageToPreview->message;
                                    // Replace *text* with <strong>text</strong>
                                    $previewMsg = preg_replace('/\*(.+?)\*/s', '<strong>$1</strong>', $previewMsg);
                                    // Replace _text_ with <em>text</em>
                                    $previewMsg = preg_replace('/\_(.+?)\_/s', '<em>$1</em>', $previewMsg);
                                @endphp
                                <div class="whitespace-pre-wrap">{!! $previewMsg !!}</div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-zinc-400 mt-2 text-right">
                                {{ $messageToPreview->created_at->format('g:i A') }} ✓✓
                            </div>
                        </div>

                        <!-- Additional info -->
                        <div class="text-xs text-gray-600 dark:text-zinc-400 mt-3 space-y-1">
                            <div>• Supports formatting: *bold*, _italic_</div>
                            <div>• Character count: {{ strlen($messageToPreview->message) }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Close</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

</div>
