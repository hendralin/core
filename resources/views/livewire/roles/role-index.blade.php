@php
    $roleService = app(\App\Services\RoleService::class);
@endphp

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Roles') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage all your roles') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <!-- Statistics Overview -->
    @php
        $totalRoles = $roles->total();
        $activeRoles = $roles->filter(fn($role) => $role->users_count > 0)->count();
        $systemRoles = $roles->filter(fn($role) => \App\Constants\RoleConstants::isProtected($role->name))->count();
        $customRoles = $totalRoles - $systemRoles;
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <flux:icon.shield-check class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $totalRoles }}</div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">Total Roles</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <flux:icon.users class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $activeRoles }}</div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">Active Roles</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                    <flux:icon.cog class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $systemRoles }}</div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">System Roles</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <flux:icon.pencil-square class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $customRoles }}</div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">Custom Roles</div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-4 mb-2">
        <!-- Actions Section -->
        <div class="flex flex-wrap gap-2">
            @can('role.create')
                <flux:button variant="primary" size="sm" href="{{ route('roles.create') }}" wire:navigate icon="plus" tooltip="Create Role">Create</flux:button>
            @endcan

            @can('role.audit')
                <flux:button variant="ghost" size="sm" href="{{ route('roles.audit') }}" wire:navigate icon="document-text" tooltip="Audit Trail">Audit</flux:button>
            @endcan

            <div wire:loading>
                <flux:icon.loading class="text-red-600" />
            </div>
        </div>

        <!-- Filter Section -->
        <div class="grid grid-cols-1 md:grid-cols-4 mb-3 mt-4">
            <div class="flex items-center gap-2 w-44 mb-2 md:mb-0">
                <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">Show:</label>
                <flux:select id="per-page" wire:model.live="perPage">
                    @foreach ($this->perPageOptions as $option)
                    <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                    @endforeach
                </flux:select>
                <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">entries</label>
            </div>
            <flux:spacer class="hidden md:inline" />
            <flux:spacer class="hidden md:inline" />
            <flux:input wire:model.live.debounce.500ms="search" icon="magnifying-glass" placeholder="Search roles" clearable />
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-sm text-left rtl:text-right text-gray-500 border dark:border-zinc-700 dark:text-zinc-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-4 py-3 w-10 text-center">No.</th>
                    <th scope="col" class="px-4 py-3 w-40">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'name') {{ $sortDirection }} @endif" wire:click="sortBy('name')">
                            Name
                            @if ($sortField == 'name' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'name' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-64">Permissions</th>
                    <th scope="col" class="px-4 py-3 w-20">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'users_count') {{ $sortDirection }} @endif" wire:click="sortBy('users_count')">
                            Users
                            @if ($sortField == 'users_count' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'users_count' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-28">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'created_at') {{ $sortDirection }} @endif" wire:click="sortBy('created_at')">
                            Created
                            @if ($sortField == 'created_at' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'created_at' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-28">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'updated_at') {{ $sortDirection }} @endif" wire:click="sortBy('updated_at')">
                            Updated
                            @if ($sortField == 'updated_at' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'updated_at' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-32 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($roles) && $roles->count() > 0)
                    @foreach($roles as $index => $role)
                        <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-white">{{ $roles->firstItem() + $index }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">{{ \App\Constants\RoleConstants::getDisplayName($role->name) }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-zinc-300">
                                @php
                                    $formattedPermissions = $roleService->formatPermissionsForDisplay($role->permissions, 3);
                                @endphp
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($formattedPermissions['permissions'] as $permissionName)
                                        <flux:badge variant="outline" size="xs">{{ $permissionName }}</flux:badge>
                                    @endforeach
                                    @if ($formattedPermissions['has_more'])
                                        <flux:badge variant="secondary" size="xs">+{{ $formattedPermissions['remaining_count'] }}</flux:badge>
                                @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center text-gray-600 dark:text-zinc-300">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $role->users_count > 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-zinc-700 dark:text-zinc-300' }}">
                                    {{ $role->users_count }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">
                                {{ $role->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">
                                {{ $role->updated_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center">
                                @can('role.view')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('roles.show', $role->id) }}" wire:navigate tooltip="Show">
                                        <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                    </flux:button>
                                @endcan

                                @can('role.edit')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('roles.edit', $role->id) }}" wire:navigate tooltip="Edit">
                                        <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                    </flux:button>
                                @endcan

                                @can('role.delete')
                                    <flux:modal.trigger name="delete-role">
                                        <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setRoleToDelete({{ $role->id }})" tooltip="Delete">
                                            <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-300" />
                                        </flux:button>
                                    </flux:modal.trigger>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 border-gray-200">
                        <td class="px-4 py-2 text-gray-600 dark:text-zinc-300 text-center" colspan="7">
                            @if(isset($search) && !empty($search))
                                No results found for "{{ $search }}"
                            @else
                                No data available in table
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 mb-2">
        {{ $roles->links(data: ['scrollTo' => false]) }}
    </div>

    <flux:modal name="delete-role" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete role?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this role.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger">Delete Role</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
