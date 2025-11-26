<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Vendor') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for edit vendor') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('vendors.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Vendor">Back</flux:button>

        <div class="w-full max-w-lg">
            <form wire:submit="submit" class="mt-6 space-y-6">
                <flux:input wire:model="name" label="Name" placeholder="Name..." />
                <flux:input wire:model="contact" label="Contact" placeholder="Contact person..." />
                <flux:input wire:model="phone" label="Phone" placeholder="Phone number..." />
                <flux:input wire:model="email" label="Email" placeholder="Email address..." />
                <flux:textarea wire:model="address" label="Address" placeholder="Address..." />
                <flux:button type="submit" variant="primary" class="cursor-pointer">Submit</flux:button>
            </form>
        </div>
    </div>
</div>
