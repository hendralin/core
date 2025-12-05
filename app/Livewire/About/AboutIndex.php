<?php

namespace App\Livewire\About;

use Livewire\Component;
use Livewire\Attributes\Title;

    #[Title('About WOTO v1.17.0')]
class AboutIndex extends Component
{
    public function render()
    {
        $systemInfo = [
            'version' => '1.17.0',
            'php_version' => PHP_VERSION,
            'laravel_version' => 'Laravel ' . app()->version(),
            'database' => config('database.default'),
            'timezone' => config('app.timezone'),
            'environment' => config('app.env'),
        ];

        // Get real-time statistics
        $stats = [
            'brands_count' => \App\Models\Brand::count(),
            'vendors_count' => \App\Models\Vendor::count(),
            'salesmen_count' => \App\Models\Salesman::count(),
            'vehicle_models_count' => \App\Models\VehicleModel::count(),
            'categories_count' => \App\Models\Category::count(),
            'types_count' => \App\Models\Type::count(),
            'vehicles_count' => \App\Models\Vehicle::count(),
            'costs_count' => \App\Models\Cost::count(),
            'warehouses_count' => \App\Models\Warehouse::count(),
            'users_count' => \App\Models\User::count(),
            'companies_count' => \App\Models\Company::count(),
            'equipment_count' => \App\Models\VehicleEquipment::count(),
            'loan_calculations_count' => \App\Models\LoanCalculation::count(),
            'leasings_count' => \App\Models\Leasing::count(),
            'purchase_payments_count' => \App\Models\PurchasePayment::count(),
            'payment_receipts_count' => \App\Models\PaymentReceipt::count(),
            'certificate_receipts_count' => \App\Models\VehicleCertificateReceipt::count(),
            'vehicle_handovers_count' => \App\Models\VehicleHandover::count(),
            // Additional dashboard metrics
            'vehicles_sold_this_month' => \App\Models\Vehicle::where('status', 0)->whereYear('selling_date', now()->year)->whereMonth('selling_date', now()->month)->count(),
            'new_vehicles_this_month' => \App\Models\Vehicle::whereYear('purchase_date', now()->year)->whereMonth('purchase_date', now()->month)->count(),
            'vehicles_ready_for_sale' => \App\Models\Vehicle::where('status', 1)->count(),
        ];

        return view('livewire.about.about-index', compact('systemInfo', 'stats'));
    }
}
