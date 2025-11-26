<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Salesman') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for edit salesman') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('salesmen.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Salesman">Back</flux:button>

        <div class="w-full max-w-lg">
            <form wire:submit="submit" class="mt-6 space-y-6">
                <flux:input wire:model="name" label="Name" placeholder="Name..." />
                <div class="grid grid-cols-2 gap-6">
                    <flux:input wire:model="phone" label="Phone" placeholder="Phone number..." />
                    <flux:input wire:model="email" label="Email" placeholder="Email address..." />
                </div>
                <flux:select wire:model="status" label="Status">
                    <flux:select.option value="1">Active</flux:select.option>
                    <flux:select.option value="0">Inactive</flux:select.option>
                </flux:select>
                <flux:textarea wire:model="address" label="Address" placeholder="Address..." />
                <flux:button type="submit" variant="primary" class="cursor-pointer">Submit</flux:button>
            </form>
        </div>
    </div>
</div>
