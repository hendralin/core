<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AiRiskPermissionSeeder extends Seeder
{
    /**
     * Safe for existing databases: creates permission if missing and assigns to superadmin.
     */
    public function run(): void
    {
        $permissions = [
            'ai-risk.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superadmin = Role::where('name', 'superadmin')->first();
        if ($superadmin) {
            foreach ($permissions as $permission) {
                if (! $superadmin->hasPermissionTo($permission)) {
                    $superadmin->givePermissionTo($permission);
                }
            }
        }
    }
}
