<?php

namespace App\Livewire\Roles;

use App\Models\Role;
use Livewire\Component;
use App\Services\RoleService;
use Livewire\Attributes\Title;
use App\Constants\RoleConstants;
use Illuminate\Support\Facades\Auth;

#[Title('Create Role')]
class RoleCreate extends Component
{
    protected RoleService $roleService;

    public $name;

    public $permissions = [];

    public $groupedPermissions = [];

    public function toggleGroupPermissions($groupName)
    {
        if (!isset($this->groupedPermissions[$groupName])) {
            return;
        }

        $groupPermissionNames = collect($this->groupedPermissions[$groupName])->pluck('name')->toArray();
        $selectedInGroup = array_intersect($this->permissions, $groupPermissionNames);

        if (count($selectedInGroup) === count($groupPermissionNames)) {
            // All permissions in group are selected, deselect them
            $this->permissions = array_diff($this->permissions, $groupPermissionNames);
        } else {
            // Not all permissions selected, select all
            $this->permissions = array_unique(array_merge($this->permissions, $groupPermissionNames));
        }
    }

    public function boot(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function mount()
    {
        $this->groupedPermissions = $this->roleService->getGroupedPermissions();
    }

    public function submit()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z][a-zA-Z0-9_-]*$/',
                'unique:roles,name',
                'not_in:' . implode(',', RoleConstants::PROTECTED_ROLES)
            ],
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|exists:permissions,name'
        ], [
            'name.required' => 'Role name is required.',
            'name.min' => 'Role name must be at least 2 characters.',
            'name.max' => 'Role name cannot exceed 50 characters.',
            'name.regex' => 'Role name must start with a letter and contain only letters, numbers, underscores, and hyphens.',
            'name.unique' => 'This role name already exists.',
            'name.not_in' => 'This is a protected system role name.',
            'permissions.required' => 'At least one permission must be selected.',
            'permissions.min' => 'At least one permission must be selected.',
            'permissions.*.exists' => 'Selected permission does not exist.'
        ]);

        try {
            $role = Role::create([
                'name' => strtolower($this->name),
            ]);

            $role->syncPermissions($this->permissions);

            // Log the creation activity with detailed information
            activity()
                ->performedOn($role)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'name' => strtolower($this->name),
                        'permissions' => $this->permissions,
                    ]
                ])
                ->log('created role');

            session()->flash('success', 'Role created successfully.');

            return $this->redirect('/roles', true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create role. Please try again.');
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.roles.role-create');
    }
}
