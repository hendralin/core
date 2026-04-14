<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::firstOrCreate(['name' => 'analytics.view']);

        foreach (['superadmin', 'owner', 'admin', 'finance'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role && ! $role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }
    }

    public function down(): void
    {
        $permission = Permission::where('name', 'analytics.view')->first();
        if (! $permission) {
            return;
        }

        foreach (['superadmin', 'owner', 'admin', 'finance'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->revokePermissionTo($permission);
            }
        }

        $permission->delete();
    }
};
