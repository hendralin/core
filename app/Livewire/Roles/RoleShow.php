<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use App\Services\RoleService;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Role;

#[Title('Show Role')]
class RoleShow extends Component
{
    protected RoleService $roleService;

    public Role $role;

    public $statistics = [];

    public function boot(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function mount(Role $role): void
    {
        $this->role = $role;
        $this->statistics = $this->roleService->getRoleStatistics($role);
    }

    public function render()
    {
        return view('livewire.roles.role-show');
    }
}
