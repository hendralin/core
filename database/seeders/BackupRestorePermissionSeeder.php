<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BackupRestorePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'backup-restore.download',
            'backup-restore.restore',
            'backup-restore.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to superadmin role
        $superadmin = \Spatie\Permission\Models\Role::where('name', 'superadmin')->first();
        if ($superadmin) {
            foreach ($permissions as $permission) {
                if (!$superadmin->hasPermissionTo($permission)) {
                    $superadmin->givePermissionTo($permission);
                }
            }
        }
    }
}
