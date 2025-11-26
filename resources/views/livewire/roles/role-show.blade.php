@php
    $roleService = app(\App\Services\RoleService::class);
@endphp

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show Role') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Role details and permissions overview') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('roles.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Roles">Back</flux:button>
        @can('role.edit')
            <flux:button variant="filled" size="sm" href="{{ route('roles.edit', $role->id) }}" wire:navigate icon="pencil-square" class="ml-1">Edit</flux:button>
        @endcan

        <div class="mt-6 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Basic Information</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:heading size="sm">Role Name</flux:heading>
                        <flux:text class="mt-1">{{ \App\Constants\RoleConstants::getDisplayName($role->name) }}</flux:text>
                    </div>
                    <div>
                        <flux:heading size="sm">System Role</flux:heading>
                        <flux:text class="mt-1">
                            @if (\App\Constants\RoleConstants::isProtected($role->name))
                                <flux:badge variant="destructive" size="sm">Protected</flux:badge>
                            @else
                                <flux:badge variant="secondary" size="sm">Custom</flux:badge>
                            @endif
                        </flux:text>
                    </div>
                    <div>
                        <flux:heading size="sm">Created At</flux:heading>
                        <flux:text class="mt-1">{{ $role->created_at->format('d F Y, H:i') }}</flux:text>
                    </div>
                    <div>
                        <flux:heading size="sm">Last Updated</flux:heading>
                        <flux:text class="mt-1">{{ $role->updated_at->format('d F Y, H:i') }}</flux:text>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Statistics</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full mb-2">
                            <flux:icon.users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $statistics['users_count'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-zinc-400">Active Users</div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full mb-2">
                            <flux:icon.shield-check class="w-6 h-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $statistics['permissions_count'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-zinc-400">Permissions</div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full mb-2">
                            <flux:icon.calendar class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $role->created_at->diffForHumans() }}</div>
                        <div class="text-sm text-gray-600 dark:text-zinc-400">Created</div>
                    </div>
                </div>
            </div>

            <!-- Permissions -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Permissions ({{ $statistics['permissions_count'] }})</flux:heading>

                @if ($role->permissions->count() > 0)
                    @php
                        $groupedPermissions = $roleService->getGroupedPermissions();
                        $rolePermissions = $role->permissions->pluck('name')->toArray();
                    @endphp

                    <div class="space-y-4">
                        @foreach ($groupedPermissions as $groupName => $permissions)
                            @php
                                $groupPermissions = collect($permissions)->filter(function($perm) use ($rolePermissions) {
                                    return in_array($perm->name, $rolePermissions);
                                });
                            @endphp

                            @if ($groupPermissions->count() > 0)
                                <div>
                                    <flux:heading size="sm" class="mb-2 text-gray-900 dark:text-zinc-100">{{ $groupName }}</flux:heading>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($groupPermissions as $permission)
                                            <flux:badge variant="outline">{{ $permission->name }}</flux:badge>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <flux:text>No permissions assigned to this role.</flux:text>
                @endif
            </div>

            <!-- Users with this role (if any) -->
            @if ($statistics['users_count'] > 0)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Users with this Role</flux:heading>
                    <flux:text>
                        This role is currently assigned to {{ $statistics['users_count'] }} user(s).
                        @if (\App\Constants\RoleConstants::isProtected($role->name))
                            As a protected system role, it cannot be deleted while users are assigned to it.
                        @endif
                    </flux:text>
                </div>
            @endif
        </div>
    </div>
</div>
