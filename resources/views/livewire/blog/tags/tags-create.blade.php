<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Blog Tag') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Add a new tag for organizing your blog posts') }}</flux:subheading>
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
                <span wire:loading.remove>{{ __('Create Tag') }}</span>
                <span wire:loading>{{ __('Creating...') }}</span>
            </flux:button>
        </div>
    </form>
</div>
