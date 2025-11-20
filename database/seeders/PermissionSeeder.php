<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'company.view',
            'company.edit',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'backup-restore.view',
            'backup-restore.create',
            'backup-restore.download',
            'backup-restore.restore',
            'backup-restore.delete',
            'session.view',
            'session.create',
            'session.edit',
            'session.delete',
            'session.connect',
            'session.disconnect',
            'waha.view',
            'waha.edit',
        ];

        foreach ($permissions as $key => $value) {
            Permission::firstOrCreate(['name' => $value]);
        }

        $permissions = Permission::all();

        $superadmin = Role::where('name', 'superadmin')->first();

        $superadmin->syncPermissions($permissions);
    }
}
