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
            'user.audit',
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'role.audit',
            'backup-restore.view',
            'backup-restore.create',
            'backup-restore.download',
            'backup-restore.restore',
            'backup-restore.delete',
            'brand.view',
            'brand.create',
            'brand.edit',
            'brand.delete',
            'brand.audit',
            'type.view',
            'type.create',
            'type.edit',
            'type.delete',
            'type.audit',
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',
            'category.audit',
            'vehiclemodel.view',
            'vehiclemodel.create',
            'vehiclemodel.edit',
            'vehiclemodel.delete',
            'vehiclemodel.audit',
            'vendor.view',
            'vendor.create',
            'vendor.edit',
            'vendor.delete',
            'vendor.audit',
            'salesman.view',
            'salesman.create',
            'salesman.edit',
            'salesman.delete',
            'salesman.audit',
            'warehouse.view',
            'warehouse.create',
            'warehouse.edit',
            'warehouse.delete',
            'warehouse.audit',
            'vehicle.view',
            'vehicle.create',
            'vehicle.edit',
            'vehicle.delete',
            'vehicle.audit',
            'vehicle-modal.view',
            'vehicle-receipt.print',
            'cost.view',
            'cost.create',
            'cost.edit',
            'cost.delete',
            'cost.audit',
            'cost.approve',
            'cost.reject',
        ];

        foreach ($permissions as $key => $value) {
            Permission::create(['name' => $value]);
        }

        $permissions = Permission::all();

        $superadmin = Role::where('name', 'superadmin')->first();

        $superadmin->syncPermissions($permissions);
    }
}
