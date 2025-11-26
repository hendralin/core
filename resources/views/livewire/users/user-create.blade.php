<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create User') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for create new user') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('users.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Users">Back</flux:button>

        <div class="w-full max-w-lg">
            <form wire:submit="submit" class="mt-6 space-y-6">
                <flux:input wire:model="name" label="Name" placeholder="Name..." />
                <flux:input wire:model="email" label="Email" type="email" placeholder="Email..." />
                <flux:input wire:model="phone" label="Phone" placeholder="081234567890..." />
                <flux:input wire:model="birth_date" label="Birth Date" type="date" />
                <flux:textarea wire:model="address" label="Address" placeholder="Address..." />
                <flux:select wire:model="timezone" label="Timezone">
                    <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                    <option value="UTC">UTC</option>
                </flux:select>
                <flux:input wire:model="password" label="Password" type="password" viewable placeholder="Password..." />
                <flux:input wire:model="confirm_password" label="Confirm Password" type="password" viewable placeholder="Confirm Password..." />
                <div class="grid grid-cols-2 gap-6">
                    <flux:checkbox.group wire:model="roles" label="Roles">
                        @foreach ($allRoles as $role)
                            <flux:checkbox label="{{ $role->name }}" value="{{ $role->name }}" />
                        @endforeach
                    </flux:checkbox.group>
                    <flux:checkbox.group wire:model="warehouses" label="Warehouses">
                        @foreach ($allWarehouses as $warehouse)
                            <flux:checkbox label="{{ $warehouse->name }}" value="{{ $warehouse->id }}" />
                        @endforeach
                    </flux:checkbox.group>
                </div>
                <flux:button type="submit" variant="primary" class="cursor-pointer">Create User</flux:button>
            </form>
        </div>
    </div>
</div>
