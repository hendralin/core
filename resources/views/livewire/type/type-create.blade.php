<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Type') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for create new type') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('types.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="kembali ke Tipe">Back</flux:button>

        <div class="w-full max-w-lg">
            <form wire:submit="submit" class="mt-6 space-y-6">
                <flux:select wire:model="brand_id" label="Brand">
                    <option value="">Select Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </flux:select>
                <flux:input wire:model="name" label="Name" placeholder="Name..." />
                <flux:textarea wire:model="description" label="Description" placeholder="Description..." />
                <flux:button type="submit" variant="primary" class="cursor-pointer">Submit</flux:button>
            </form>
        </div>
    </div>
</div>
