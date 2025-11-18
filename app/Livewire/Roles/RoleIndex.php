<?php

namespace App\Livewire\Roles;

use App\Services\RoleService;
use App\Constants\RoleConstants;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use App\Models\Role;

#[Title('Roles')]
class RoleIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected RoleService $roleService;

    public $roleIdToDelete = null;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $sortableFields = ['name', 'users_count', 'created_at', 'updated_at'];

    public function boot(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function sortBy($field)
    {
        if (!in_array($field, $this->sortableFields)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function setRoleToDelete($roleId)
    {
        $this->roleIdToDelete = $roleId;
    }

    public function delete()
    {
        try {
            if (!$this->roleIdToDelete) {
                session()->flash('error', 'No role selected for deletion.');
                return;
            }

            $role = Role::findOrFail($this->roleIdToDelete);

            // Check if role can be deleted using RoleService
            $canDelete = $this->roleService->canDeleteRole($role);

            if (!$canDelete['can_delete']) {
                session()->flash('error', implode(' ', $canDelete['errors']));
                return;
            }

            DB::transaction(function () use ($role) {
                // Store role data for logging before deletion
                $roleData = [
                    'name' => $role->name,
                    'permissions' => $role->permissions()->pluck('name')->toArray(),
                ];

                $role->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($role)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $roleData
                    ])
                    ->log('deleted role');
            });

            $this->reset(['roleIdToDelete']);

            session()->flash('success', 'Role deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Role not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $roles = $this->roleService->getRolesForIndex(
            $this->search,
            $this->sortField,
            $this->sortDirection
        );

        return view('livewire.roles.role-index', compact('roles'));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
