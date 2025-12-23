<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Template') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for edit template') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('templates.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Templates">Back</flux:button>

        <div class="mt-4 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Edit Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                        <flux:heading size="lg" class="mb-6">Edit Template</flux:heading>

                        <form wire:submit="submit" class="space-y-6">
                            <flux:select wire:model="waha_session_id" label="Session" description="Select the session for this template">
                                <flux:select.option value="" label="Select Session" />
                                @foreach($sessions as $session)
                                    <flux:select.option value="{{ $session->id }}">{{ $session->name }}</flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:input wire:model="name" label="Template Name" placeholder="Enter template name..." required />

                            <flux:input wire:model.live="header" label="Header" placeholder="Enter template header (optional)..." />

                            <flux:textarea wire:model.live="body" label="Template Body" placeholder="Enter template content..." rows="8" required />

                            <flux:checkbox wire:model="is_active" label="Active" description="Enable this template for use" />

                            <div class="flex gap-3 pt-4">
                                <flux:button type="submit" variant="primary" class="cursor-pointer">Update Template</flux:button>
                                <flux:button variant="ghost" href="{{ route('templates.show', $template->id) }}" wire:navigate>Cancel</flux:button>
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
                                    <div class="text-sm text-gray-900 dark:text-zinc-100">
                                        @if($header)
                                            <div class="font-semibold mb-2">{{ $header }}</div>
                                        @endif
                                        @php
                                            $previewMessage = $body ?? '';
                                            // Replace *text* with <strong>text</strong>
                                            $previewMessage = preg_replace('/\*(.+?)\*/s', '<strong>$1</strong>', $previewMessage);
                                            // Replace _text_ with <em>text</em>
                                            $previewMessage = preg_replace('/\_(.+?)\_/s', '<em>$1</em>', $previewMessage);
                                        @endphp
                                        <div class="whitespace-pre-wrap">{!! $previewMessage !!}</div>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-2 text-right">
                                        12:34 PM ✓✓
                                    </div>
                                </div>

                                <!-- Additional info -->
                                <div class="text-xs text-gray-600 dark:text-zinc-400 space-y-1">
                                    <div>• Template variables: @{{1}}, @{{2}}, etc.</div>
                                    <div>• Supports formatting: *bold*, _italic_</div>
                                    <div>• Character count: {{ strlen($body ?? '') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
