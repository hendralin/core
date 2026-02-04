<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Blog Tag') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Update tag information') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <form wire:submit="submit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tag Name -->
            <div class="md:col-span-2">
                <flux:field>
                    <flux:label for="name">{{ __('Tag Name') }} <span class="text-red-500">*</span></flux:label>
                    <flux:input
                        id="name"
                        wire:model="name"
                        type="text"
                        placeholder="Enter tag name"
                        required
                    />
                    <flux:error name="name" />
                    <flux:description>A short, descriptive name for your tag (e.g., "Technology", "News", "Tutorial").</flux:description>
                </flux:field>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-zinc-700">
            <flux:button variant="ghost" href="{{ route('blog.tags.index') }}" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Update Tag') }}</span>
                <span wire:loading>{{ __('Updating...') }}</span>
            </flux:button>
        </div>
    </form>

    <!-- Tag Statistics -->
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-zinc-700">
        <flux:heading size="md" class="mb-4">{{ __('Tag Statistics') }}</flux:heading>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                <div class="flex items-center">
                    <flux:icon.document-text class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" />
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $tag->posts_count }}</div>
                        <div class="text-sm text-gray-600 dark:text-zinc-400">Total Posts</div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                <div class="flex items-center">
                    <flux:icon.calendar class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" />
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $tag->created_at->format('M j, Y') }}</div>
                        <div class="text-sm text-gray-600 dark:text-zinc-400">Created</div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                <div class="flex items-center">
                    <flux:icon.clock class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-3" />
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $tag->updated_at->format('M j, Y') }}</div>
                        <div class="text-sm text-gray-600 dark:text-zinc-400">Last Updated</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
