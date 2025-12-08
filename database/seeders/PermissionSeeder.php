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
            // SETUP
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

            // BRAND
            'brand.view',
            'brand.create',
            'brand.edit',
            'brand.delete',
            'brand.audit',

            // TIPE KENDARAAN
            'type.view',
            'type.create',
            'type.edit',
            'type.delete',
            'type.audit',

            // KATEGORI KENDARAAN
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',
            'category.audit',

            // MODEL KENDARAAN
            'vehiclemodel.view',
            'vehiclemodel.create',
            'vehiclemodel.edit',
            'vehiclemodel.delete',
            'vehiclemodel.audit',

            // VENDOR
            'vendor.view',
            'vendor.create',
            'vendor.edit',
            'vendor.delete',
            'vendor.audit',

            // SALESMEN
            'salesman.view',
            'salesman.create',
            'salesman.edit',
            'salesman.delete',
            'salesman.audit',

            // WAREHOUSE
            'warehouse.view',
            'warehouse.create',
            'warehouse.edit',
            'warehouse.delete',
            'warehouse.audit',

            // KENDARAAN
            'vehicle.view',
            'vehicle.create',
            'vehicle.edit',
            'vehicle.delete',
            'vehicle.audit',
            'vehicle-modal.view',

            // KOMISI PENJUALAN KENDARAAN
            'vehicle-commission.view',
            'vehicle-commission.create',
            'vehicle-commission.edit',
            'vehicle-commission.delete',
            'vehicle-commission.audit',

            // PERHITUNGAN KREDIT KENDARAAN
            'vehicle-loan-calculation.view',
            'vehicle-loan-calculation.create',
            'vehicle-loan-calculation.edit',
            'vehicle-loan-calculation.delete',
            'vehicle-loan-calculation.audit',

            // PEMBAYARAN PEMBELIAN KENDARAAN
            'vehicle-purchase-payment.view',
            'vehicle-purchase-payment.create',
            'vehicle-purchase-payment.edit',
            'vehicle-purchase-payment.delete',
            'vehicle-purchase-payment.audit',

            // TANDA TERIMA PEMBAYARAN PENJUALAN
            'vehicle-payment-receipt.view',
            'vehicle-payment-receipt.create',
            'vehicle-payment-receipt.edit',
            'vehicle-payment-receipt.delete',
            'vehicle-payment-receipt.print',
            'vehicle-payment-receipt.audit',

            // TANDA TERIMA BPKB
            'vehicle-registration-certificate-receipt.view',
            'vehicle-registration-certificate-receipt.create',
            'vehicle-registration-certificate-receipt.edit',
            'vehicle-registration-certificate-receipt.delete',
            'vehicle-registration-certificate-receipt.print',
            'vehicle-registration-certificate-receipt.audit',

            // SERAH TERIMA KENDARAAN
            'vehicle-handover.view',
            'vehicle-handover.create',
            'vehicle-handover.edit',
            'vehicle-handover.delete',
            'vehicle-handover.print',
            'vehicle-handover.audit',

            // PEMBUKUAN MODAL
            'cost.view',
            'cost.create',
            'cost.edit',
            'cost.delete',
            'cost.audit',
            'cost.approve',
            'cost.reject',

            // PENGELUARAN KAS
            'cashdisbursement.view',
            'cashdisbursement.create',
            'cashdisbursement.edit',
            'cashdisbursement.delete',
            'cashdisbursement.audit',
            'cashdisbursement.approve',
            'cashdisbursement.reject',

            // CASH INJECT
            'cash-inject.view',
            'cash-inject.create',
            'cash-inject.edit',
            'cash-inject.delete',
            'cash-inject.audit',

            // CASH REPORT
            'cash-report.view',
        ];

        foreach ($permissions as $key => $value) {
            Permission::create(['name' => $value]);
        }

        $permissions = Permission::all();

        $superadmin = Role::where('name', 'superadmin')->first();

        $superadmin->syncPermissions($permissions);
    }
}
