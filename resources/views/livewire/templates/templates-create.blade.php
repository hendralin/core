<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Template') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for create new template') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('templates.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Templates">Back</flux:button>

        <div class="mt-4 w-full max-w-7xl">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Create Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                        <flux:heading size="lg" class="mb-6">Create Template</flux:heading>

                        <form wire:submit="submit" class="space-y-6">
                            <flux:select wire:model="waha_session_id" label="Session" description="Select the session for this template">
                                <flux:select.option value="" label="Select Session" />
                                @foreach($sessions as $session)
                                    <flux:select.option value="{{ $session->id }}">{{ $session->name }}</flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:input wire:model="name" label="Template Name" description="Use only lowercase letters and underscores. No spaces, numbers or special characters allowed." placeholder="e.g., welcome_message" />

                            <flux:input wire:model.live="header" label="Header Text" description="Use @{{1}} for variables. Max 60 characters." placeholder="e.g., Welcome to @{{1}}!..." />

                            <flux:textarea wire:model.live="body" label="Body Text" description="Use @{{1}}, @{{2}}, etc. for variables. Use * for bold, _ for italic. Max 1024 characters." placeholder="e.g., Hello @{{1}}, thank you for choosing us..." rows="8" />

                            <flux:checkbox wire:model="is_active" label="Active" description="Enable this template for use" />

                            <div class="flex gap-3 pt-4">
                                <flux:button type="submit" variant="primary" class="cursor-pointer">Create Template</flux:button>
                                <flux:button variant="ghost" href="{{ route('templates.index') }}" wire:navigate>Cancel</flux:button>
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
                                <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-3 max-w-xs ml-auto">
                                    <div class="text-sm text-gray-900 dark:text-zinc-100">
                                        @if($header)
                                            <div class="font-semibold mb-2">{{ $header }}</div>
                                        @endif
                                        <div class="whitespace-pre-wrap">{{ $body }}</div>
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
