<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show Vendor') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Vendor details') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('vendors.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Vendor">Back</flux:button>
        @can('vendor.edit')
            <flux:button variant="filled" size="sm" href="{{ route('vendors.edit', $vendor->id) }}" wire:navigate icon="pencil-square" class="ml-1">Edit</flux:button>
        @endcan

        <div class="w-full max-w-2xl mt-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-6">Vendor Information</flux:heading>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:heading size="md">Name</flux:heading>
                            <flux:text class="mt-1">{{ $vendor->name }}</flux:text>
                        </div>

                        @if($vendor->contact)
                        <div>
                            <flux:heading size="md">Contact</flux:heading>
                            <flux:text class="mt-1">{{ $vendor->contact }}</flux:text>
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($vendor->phone)
                        <div>
                            <flux:heading size="md">Phone</flux:heading>
                            <flux:text class="mt-1">{{ $vendor->phone }}</flux:text>
                        </div>
                        @endif

                        @if($vendor->email)
                        <div>
                            <flux:heading size="md">Email</flux:heading>
                            <flux:text class="mt-1">{{ $vendor->email }}</flux:text>
                        </div>
                        @endif
                    </div>

                    @if($vendor->address)
                    <div>
                        <flux:heading size="md">Address</flux:heading>
                        <flux:text class="mt-1">{!! nl2br(e($vendor->address)) !!}</flux:text>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <div>
                            <flux:heading size="sm">Created</flux:heading>
                            <flux:text class="text-sm">{{ $vendor->created_at->format('M d, Y \a\t H:i') }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Last Updated</flux:heading>
                            <flux:text class="text-sm">{{ $vendor->updated_at->format('M d, Y \a\t H:i') }}</flux:text>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
