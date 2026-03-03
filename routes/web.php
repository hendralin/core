<?php

use App\Http\Controllers\SalarySlipController;
use App\Livewire\About\AboutIndex;
use App\Livewire\BackupRestore\BackupRestoreIndex;
use App\Livewire\Brand\BrandAudit;
use App\Livewire\Brand\BrandCreate;
use App\Livewire\Brand\BrandEdit;
use App\Livewire\Brand\BrandIndex;
use App\Livewire\Brand\BrandShow;
use App\Livewire\CashDisbursement\CashDisbursementAudit;
use App\Livewire\CashDisbursement\CashDisbursementCreate;
use App\Livewire\CashDisbursement\CashDisbursementEdit;
use App\Livewire\CashDisbursement\CashDisbursementIndex;
use App\Livewire\CashDisbursement\CashDisbursementShow;
use App\Livewire\CashInject\CashInjectAudit;
use App\Livewire\CashInject\CashInjectCreate;
use App\Livewire\CashInject\CashInjectEdit;
use App\Livewire\CashInject\CashInjectIndex;
use App\Livewire\CashInject\CashInjectShow;
use App\Livewire\Category\CategoryAudit;
use App\Livewire\Category\CategoryCreate;
use App\Livewire\Category\CategoryEdit;
use App\Livewire\Category\CategoryIndex;
use App\Livewire\Category\CategoryShow;
use App\Livewire\CertificateReceipt\CertificateReceiptAudit;
use App\Livewire\ChangeLog\ChangeLogIndex;
use App\Livewire\Commission\CommissionAudit;
use App\Livewire\Company\CompanyEdit;
use App\Livewire\Company\CompanyShow;
use App\Livewire\Cost\CostAudit;
use App\Livewire\Cost\CostCreate;
use App\Livewire\Cost\CostEdit;
use App\Livewire\Cost\CostIndex;
use App\Livewire\Cost\CostShow;
use App\Livewire\Employee\EmployeeAudit;
use App\Livewire\Employee\EmployeeCreate;
use App\Livewire\Employee\EmployeeEdit;
use App\Livewire\Employee\EmployeeIndex;
use App\Livewire\Employee\EmployeeShow;
use App\Livewire\Handover\HandoverAudit;
use App\Livewire\LoanCalculation\LoanCalculationAudit;
use App\Livewire\PaymentReceipt\PaymentReceiptAudit;
use App\Livewire\Position\PositionAudit;
use App\Livewire\Position\PositionCreate;
use App\Livewire\Position\PositionEdit;
use App\Livewire\Position\PositionIndex;
use App\Livewire\Position\PositionShow;
use App\Livewire\PurchasePayment\PurchasePaymentAudit;
use App\Livewire\Report\CashReport\CashReportIndex;
use App\Livewire\Report\SalesReport\SalesReportIndex;
use App\Livewire\Report\TaxCashReport\TaxCashReportIndex;
use App\Livewire\Roles\RoleAudit;
use App\Livewire\Roles\RoleCreate;
use App\Livewire\Roles\RoleEdit;
use App\Livewire\Roles\RoleIndex;
use App\Livewire\Roles\RoleShow;
use App\Livewire\Salary\SalaryAudit;
use App\Livewire\Salary\SalaryCreate;
use App\Livewire\Salary\SalaryEdit;
use App\Livewire\Salary\SalaryIndex;
use App\Livewire\Salary\SalaryShow;
use App\Livewire\SalaryComponent\SalaryComponentAudit;
use App\Livewire\SalaryComponent\SalaryComponentCreate;
use App\Livewire\SalaryComponent\SalaryComponentEdit;
use App\Livewire\SalaryComponent\SalaryComponentIndex;
use App\Livewire\SalaryComponent\SalaryComponentShow;
use App\Livewire\Salesman\SalesmanAudit;
use App\Livewire\Salesman\SalesmanCreate;
use App\Livewire\Salesman\SalesmanEdit;
use App\Livewire\Salesman\SalesmanIndex;
use App\Livewire\Salesman\SalesmanShow;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Type\TypeAudit;
use App\Livewire\Type\TypeCreate;
use App\Livewire\Type\TypeEdit;
use App\Livewire\Type\TypeIndex;
use App\Livewire\Type\TypeShow;
use App\Livewire\Users\UserAudit;
use App\Livewire\Users\UserCreate;
use App\Livewire\Users\UserEdit;
use App\Livewire\Users\UserIndex;
use App\Livewire\Users\UserShow;
use App\Livewire\Vehicle\VehicleAudit;
use App\Livewire\Vehicle\VehicleCreate;
use App\Livewire\Vehicle\VehicleEdit;
use App\Livewire\Vehicle\VehicleIndex;
use App\Livewire\Vehicle\VehicleShow;
use App\Livewire\VehicleFile\VehicleFileAudit;
use App\Livewire\VehicleModel\VehicleModelAudit;
use App\Livewire\VehicleModel\VehicleModelCreate;
use App\Livewire\VehicleModel\VehicleModelEdit;
use App\Livewire\VehicleModel\VehicleModelIndex;
use App\Livewire\VehicleModel\VehicleModelShow;
use App\Livewire\VehicleTaxPayment\VehicleTaxPaymentAudit;
use App\Livewire\VehicleTaxPayment\VehicleTaxPaymentCreate;
use App\Livewire\VehicleTaxPayment\VehicleTaxPaymentEdit;
use App\Livewire\VehicleTaxPayment\VehicleTaxPaymentIndex;
use App\Livewire\VehicleTaxPayment\VehicleTaxPaymentShow;
use App\Livewire\Vendor\VendorAudit;
use App\Livewire\Vendor\VendorCreate;
use App\Livewire\Vendor\VendorEdit;
use App\Livewire\Vendor\VendorIndex;
use App\Livewire\Vendor\VendorShow;
use App\Livewire\Warehouse\WarehouseAudit;
use App\Livewire\Warehouse\WarehouseCreate;
use App\Livewire\Warehouse\WarehouseEdit;
use App\Livewire\Warehouse\WarehouseIndex;
use App\Livewire\Warehouse\WarehouseShow;
use Illuminate\Support\Facades\Route;

// License expired page (accessible even when license is expired)
Route::get('/license-expired', function () {
    return view('license.expired');
})->name('license.expired');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', Profile::class)->name('settings.profile');
    Route::livewire('settings/password', Password::class)->name('settings.password');
    Route::livewire('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('/', function () {
        return view('dashboard');
    })->name('home');

    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::livewire('company/edit', CompanyEdit::class)->name('company.edit')->middleware(['permission:company.edit']);
    Route::livewire('company', CompanyShow::class)->name('company.show')->middleware(['permission:company.view']);

    Route::livewire('users', UserIndex::class)->name('users.index')->middleware(['permission:user.view']);
    Route::livewire('users/audit', UserAudit::class)->name('users.audit')->middleware(['permission:user.audit']);
    Route::livewire('users/create', UserCreate::class)->name('users.create')->middleware(['permission:user.create']);
    Route::livewire('users/{user}/edit', UserEdit::class)->name('users.edit')->middleware(['permission:user.edit']);
    Route::livewire('users/{user}', UserShow::class)->name('users.show')->middleware(['permission:user.view']);
    Route::get('users/download/{filename}', function ($filename) {
        $path = storage_path('app/temp/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    })->name('users.download')->middleware('auth');

    Route::livewire('roles', RoleIndex::class)->name('roles.index')->middleware(['permission:role.view|role.create|role.edit|role.delete']);
    Route::livewire('roles/audit', RoleAudit::class)->name('roles.audit')->middleware(['permission:role.audit']);
    Route::livewire('roles/create', RoleCreate::class)->name('roles.create')->middleware(['permission:role.create']);
    Route::livewire('roles/{role}/edit', RoleEdit::class)->name('roles.edit')->middleware(['permission:role.edit']);
    Route::livewire('roles/{role}', RoleShow::class)->name('roles.show')->middleware(['permission:role.view']);

    Route::livewire('brands', BrandIndex::class)->name('brands.index')->middleware(['permission:brand.view|brand.create|brand.edit|brand.delete']);
    Route::livewire('brands/audit', BrandAudit::class)->name('brands.audit')->middleware(['permission:brand.audit']);
    Route::livewire('brands/create', BrandCreate::class)->name('brands.create')->middleware(['permission:brand.create']);
    Route::livewire('brands/{brand}/edit', BrandEdit::class)->name('brands.edit')->middleware(['permission:brand.edit']);
    Route::livewire('brands/{brand}', BrandShow::class)->name('brands.show')->middleware(['permission:brand.view']);

    Route::livewire('categories', CategoryIndex::class)->name('categories.index')->middleware(['permission:category.view|category.create|category.edit|category.delete']);
    Route::livewire('categories/audit', CategoryAudit::class)->name('categories.audit')->middleware(['permission:category.audit']);
    Route::livewire('categories/create', CategoryCreate::class)->name('categories.create')->middleware(['permission:category.create']);
    Route::livewire('categories/{category}/edit', CategoryEdit::class)->name('categories.edit')->middleware(['permission:category.edit']);
    Route::livewire('categories/{category}', CategoryShow::class)->name('categories.show')->middleware(['permission:category.view']);

    Route::livewire('types', TypeIndex::class)->name('types.index')->middleware(['permission:type.view|type.create|type.edit|type.delete']);
    Route::livewire('types/audit', TypeAudit::class)->name('types.audit')->middleware(['permission:type.audit']);
    Route::livewire('types/create', TypeCreate::class)->name('types.create')->middleware(['permission:type.create']);
    Route::livewire('types/{type}/edit', TypeEdit::class)->name('types.edit')->middleware(['permission:type.edit']);
    Route::livewire('types/{type}', TypeShow::class)->name('types.show')->middleware(['permission:type.view']);

    Route::livewire('models', VehicleModelIndex::class)->name('models.index')->middleware(['permission:vehiclemodel.view|vehiclemodel.create|vehiclemodel.edit|vehiclemodel.delete']);
    Route::livewire('models/audit', VehicleModelAudit::class)->name('models.audit')->middleware(['permission:vehiclemodel.audit']);
    Route::livewire('models/create', VehicleModelCreate::class)->name('models.create')->middleware(['permission:vehiclemodel.create']);
    Route::livewire('models/{vehicleModel}/edit', VehicleModelEdit::class)->name('models.edit')->middleware(['permission:vehiclemodel.edit']);
    Route::livewire('models/{vehicleModel}', VehicleModelShow::class)->name('models.show')->middleware(['permission:vehiclemodel.view']);

    Route::livewire('vendors', VendorIndex::class)->name('vendors.index')->middleware(['permission:vendor.view|vendor.create|vendor.edit|vendor.delete']);
    Route::livewire('vendors/audit', VendorAudit::class)->name('vendors.audit')->middleware(['permission:vendor.audit']);
    Route::livewire('vendors/create', VendorCreate::class)->name('vendors.create')->middleware(['permission:vendor.create']);
    Route::livewire('vendors/{vendor}/edit', VendorEdit::class)->name('vendors.edit')->middleware(['permission:vendor.edit']);
    Route::livewire('vendors/{vendor}', VendorShow::class)->name('vendors.show')->middleware(['permission:vendor.view']);

    Route::livewire('salesmen', SalesmanIndex::class)->name('salesmen.index')->middleware(['permission:salesman.view|salesman.create|salesman.edit|salesman.delete']);
    Route::livewire('salesmen/audit', SalesmanAudit::class)->name('salesmen.audit')->middleware(['permission:salesman.audit']);
    Route::livewire('salesmen/create', SalesmanCreate::class)->name('salesmen.create')->middleware(['permission:salesman.create']);
    Route::livewire('salesmen/{salesman}/edit', SalesmanEdit::class)->name('salesmen.edit')->middleware(['permission:salesman.edit']);
    Route::livewire('salesmen/{salesman}', SalesmanShow::class)->name('salesmen.show')->middleware(['permission:salesman.view']);

    Route::livewire('warehouses', WarehouseIndex::class)->name('warehouses.index')->middleware(['permission:warehouse.view|warehouse.create|warehouse.edit|warehouse.delete']);
    Route::livewire('warehouses/audit', WarehouseAudit::class)->name('warehouses.audit')->middleware(['permission:warehouse.audit']);
    Route::livewire('warehouses/create', WarehouseCreate::class)->name('warehouses.create')->middleware(['permission:warehouse.create']);
    Route::livewire('warehouses/{warehouse}/edit', WarehouseEdit::class)->name('warehouses.edit')->middleware(['permission:warehouse.edit']);
    Route::livewire('warehouses/{warehouse}', WarehouseShow::class)->name('warehouses.show')->middleware(['permission:warehouse.view']);

    Route::livewire('vehicles', VehicleIndex::class)->name('vehicles.index')->middleware(['permission:vehicle.view|vehicle.create|vehicle.edit|vehicle.delete']);
    Route::livewire('vehicles/audit', VehicleAudit::class)->name('vehicles.audit')->middleware(['permission:vehicle.audit']);
    Route::livewire('vehicles/create', VehicleCreate::class)->name('vehicles.create')->middleware(['permission:vehicle.create']);
    Route::livewire('vehicles/{vehicle}/edit', VehicleEdit::class)->name('vehicles.edit')->middleware(['permission:vehicle.edit']);
    Route::livewire('vehicles/{vehicle}', VehicleShow::class)->name('vehicles.show')->middleware(['permission:vehicle.view']);

    Route::livewire('commissions/audit', CommissionAudit::class)->name('commissions.audit')->middleware(['permission:vehicle-commission.audit']);
    Route::livewire('loan-calculations/audit', LoanCalculationAudit::class)->name('loan-calculations.audit')->middleware(['permission:vehicle-loan-calculation.audit']);
    Route::livewire('purchase-payments/audit', PurchasePaymentAudit::class)->name('purchase-payments.audit')->middleware(['permission:vehicle-purchase-payment.audit']);
    Route::livewire('payment-receipts/audit', PaymentReceiptAudit::class)->name('payment-receipts.audit')->middleware(['permission:vehicle-payment-receipt.audit']);
    Route::livewire('certificate-receipts/audit', CertificateReceiptAudit::class)->name('certificate-receipts.audit')->middleware(['permission:vehicle-registration-certificate-receipt.audit']);
    Route::livewire('handovers/audit', HandoverAudit::class)->name('handovers.audit')->middleware(['permission:vehicle-handover.audit']);
    Route::livewire('vehicle-files/audit', VehicleFileAudit::class)->name('vehicle-files.audit')->middleware(['permission:vehicle-file.audit']);

    Route::livewire('costs', CostIndex::class)->name('costs.index')->middleware(['permission:cost.view|cost.create|cost.edit|cost.delete']);
    Route::livewire('costs/audit', CostAudit::class)->name('costs.audit')->middleware(['permission:cost.audit']);
    Route::livewire('costs/create', CostCreate::class)->name('costs.create')->middleware(['permission:cost.create']);
    Route::livewire('costs/{cost}/edit', CostEdit::class)->name('costs.edit')->middleware(['permission:cost.edit']);
    Route::livewire('costs/{cost}', CostShow::class)->name('costs.show')->middleware(['permission:cost.view']);

    Route::livewire('cash-disbursements', CashDisbursementIndex::class)->name('cash-disbursements.index')->middleware(['permission:cashdisbursement.view|cashdisbursement.create|cashdisbursement.edit|cashdisbursement.delete']);
    Route::livewire('cash-disbursements/audit', CashDisbursementAudit::class)->name('cash-disbursements.audit')->middleware(['permission:cashdisbursement.audit']);
    Route::livewire('cash-disbursements/create', CashDisbursementCreate::class)->name('cash-disbursements.create')->middleware(['permission:cashdisbursement.create']);
    Route::livewire('cash-disbursements/{cost}/edit', CashDisbursementEdit::class)->name('cash-disbursements.edit')->middleware(['permission:cashdisbursement.edit']);
    Route::livewire('cash-disbursements/{cost}', CashDisbursementShow::class)->name('cash-disbursements.show')->middleware(['permission:cashdisbursement.view']);

    Route::livewire('vehicle-tax-payments', VehicleTaxPaymentIndex::class)->name('vehicle-tax-payments.index')->middleware(['permission:vehicle-tax-payment.view|vehicle-tax-payment.create|vehicle-tax-payment.edit|vehicle-tax-payment.delete']);
    Route::livewire('vehicle-tax-payments/audit', VehicleTaxPaymentAudit::class)->name('vehicle-tax-payments.audit')->middleware(['permission:vehicle-tax-payment.audit']);
    Route::livewire('vehicle-tax-payments/create', VehicleTaxPaymentCreate::class)->name('vehicle-tax-payments.create')->middleware(['permission:vehicle-tax-payment.create']);
    Route::livewire('vehicle-tax-payments/{vehicleTaxPayment}/edit', VehicleTaxPaymentEdit::class)->name('vehicle-tax-payments.edit')->middleware(['permission:vehicle-tax-payment.edit']);
    Route::livewire('vehicle-tax-payments/{vehicleTaxPayment}', VehicleTaxPaymentShow::class)->name('vehicle-tax-payments.show')->middleware(['permission:vehicle-tax-payment.view']);

    Route::livewire('cash-injects', CashInjectIndex::class)->name('cash-injects.index')->middleware(['permission:cash-inject.view|cash-inject.create|cash-inject.edit|cash-inject.delete']);
    Route::livewire('cash-injects/audit', CashInjectAudit::class)->name('cash-injects.audit')->middleware(['permission:cash-inject.audit']);
    Route::livewire('cash-injects/create', CashInjectCreate::class)->name('cash-injects.create')->middleware(['permission:cash-inject.create']);
    Route::livewire('cash-injects/{cost}/edit', CashInjectEdit::class)->name('cash-injects.edit')->middleware(['permission:cash-inject.edit']);
    Route::livewire('cash-injects/{cost}', CashInjectShow::class)->name('cash-injects.show')->middleware(['permission:cash-inject.view']);

    Route::livewire('positions', PositionIndex::class)->name('positions.index')->middleware(['permission:position.view|position.create|position.edit|position.delete']);
    Route::livewire('positions/audit', PositionAudit::class)->name('positions.audit')->middleware(['permission:position.audit']);
    Route::livewire('positions/create', PositionCreate::class)->name('positions.create')->middleware(['permission:position.create']);
    Route::livewire('positions/{position}/edit', PositionEdit::class)->name('positions.edit')->middleware(['permission:position.edit']);
    Route::livewire('positions/{position}', PositionShow::class)->name('positions.show')->middleware(['permission:position.view']);

    Route::livewire('salary-components', SalaryComponentIndex::class)->name('salary-components.index')->middleware(['permission:salary-component.view|salary-component.create|salary-component.edit|salary-component.delete']);
    Route::livewire('salary-components/audit', SalaryComponentAudit::class)->name('salary-components.audit')->middleware(['permission:salary-component.audit']);
    Route::livewire('salary-components/create', SalaryComponentCreate::class)->name('salary-components.create')->middleware(['permission:salary-component.create']);
    Route::livewire('salary-components/{salaryComponent}/edit', SalaryComponentEdit::class)->name('salary-components.edit')->middleware(['permission:salary-component.edit']);
    Route::livewire('salary-components/{salaryComponent}', SalaryComponentShow::class)->name('salary-components.show')->middleware(['permission:salary-component.view']);

    Route::livewire('employees', EmployeeIndex::class)->name('employees.index')->middleware(['permission:employee.view|employee.create|employee.edit|employee.delete']);
    Route::livewire('employees/audit', EmployeeAudit::class)->name('employees.audit')->middleware(['permission:employee.audit']);
    Route::livewire('employees/create', EmployeeCreate::class)->name('employees.create')->middleware(['permission:employee.create']);
    Route::livewire('employees/{employee}/edit', EmployeeEdit::class)->name('employees.edit')->middleware(['permission:employee.edit']);
    Route::livewire('employees/{employee}', EmployeeShow::class)->name('employees.show')->middleware(['permission:employee.view']);

    Route::livewire('salaries', SalaryIndex::class)->name('salaries.index')->middleware(['permission:salary.view|salary.create|salary.edit|salary.delete']);
    Route::livewire('salaries/audit', SalaryAudit::class)->name('salaries.audit')->middleware(['permission:salary.audit']);
    Route::livewire('salaries/create', SalaryCreate::class)->name('salaries.create')->middleware(['permission:salary.create']);
    Route::get('salaries/{salary}/print', [SalarySlipController::class, 'show'])->name('salaries.print')->middleware(['permission:salary.print']);
    Route::livewire('salaries/{salary}/edit', SalaryEdit::class)->name('salaries.edit')->middleware(['permission:salary.edit']);
    Route::livewire('salaries/{salary}', SalaryShow::class)->name('salaries.show')->middleware(['permission:salary.view']);

    Route::livewire('cash-reports', CashReportIndex::class)->name('cash-reports.index')->middleware(['permission:cash-report.view']);
    Route::livewire('tax-cash-reports', TaxCashReportIndex::class)->name('tax-cash-reports.index')->middleware(['permission:tax-cash-report.view']);
    Route::livewire('sales-reports', SalesReportIndex::class)->name('sales-reports.index')->middleware(['permission:sales-report.view']);

    Route::prefix('backup-restore')->name('backup-restore.')->group(function () {
        Route::livewire('/', BackupRestoreIndex::class)->name('index')->middleware(['permission:backup-restore.view']);
    });

    Route::livewire('about', AboutIndex::class)->name('about.index');
    Route::livewire('change-log', ChangeLogIndex::class)->name('change-log.index');
});

require __DIR__ . '/auth.php';
