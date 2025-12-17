<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show Template') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('This page is for show template details') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('templates.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Templates">Back</flux:button>
        @can('template.edit')
            <flux:button variant="filled" size="sm" href="{{ route('templates.edit', $template->id) }}" wire:navigate icon="pencil-square" class="ml-1">Edit</flux:button>
        @endcan

        <div class="mt-4 w-full max-w-7xl">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Template Information -->
                <div class="lg:col-span-2">
                    <!-- Template Basic Info -->
                    <div class="mb-6">
                        <flux:heading size="xl">{{ $template->name }}</flux:heading>
                        <div class="mt-2">
                            @if ($template->is_active)
                                <flux:badge color="green">Active</flux:badge>
                            @else
                                <flux:badge color="red">Inactive</flux:badge>
                            @endif
                        </div>
                    </div>

                    <flux:heading size="lg">Template Information</flux:heading>
                    <div class="grid grid-cols-2 gap-4 mt-3 mb-6">
                        <div>
                            <flux:heading size="sm">Created</flux:heading>
                            <flux:text class="mt-1">{{ $template->created_at->format('M d, Y H:i') }}</flux:text>
                            <flux:text class="text-sm text-gray-500 dark:text-zinc-500">by {{ $template->createdBy->name ?? 'Unknown' }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Updated</flux:heading>
                            <flux:text class="mt-1">{{ $template->updated_at->format('M d, Y H:i') }}</flux:text>
                            <flux:text class="text-sm text-gray-500 dark:text-zinc-500">by {{ $template->updatedBy->name ?? 'Unknown' }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Session</flux:heading>
                            <flux:text class="mt-1">
                                @if($template->wahaSession)
                                    {{ $template->wahaSession->name }}
                                    @if(!$template->wahaSession->is_active)
                                        <flux:badge color="red" size="sm" class="ml-2">Inactive</flux:badge>
                                    @endif
                                @else
                                    <span class="text-gray-400 dark:text-zinc-400">No session assigned</span>
                                @endif
                            </flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Usage Count</flux:heading>
                            <flux:text class="mt-1">{{ $template->usage_count }}</flux:text>
                        </div>
                        <div class="col-span-2">
                            <flux:heading size="sm">Last Used</flux:heading>
                            <flux:text class="mt-1">
                                @if($template->last_used_at)
                                    {{ $template->last_used_at->diffForHumans() }}
                                @else
                                    <span class="text-gray-400 dark:text-zinc-400">Never</span>
                                @endif
                            </flux:text>
                        </div>
                    </div>

                    @if($template->header)
                    <flux:heading size="lg" class="mt-6">Header Text</flux:heading>
                    <div class="mt-3 p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg border">
                        <flux:text>{{ $template->header }}</flux:text>
                    </div>
                    @endif

                    <flux:heading size="lg" class="mt-6">Body Text</flux:heading>
                    <div class="mt-3 p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg border">
                        <flux:text class="whitespace-pre-wrap">{{ $template->body }}</flux:text>
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
                                        @if($template->header)
                                            <div class="font-semibold mb-2">{{ $template->header }}</div>
                                        @endif
                                        <div class="whitespace-pre-wrap">{{ $template->body }}</div>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-2 text-right">
                                        12:34 PM ✓✓
                                    </div>
                                </div>

                                <!-- Additional info -->
                                <div class="text-xs text-gray-600 dark:text-zinc-400 space-y-1">
                                    <div>• Template variables: @{{1}}, @{{2}}, etc.</div>
                                    <div>• Supports formatting: *bold*, _italic_</div>
                                    <div>• Character count: {{ strlen($template->body) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
