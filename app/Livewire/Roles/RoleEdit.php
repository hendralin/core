<?php

namespace App\Livewire\Roles;

use App\Services\RoleService;
use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Role')]
class RoleEdit extends Component
{
    protected RoleService $roleService;

    public Role $role;

    public string $name;

    public $permissions = [];

    public $groupedPermissions = [];

    public function boot(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function mount(Role $role): void
    {
        $this->role = $role;

        $this->groupedPermissions = $this->roleService->getGroupedPermissions();

        $this->name = $role->name;

        $this->permissions = $role->permissions()->pluck('name')->toArray();
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
                'unique:roles,name,' . $this->role->id,
                function ($attribute, $value, $fail) {
                    // Allow editing protected roles but prevent changing their names to other protected names
                    if (\App\Constants\RoleConstants::isProtected($this->role->name) &&
                        !\App\Constants\RoleConstants::isProtected($value)) {
                        $fail('Cannot change protected system role name.');
                    }
                    if (!\App\Constants\RoleConstants::isProtected($this->role->name) &&
                        \App\Constants\RoleConstants::isProtected($value)) {
                        $fail('Cannot change to protected system role name.');
                    }
                }
            ],
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|exists:permissions,name'
        ], [
            'name.required' => 'Role name is required.',
            'name.min' => 'Role name must be at least 2 characters.',
            'name.max' => 'Role name cannot exceed 50 characters.',
            'name.regex' => 'Role name must start with a letter and contain only letters, numbers, underscores, and hyphens.',
            'name.unique' => 'This role name already exists.',
            'permissions.required' => 'At least one permission must be selected.',
            'permissions.min' => 'At least one permission must be selected.',
            'permissions.*.exists' => 'Selected permission does not exist.'
        ]);

        try {
            // Store old values for logging
            $oldValues = [
                'name' => $this->role->name,
                'permissions' => $this->role->permissions()->pluck('name')->toArray(),
            ];

            $this->role->update([
                'name' => strtolower($this->name),
            ]);

            $this->role->syncPermissions($this->permissions);

            // Log the update activity with detailed information
            activity()
                ->performedOn($this->role)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => $oldValues,
                    'attributes' => [
                        'name' => strtolower($this->name),
                        'permissions' => $this->permissions,
                    ]
                ])
                ->log('updated role');

            session()->flash('success', 'Role updated successfully.');

            return $this->redirect('/roles', true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update role. Please try again.');
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.roles.role-edit');
    }
}
