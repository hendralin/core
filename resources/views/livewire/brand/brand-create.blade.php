<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Brand') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for create new brand') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('brands.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Brands">Back</flux:button>

        <div class="w-full max-w-lg">
            <form wire:submit="submit" class="mt-6 space-y-6">
                <flux:input wire:model="name" label="Name" placeholder="Name..." />
                <flux:textarea wire:model="description" label="Description" placeholder="Description..." />
                <flux:button type="submit" variant="primary" class="cursor-pointer">Submit</flux:button>
            </form>
        </div>
    </div>
</div>
