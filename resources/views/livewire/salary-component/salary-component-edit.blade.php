<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Salary Component') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for edit salary component') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('salary-components.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Salary Components">Back</flux:button>

        <div class="w-full max-w-lg">
            <form wire:submit="submit" class="mt-6 space-y-6">
                <flux:input wire:model="name" label="Name" placeholder="Salary component name..." />
                <flux:textarea wire:model="description" label="Description" placeholder="Salary component description..." />
                <flux:button type="submit" variant="primary" class="cursor-pointer">Submit</flux:button>
            </form>
        </div>
    </div>
</div>
