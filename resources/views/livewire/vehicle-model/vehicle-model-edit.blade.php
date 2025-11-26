<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Model') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for edit model') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('models.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Model">Back</flux:button>

        <div class="w-full max-w-lg">
            <form wire:submit="submit" class="mt-6 space-y-6">
                <flux:input wire:model="name" label="Name" placeholder="Name..." />
                <flux:textarea wire:model="description" label="Description" placeholder="Description..." />
                <flux:button type="submit" variant="primary" class="cursor-pointer">Submit</flux:button>
            </form>
        </div>
    </div>
</div>
