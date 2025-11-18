<?php

namespace App\Services;

use App\Constants\RoleConstants;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
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

        $grouped = [
            'System' => [],
            'Users & Roles' => [],
            'Master Data' => [],
            'Inventory' => [],
            'Cost Management' => [],
            'Transactions' => [],
            'Reports' => [],
            'Sales & Cashier' => [],
        ];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);

            if (count($parts) >= 2) {
                $resource = $parts[0];

                switch ($resource) {
                    case 'company':
                    case 'backup-restore':
                        $grouped['System'][] = $permission;
                        break;
                    case 'user':
                    case 'role':
                        $grouped['Users & Roles'][] = $permission;
                        break;
                    case 'category':
                    case 'merk':
                    case 'brand':
                    case 'type':
                    case 'vehiclemodel':
                    case 'warehouse':
                    case 'customer':
                    case 'supplier':
                    case 'salesman':
                    case 'vendor':
                    case 'vehicle':
                        $grouped['Master Data'][] = $permission;
                        break;
                    case 'item':
                    case 'serial-number':
                    case 'adjustment':
                        $grouped['Inventory'][] = $permission;
                        break;
                    case 'cost':
                        $grouped['Cost Management'][] = $permission;
                        break;
                    case 'purchase-order':
                    case 'purchase-invoice':
                    case 'sales-order':
                    case 'received-item':
                    case 'item-transfer':
                    case 'vendor-payment':
                        $grouped['Transactions'][] = $permission;
                        break;
                    case 'report':
                        $grouped['Reports'][] = $permission;
                        break;
                    case 'cashier':
                    case 'sale':
                        $grouped['Sales & Cashier'][] = $permission;
                        break;
                    default:
                        $grouped['System'][] = $permission;
                }
            } else {
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
    public function getRolesForIndex($search = null, $sortField = 'name', $sortDirection = 'asc')
    {
        // Validate sort field
        $validSortFields = ['name', 'users_count', 'created_at', 'updated_at'];
        $sortField = in_array($sortField, $validSortFields) ? $sortField : 'name';

        return Role::query()
            ->with('permissions')
            ->withCount('users')
            ->when(!auth()->user()->hasRole('superadmin'), function ($q) {
                $q->whereNotIn('name', ['salesman', 'customer', 'supplier']);
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
    public function getEnhancedRolesForIndex($search = null, $sortField = 'name', $sortDirection = 'asc')
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
