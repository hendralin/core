<?php

use App\Livewire\Cost\CostEdit;
use App\Livewire\Cost\CostShow;
use App\Livewire\Type\TypeEdit;
use App\Livewire\Type\TypeShow;
use App\Livewire\Cost\CostAudit;
use App\Livewire\Cost\CostIndex;
use App\Livewire\Roles\RoleEdit;
use App\Livewire\Roles\RoleShow;
use App\Livewire\Type\TypeAudit;
use App\Livewire\Type\TypeIndex;
use App\Livewire\Users\UserEdit;
use App\Livewire\Users\UserShow;
use App\Livewire\Brand\BrandEdit;
use App\Livewire\Brand\BrandShow;
use App\Livewire\Cost\CostCreate;
use App\Livewire\Roles\RoleAudit;
use App\Livewire\Roles\RoleIndex;
use App\Livewire\Type\TypeCreate;
use App\Livewire\Users\UserAudit;
use App\Livewire\Users\UserIndex;
use App\Livewire\About\AboutIndex;
use App\Livewire\Brand\BrandAudit;
use App\Livewire\Brand\BrandIndex;
use App\Livewire\Roles\RoleCreate;
use App\Livewire\Settings\Profile;
use App\Livewire\Users\UserCreate;
use App\Livewire\Brand\BrandCreate;
use App\Livewire\Settings\Password;
use App\Livewire\Vendor\VendorEdit;
use App\Livewire\Vendor\VendorShow;
use App\Livewire\Vendor\VendorAudit;
use App\Livewire\Vendor\VendorIndex;
use App\Livewire\Company\CompanyEdit;
use App\Livewire\Company\CompanyShow;
use App\Livewire\Settings\Appearance;
use App\Livewire\Vehicle\VehicleEdit;
use App\Livewire\Vehicle\VehicleShow;
use App\Livewire\Vendor\VendorCreate;
use Illuminate\Support\Facades\Route;
use App\Livewire\Vehicle\VehicleAudit;
use App\Livewire\Vehicle\VehicleIndex;
use App\Livewire\Category\CategoryEdit;
use App\Livewire\Category\CategoryShow;
use App\Livewire\Salesman\SalesmanEdit;
use App\Livewire\Salesman\SalesmanShow;
use App\Livewire\Vehicle\VehicleCreate;
use App\Livewire\Category\CategoryAudit;
use App\Livewire\Category\CategoryIndex;
use App\Livewire\Handover\HandoverAudit;
use App\Livewire\Salesman\SalesmanAudit;
use App\Livewire\Salesman\SalesmanIndex;
use App\Livewire\Category\CategoryCreate;
use App\Livewire\Salesman\SalesmanCreate;
use App\Livewire\Warehouse\WarehouseEdit;
use App\Livewire\Warehouse\WarehouseShow;
use App\Livewire\Warehouse\WarehouseAudit;
use App\Livewire\Warehouse\WarehouseIndex;
use App\Livewire\CashInject\CashInjectEdit;
use App\Livewire\CashInject\CashInjectShow;
use App\Livewire\Warehouse\WarehouseCreate;
use App\Livewire\CashInject\CashInjectAudit;
use App\Livewire\CashInject\CashInjectIndex;
use App\Livewire\Commission\CommissionAudit;
use App\Livewire\CashInject\CashInjectCreate;
use App\Livewire\VehicleFile\VehicleFileAudit;
use App\Livewire\VehicleModel\VehicleModelEdit;
use App\Livewire\VehicleModel\VehicleModelShow;
use App\Livewire\VehicleModel\VehicleModelAudit;
use App\Livewire\VehicleModel\VehicleModelIndex;
use App\Livewire\VehicleModel\VehicleModelCreate;
use App\Livewire\BackupRestore\BackupRestoreIndex;
use App\Livewire\Report\CashReport\CashReportIndex;
use App\Livewire\PaymentReceipt\PaymentReceiptAudit;
use App\Livewire\Report\SalesReport\SalesReportIndex;
use App\Livewire\LoanCalculation\LoanCalculationAudit;
use App\Livewire\PurchasePayment\PurchasePaymentAudit;
use App\Livewire\CashDisbursement\CashDisbursementEdit;
use App\Livewire\CashDisbursement\CashDisbursementShow;
use App\Livewire\CashDisbursement\CashDisbursementAudit;
use App\Livewire\CashDisbursement\CashDisbursementIndex;
use App\Livewire\CashDisbursement\CashDisbursementCreate;
use App\Livewire\CertificateReceipt\CertificateReceiptAudit;

// License expired page (accessible even when license is expired)
Route::get('/license-expired', function () {
    return view('license.expired');
})->name('license.expired');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('/', function () {
        return view('dashboard');
    })->name('home');

    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('company/edit', CompanyEdit::class)->name('company.edit')->middleware(['permission:company.edit']);
    Route::get('company', CompanyShow::class)->name('company.show')->middleware(['permission:company.view']);

    Route::get('users', UserIndex::class)->name('users.index')->middleware(['permission:user.view']);
    Route::get('users/audit', UserAudit::class)->name('users.audit')->middleware(['permission:user.audit']);
    Route::get('users/create', UserCreate::class)->name('users.create')->middleware(['permission:user.create']);
    Route::get('users/{user}/edit', UserEdit::class)->name('users.edit')->middleware(['permission:user.edit']);
    Route::get('users/{user}', UserShow::class)->name('users.show')->middleware(['permission:user.view']);
    Route::get('users/download/{filename}', function ($filename) {
        $path = storage_path('app/temp/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    })->name('users.download')->middleware('auth');

    Route::get('roles', RoleIndex::class)->name('roles.index')->middleware(['permission:role.view|role.create|role.edit|role.delete']);
    Route::get('roles/audit', RoleAudit::class)->name('roles.audit')->middleware(['permission:role.audit']);
    Route::get('roles/create', RoleCreate::class)->name('roles.create')->middleware(['permission:role.create']);
    Route::get('roles/{role}/edit', RoleEdit::class)->name('roles.edit')->middleware(['permission:role.edit']);
    Route::get('roles/{role}', RoleShow::class)->name('roles.show')->middleware(['permission:role.view']);

    Route::get('brands', BrandIndex::class)->name('brands.index')->middleware(['permission:brand.view|brand.create|brand.edit|brand.delete']);
    Route::get('brands/audit', BrandAudit::class)->name('brands.audit')->middleware(['permission:brand.audit']);
    Route::get('brands/create', BrandCreate::class)->name('brands.create')->middleware(['permission:brand.create']);
    Route::get('brands/{brand}/edit', BrandEdit::class)->name('brands.edit')->middleware(['permission:brand.edit']);
    Route::get('brands/{brand}', BrandShow::class)->name('brands.show')->middleware(['permission:brand.view']);

    Route::get('categories', CategoryIndex::class)->name('categories.index')->middleware(['permission:category.view|category.create|category.edit|category.delete']);
    Route::get('categories/audit', CategoryAudit::class)->name('categories.audit')->middleware(['permission:category.audit']);
    Route::get('categories/create', CategoryCreate::class)->name('categories.create')->middleware(['permission:category.create']);
    Route::get('categories/{category}/edit', CategoryEdit::class)->name('categories.edit')->middleware(['permission:category.edit']);
    Route::get('categories/{category}', CategoryShow::class)->name('categories.show')->middleware(['permission:category.view']);

    Route::get('types', TypeIndex::class)->name('types.index')->middleware(['permission:type.view|type.create|type.edit|type.delete']);
    Route::get('types/audit', TypeAudit::class)->name('types.audit')->middleware(['permission:type.audit']);
    Route::get('types/create', TypeCreate::class)->name('types.create')->middleware(['permission:type.create']);
    Route::get('types/{type}/edit', TypeEdit::class)->name('types.edit')->middleware(['permission:type.edit']);
    Route::get('types/{type}', TypeShow::class)->name('types.show')->middleware(['permission:type.view']);

    Route::get('models', VehicleModelIndex::class)->name('models.index')->middleware(['permission:vehiclemodel.view|vehiclemodel.create|vehiclemodel.edit|vehiclemodel.delete']);
    Route::get('models/audit', VehicleModelAudit::class)->name('models.audit')->middleware(['permission:vehiclemodel.audit']);
    Route::get('models/create', VehicleModelCreate::class)->name('models.create')->middleware(['permission:vehiclemodel.create']);
    Route::get('models/{vehicleModel}/edit', VehicleModelEdit::class)->name('models.edit')->middleware(['permission:vehiclemodel.edit']);
    Route::get('models/{vehicleModel}', VehicleModelShow::class)->name('models.show')->middleware(['permission:vehiclemodel.view']);

    Route::get('vendors', VendorIndex::class)->name('vendors.index')->middleware(['permission:vendor.view|vendor.create|vendor.edit|vendor.delete']);
    Route::get('vendors/audit', VendorAudit::class)->name('vendors.audit')->middleware(['permission:vendor.audit']);
    Route::get('vendors/create', VendorCreate::class)->name('vendors.create')->middleware(['permission:vendor.create']);
    Route::get('vendors/{vendor}/edit', VendorEdit::class)->name('vendors.edit')->middleware(['permission:vendor.edit']);
    Route::get('vendors/{vendor}', VendorShow::class)->name('vendors.show')->middleware(['permission:vendor.view']);

    Route::get('salesmen', SalesmanIndex::class)->name('salesmen.index')->middleware(['permission:salesman.view|salesman.create|salesman.edit|salesman.delete']);
    Route::get('salesmen/audit', SalesmanAudit::class)->name('salesmen.audit')->middleware(['permission:salesman.audit']);
    Route::get('salesmen/create', SalesmanCreate::class)->name('salesmen.create')->middleware(['permission:salesman.create']);
    Route::get('salesmen/{salesman}/edit', SalesmanEdit::class)->name('salesmen.edit')->middleware(['permission:salesman.edit']);
    Route::get('salesmen/{salesman}', SalesmanShow::class)->name('salesmen.show')->middleware(['permission:salesman.view']);

    Route::get('warehouses', WarehouseIndex::class)->name('warehouses.index')->middleware(['permission:warehouse.view|warehouse.create|warehouse.edit|warehouse.delete']);
    Route::get('warehouses/audit', WarehouseAudit::class)->name('warehouses.audit')->middleware(['permission:warehouse.view']);
    Route::get('warehouses/create', WarehouseCreate::class)->name('warehouses.create')->middleware(['permission:warehouse.create']);
    Route::get('warehouses/{warehouse}/edit', WarehouseEdit::class)->name('warehouses.edit')->middleware(['permission:warehouse.edit']);
    Route::get('warehouses/{warehouse}', WarehouseShow::class)->name('warehouses.show')->middleware(['permission:warehouse.view']);

    Route::get('vehicles', VehicleIndex::class)->name('vehicles.index')->middleware(['permission:vehicle.view|vehicle.create|vehicle.edit|vehicle.delete']);
    Route::get('vehicles/audit', VehicleAudit::class)->name('vehicles.audit')->middleware(['permission:vehicle.view']);
    Route::get('vehicles/create', VehicleCreate::class)->name('vehicles.create')->middleware(['permission:vehicle.create']);
    Route::get('vehicles/{vehicle}/edit', VehicleEdit::class)->name('vehicles.edit')->middleware(['permission:vehicle.edit']);
    Route::get('vehicles/{vehicle}', VehicleShow::class)->name('vehicles.show')->middleware(['permission:vehicle.view']);

    Route::get('commissions/audit', CommissionAudit::class)->name('commissions.audit')->middleware(['permission:vehicle-commission.audit']);
    Route::get('loan-calculations/audit', LoanCalculationAudit::class)->name('loan-calculations.audit')->middleware(['permission:vehicle-loan-calculation.audit']);
    Route::get('purchase-payments/audit', PurchasePaymentAudit::class)->name('purchase-payments.audit')->middleware(['permission:vehicle-purchase-payment.audit']);
    Route::get('payment-receipts/audit', PaymentReceiptAudit::class)->name('payment-receipts.audit')->middleware(['permission:vehicle-payment-receipt.audit']);
    Route::get('certificate-receipts/audit', CertificateReceiptAudit::class)->name('certificate-receipts.audit')->middleware(['permission:vehicle-registration-certificate-receipt.audit']);
    Route::get('handovers/audit', HandoverAudit::class)->name('handovers.audit')->middleware(['permission:vehicle-handover.audit']);
    Route::get('vehicle-files/audit', VehicleFileAudit::class)->name('vehicle-files.audit')->middleware(['permission:vehicle-file.audit']);

    Route::get('costs', CostIndex::class)->name('costs.index')->middleware(['permission:cost.view|cost.create|cost.edit|cost.delete']);
    Route::get('costs/audit', CostAudit::class)->name('costs.audit')->middleware(['permission:cost.audit']);
    Route::get('costs/create', CostCreate::class)->name('costs.create')->middleware(['permission:cost.create']);
    Route::get('costs/{cost}/edit', CostEdit::class)->name('costs.edit')->middleware(['permission:cost.edit']);
    Route::get('costs/{cost}', CostShow::class)->name('costs.show')->middleware(['permission:cost.view']);

    Route::get('cash-disbursements', CashDisbursementIndex::class)->name('cash-disbursements.index')->middleware(['permission:cashdisbursement.view|cashdisbursement.create|cashdisbursement.edit|cashdisbursement.delete']);
    Route::get('cash-disbursements/audit', CashDisbursementAudit::class)->name('cash-disbursements.audit')->middleware(['permission:cashdisbursement.audit']);
    Route::get('cash-disbursements/create', CashDisbursementCreate::class)->name('cash-disbursements.create')->middleware(['permission:cashdisbursement.create']);
    Route::get('cash-disbursements/{cost}/edit', CashDisbursementEdit::class)->name('cash-disbursements.edit')->middleware(['permission:cashdisbursement.edit']);
    Route::get('cash-disbursements/{cost}', CashDisbursementShow::class)->name('cash-disbursements.show')->middleware(['permission:cashdisbursement.view']);

    Route::get('cash-injects', CashInjectIndex::class)->name('cash-injects.index')->middleware(['permission:cash-inject.view|cash-inject.create|cash-inject.edit|cash-inject.delete']);
    Route::get('cash-injects/audit', CashInjectAudit::class)->name('cash-injects.audit')->middleware(['permission:cash-inject.audit']);
    Route::get('cash-injects/create', CashInjectCreate::class)->name('cash-injects.create')->middleware(['permission:cash-inject.create']);
    Route::get('cash-injects/{cost}/edit', CashInjectEdit::class)->name('cash-injects.edit')->middleware(['permission:cash-inject.edit']);
    Route::get('cash-injects/{cost}', CashInjectShow::class)->name('cash-injects.show')->middleware(['permission:cash-inject.view']);

    Route::get('cash-reports', CashReportIndex::class)->name('cash-reports.index')->middleware(['permission:cash-report.view']);
    Route::get('sales-reports', SalesReportIndex::class)->name('sales-reports.index')->middleware(['permission:sales-report.view']);

    Route::prefix('backup-restore')->name('backup-restore.')->group(function () {
        Route::get('/', BackupRestoreIndex::class)->name('index')->middleware(['permission:backup-restore.view']);
    });

    Route::get('about', AboutIndex::class)->name('about.index');
});

require __DIR__ . '/auth.php';
