<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Role') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for create new role') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('roles.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Roles">Back</flux:button>

        <div class="w-full max-w-4xl">
            <form wire:submit="submit" class="mt-6 space-y-6">
                <flux:input wire:model="name" label="Name" placeholder="Name..." />

                <div class="space-y-6">
                    <flux:heading size="md">Permissions</flux:heading>

                    @foreach ($groupedPermissions as $groupName => $permissions)
                        @if (!empty($permissions))
                            <div class="border border-gray-200 dark:border-zinc-700 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <flux:heading size="sm" class="text-gray-900 dark:text-zinc-100">{{ $groupName }}</flux:heading>
                                    <div class="flex items-center gap-2">
                                        <flux:badge variant="soft" size="sm" color="gray">{{ count($permissions) }} permissions</flux:badge>
                                        <flux:button
                                            variant="ghost"
                                            size="sm"
                                            wire:click="toggleGroupPermissions('{{ $groupName }}')"
                                            class="text-xs"
                                        >
                                            Select All
                                        </flux:button>
                                    </div>
                                </div>
                                <flux:checkbox.group wire:model="permissions" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                                    @foreach ($permissions as $permission)
                                        <flux:checkbox label="{{ $permission->name }}" value="{{ $permission->name }}" />
                                    @endforeach
                                </flux:checkbox.group>
                            </div>
                        @endif
                    @endforeach
                </div>

                <flux:button type="submit" variant="primary" class="cursor-pointer">Submit</flux:button>
            </form>
        </div>
    </div>
</div>
