<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit User') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for edit user') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('users.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Users">Back</flux:button>

    <div class="w-full max-w-4xl mt-6" x-data="userTabs()">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 dark:border-zinc-700 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button @click="setActiveTab('profile')"
                        :class="activeTab === 'profile' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                        data-tab="profile">
                    Profile Information
                </button>
                <button @click="setActiveTab('avatar')"
                        :class="activeTab === 'avatar' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                        data-tab="avatar">
                    Avatar & Photo
                </button>
                <button @click="setActiveTab('activity')"
                        :class="activeTab === 'activity' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                        data-tab="activity">
                    Activity Log
                </button>
            </nav>
        </div>

        <!-- Profile Tab -->
        <div x-show="activeTab === 'profile'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="tab-content">
            <form wire:submit="submit" class="space-y-6">
                <flux:input wire:model="name" label="Name" placeholder="Name..." />
                <flux:input wire:model="email" label="Email" type="email" placeholder="Email..." />
                <flux:input wire:model="phone" label="Phone" placeholder="Phone..." />
                <flux:input wire:model="birth_date" label="Birth Date" type="date" />
                <flux:textarea wire:model="address" label="Address" placeholder="Address..." />
                <flux:select wire:model="timezone" label="Timezone">
                    <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                    <option value="UTC">UTC</option>
                </flux:select>
                <flux:select wire:model="status" label="Status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                    <option value="2">Pending</option>
                </flux:select>
                <div class="space-y-4">
                    <flux:heading size="sm">Change Password (leave blank to keep current)</flux:heading>
                    <flux:input wire:model="password" label="New Password" type="password" viewable placeholder="New Password..." />
                    <flux:input wire:model="confirm_password" label="Confirm New Password" type="password" viewable placeholder="Confirm New Password..." />
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <flux:checkbox.group wire:model="roles" label="Roles">
                        @foreach ($allRoles as $role)
                            <flux:checkbox label="{{ $role->name }}" value="{{ $role->name }}" />
                        @endforeach
                    </flux:checkbox.group>
                </div>
                <flux:button type="submit" variant="primary">Update User</flux:button>
            </form>
        </div>

        <!-- Avatar Tab -->
        <div x-show="activeTab === 'avatar'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="tab-content">
            @livewire('users.avatar-upload', ['user' => $user], key('avatar-upload-'.$user->id))
        </div>

        <!-- Activity Tab -->
        <div x-show="activeTab === 'activity'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="tab-content">
            @livewire('users.user-activity', ['user' => $user], key('user-activity-'.$user->id))
        </div>
    </div>
</div>

<script>
function userTabs() {
    return {
        activeTab: @entangle('activeTab').live,
        setActiveTab(tab) {
            this.activeTab = tab;
        }
    }
}
</script>
