<?php

namespace App\Services;

use App\Constants\RoleConstants;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleService
{
    // Permission group constants based on PermissionSeeder
    private const PERMISSION_GROUPS = [
        'System' => ['company', 'backup-restore'],
        'Users & Roles' => ['user', 'role'],
        'Master Data' => ['brand', 'type', 'category', 'vehiclemodel', 'vendor', 'salesman', 'warehouse'],
        'Vehicles' => ['vehicle', 'vehicle-modal', 'vehicle-commission', 'vehicle-loan-calculation', 'vehicle-purchase-payment', 'vehicle-payment-receipt', 'vehicle-registration-certificate-receipt', 'vehicle-handover'],
        'Cost Management' => ['cost'],
        'Cash Management' => ['cashdisbursement'],
        'Cash Inject Management' => ['cash-inject'],
        'Reports' => ['cash-report'],
    ];
    /**
     * Check if a role can be deleted
     */
    public function canDeleteRole(Role $role): array
    {
        $errors = [];

        // Check if it's a protected system role
        if (RoleConstants::isProtected($role->name)) {
            $errors[] = 'Cannot delete protected system role.';
        }

        // Check if role is still assigned to users
        if ($role->users()->exists()) {
            $userCount = $role->users()->count();
            $errors[] = "Role is still assigned to {$userCount} user(s). Please reassign users first.";
        }

        return [
            'can_delete' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get grouped permissions by resource
     */
    public function getGroupedPermissions(): array
    {
        $permissions = Permission::all();
        $grouped = array_fill_keys(array_keys(self::PERMISSION_GROUPS), []);

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $resource = $parts[0] ?? null;

            $groupFound = false;
            foreach (self::PERMISSION_GROUPS as $groupName => $resources) {
                if (in_array($resource, $resources)) {
                    $grouped[$groupName][] = $permission;
                    $groupFound = true;
                    break;
                }
            }

            // If no specific group found, put in System
            if (!$groupFound) {
                $grouped['System'][] = $permission;
            }
        }

        // Remove empty groups
        return array_filter($grouped, function ($permissions) {
            return !empty($permissions);
        });
    }

    /**
     * Get role statistics
     */
    public function getRoleStatistics(Role $role): array
    {
        if (!$role->exists) {
            throw new \InvalidArgumentException('Role does not exist');
        }

        return [
            'users_count' => $role->users()->count(),
            'permissions_count' => $role->permissions()->count(),
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ];
    }

    /**
     * Get roles with user counts for index page
     */
    public function getRolesForIndex(?string $search = null, string $sortField = 'name', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        // Validate sort field
        $validSortFields = ['name', 'users_count', 'created_at', 'updated_at'];
        $sortField = in_array($sortField, $validSortFields) ? $sortField : 'name';

        return Role::query()
            ->with('permissions')
            ->withCount('users')
            ->when(auth()->check() && !auth()->user()->hasRole(RoleConstants::SUPERADMIN), function ($q) {
                $q->whereNotIn('name', [RoleConstants::SALESMAN, RoleConstants::CUSTOMER, RoleConstants::SUPPLIER]);
            })
            ->when($search, fn($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->orderBy($sortField, $sortDirection)
            ->paginate(10);
    }

    /**
     * Format permissions for display (limit and show more)
     */
    public function formatPermissionsForDisplay(Collection $permissions, int $limit = 3): array
    {
        if ($limit < 1) {
            throw new \InvalidArgumentException('Limit must be at least 1');
        }

        $permissionNames = $permissions->pluck('name')->toArray();
        $total = count($permissionNames);

        if ($total <= $limit) {
            return [
                'permissions' => $permissionNames,
                'has_more' => false,
                'remaining_count' => 0
            ];
        }

        return [
            'permissions' => array_slice($permissionNames, 0, $limit),
            'has_more' => true,
            'remaining_count' => $total - $limit
        ];
    }

    /**
     * Get permission summary by groups for a role
     */
    public function getPermissionSummaryByGroups(Role $role): array
    {
        if (!$role->exists) {
            throw new \InvalidArgumentException('Role does not exist');
        }

        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $groupedPermissions = $this->getGroupedPermissions();

        $summary = [];
        foreach ($groupedPermissions as $groupName => $permissions) {
            $groupPermissionNames = collect($permissions)->pluck('name')->toArray();
            $roleGroupPermissions = array_intersect($rolePermissions, $groupPermissionNames);

            if (!empty($roleGroupPermissions)) {
                $summary[$groupName] = count($roleGroupPermissions);
            }
        }

        return $summary;
    }

    /**
     * Get role usage status
     */
    public function getRoleUsageStatus(Role $role): array
    {
        if (!$role->exists) {
            throw new \InvalidArgumentException('Role does not exist');
        }

        $usersCount = $role->users()->count();
        $permissionsCount = $role->permissions()->count();

        $status = 'inactive';
        $statusColor = 'gray';

        if ($usersCount > 0) {
            $status = 'active';
            $statusColor = 'green';
        } elseif ($permissionsCount > 0) {
            $status = 'configured';
            $statusColor = 'blue';
        }

        return [
            'status' => $status,
            'status_color' => $statusColor,
            'is_used' => $usersCount > 0,
            'has_permissions' => $permissionsCount > 0
        ];
    }

    /**
     * Get enhanced roles data for index page
     */
    public function getEnhancedRolesForIndex(?string $search = null, string $sortField = 'name', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        $roles = $this->getRolesForIndex($search, $sortField, $sortDirection);

        // Add additional data to each role
        $roles->getCollection()->transform(function ($role) {
            $role->permission_summary = $this->getPermissionSummaryByGroups($role);
            $role->usage_status = $this->getRoleUsageStatus($role);
            $role->is_system_role = RoleConstants::isProtected($role->name);

            return $role;
        });

        return $roles;
    }
}
