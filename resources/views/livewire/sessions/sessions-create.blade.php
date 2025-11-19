<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Session') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for creating new WhatsApp HTTP API session') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('sessions.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Sessions">Back</flux:button>

        <div class="w-full max-w-2xl">
            <form wire:submit="save" class="mt-6 space-y-6">
                <flux:input wire:model="name" label="Session Name" placeholder="My WhatsApp Session..." />
                <flux:input wire:model="session_id" label="Session ID" placeholder="session-001..." />
                <flux:checkbox wire:model="is_active" label="Active" />
                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" icon="plus">Create Session</flux:button>
                    <flux:button variant="ghost" href="{{ route('sessions.index') }}" wire:navigate>Cancel</flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
