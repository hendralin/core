<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show Salesman') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Salesman details') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('salesmen.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Salesman">Back</flux:button>
        @can('salesman.edit')
            <flux:button variant="filled" size="sm" href="{{ route('salesmen.edit', $salesman->id) }}" wire:navigate icon="pencil-square" class="ml-1">Edit</flux:button>
        @endcan

        <div class="w-full max-w-2xl mt-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-6">Salesman Information</flux:heading>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:heading size="md">Name</flux:heading>
                            <flux:text class="mt-1">{{ $salesman->name }}</flux:text>
                        </div>

                        <div>
                            <flux:heading size="md">User</flux:heading>
                            <flux:text class="mt-1">{{ $salesman->user->name }} ({{ $salesman->user->email }})</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($salesman->phone)
                        <div>
                            <flux:heading size="md">Phone</flux:heading>
                            <flux:text class="mt-1">{{ $salesman->phone }}</flux:text>
                        </div>
                        @endif

                        @if($salesman->email)
                        <div>
                            <flux:heading size="md">Email</flux:heading>
                            <flux:text class="mt-1">{{ $salesman->email }}</flux:text>
                        </div>
                        @endif
                    </div>

                    @if($salesman->address)
                    <div>
                        <flux:heading size="md">Address</flux:heading>
                        <flux:text class="mt-1">{!! nl2br(e($salesman->address)) !!}</flux:text>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <div>
                            <flux:heading size="sm">Created</flux:heading>
                            <flux:text class="text-sm">{{ $salesman->created_at->format('M d, Y \a\t H:i') }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Last Updated</flux:heading>
                            <flux:text class="text-sm">{{ $salesman->updated_at->format('M d, Y \a\t H:i') }}</flux:text>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
