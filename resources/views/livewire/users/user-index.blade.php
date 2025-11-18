<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Users') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage all your users') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        @session('success')
            <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
        @endsession

        @session('error')
            <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
        @endsession


        <x-data-table :data="$users" :roles="$roles" :showAdvancedFilters="$showAdvancedFilters" searchable filterable selectable title="Users" placeholder="Search users...">
            <x-slot name="actions">
                <div class="flex flex-wrap gap-2">
                    @can('user.create')
                        <flux:button variant="primary" size="sm" href="{{ route('users.create') }}" wire:navigate icon="plus">Create User</flux:button>
                    @endcan

                    <flux:button variant="outline" size="sm" href="{{ route('users.audit') }}" wire:navigate icon="document-text">Audit Trail</flux:button>

                    @if (count($selected) > 0)
                        <!-- Bulk Status Change -->
                        @can('user.edit')
                            <flux:button size="sm" wire:click="bulkStatusChange(1)" variant="primary" color="green" icon="check-circle">
                                Activate {{ count($selected) }} Selected
                            </flux:button>
                            <flux:button size="sm" wire:click="bulkStatusChange(0)" variant="danger" icon="x-circle">
                                Deactivate {{ count($selected) }} Selected
                            </flux:button>
                        @endcan

                        <!-- Bulk Export -->
                        <flux:button size="sm" wire:click="exportSelected" variant="outline" icon="arrow-down-tray">
                            Export {{ count($selected) }} Selected
                        </flux:button>

                        <!-- Bulk Delete -->
                        @can('user.delete')
                            <flux:button size="sm" wire:click="deleteSelected('')" wire:confirm="Are you sure to remove this {{ count($selected) }} selected user?" variant="danger" icon="trash">
                                Delete {{ count($selected) }} Selected
                            </flux:button>
                        @endcan
                    @endif
                    <div wire:loading>
                        <flux:icon.loading class="text-red-600" />
                    </div>
                </div>
            </x-slot>

            <x-slot name="columns">
                <x-table-column sortable="true" field="name">Name</x-table-column>
                <x-table-column sortable="true" field="email">Email</x-table-column>
                <x-table-column field="role">Role</x-table-column>
                <x-table-column field="status">Status</x-table-column>
                <x-table-column field="email_verified" class="whitespace-nowrap">Email Verified</x-table-column>
                <x-table-column field="created_at">Joined</x-table-column>
                <x-table-column field="last_login_at" class="whitespace-nowrap">Last Login</x-table-column>
                <x-table-column class="w-1/12">Actions</x-table-column>
            </x-slot>

            <x-slot name="rows">
                @if(isset($users) && $users->count() > 0)
                    @foreach($users as $user)
                        <x-table-row wire:loading.class="opacity-50">
                            <th class="px-6 py-3">
                                <input wire:model.live="selected" wire:key="{{ $user->id }}" value="{{ $user->id }}" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </th>
                            <x-table-cell>
                                <div class="flex items-center">
                                    <img src="{{ $user->avatar_url }}" class="h-10 w-10 rounded-full" alt="{{ $user->name }}">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </x-table-cell>
                            <x-table-cell>{{ $user->email }}</x-table-cell>
                            <x-table-cell>
                                @if ($user->roles)
                                    @foreach ($user->roles as $role)
                                        <flux:badge class="mt-1">{{ $role->name }}</flux:badge>
                                    @endforeach
                                @endif
                            </x-table-cell>
                            <x-table-cell>
                                @if ($user->status == 0)
                                    <flux:badge color="red">Inactive</flux:badge>
                                @elseif ($user->status == 1)
                                    <flux:badge color="green">Active</flux:badge>
                                @else
                                    <flux:badge color="yellow">Pending</flux:badge>
                                @endif
                            </x-table-cell>
                            <x-table-cell>
                                @if($user->is_email_verified)
                                    <flux:badge color="green">Verified</flux:badge>
                                @else
                                    <flux:badge color="yellow">Unverified</flux:badge>
                                @endif
                            </x-table-cell>
                            <x-table-cell>{{ $user->created_at->format('M d, Y') }}</x-table-cell>
                            <x-table-cell>
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->diffForHumans() }}
                                @else
                                    <span class="text-gray-400">Never</span>
                                @endif
                            </x-table-cell>
                            <x-table-cell>
                                <x-table-actions :user="$user" />
                            </x-table-cell>
                        </x-table-row>
                    @endforeach
                @else
                    <x-table-row>
                        <x-table-cell colspan="9" class="text-center">
                            @if(isset($search) && !empty($search))
                                No results found for "{{ $search }}"
                            @else
                                No data available in table
                            @endif
                        </x-table-cell>
                    </x-table-row>
                @endif
            </x-slot>
        </x-data-table>
    </div>

    <!-- Download Script -->
    <script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('download-file', (data) => {
            const link = document.createElement('a');
            link.href = data.url;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
    </script>
</div>
