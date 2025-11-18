<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompanyLicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = \App\Models\Company::all();

        foreach ($companies as $company) {
            // Skip if company already has license data
            if ($company->license_key || $company->license_type !== 'trial') {
                continue;
            }

            // Generate license key
            $licenseKey = 'LIC-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)) . '-' . date('Y');

            // Set trial license for 30 days
            $company->update([
                'license_key' => $licenseKey,
                'license_type' => 'trial',
                'license_status' => 'active',
                'license_issued_at' => now(),
                'license_expires_at' => now()->addDays(30),
                'max_users' => 5, // Trial limit
                'max_storage_gb' => 1, // Trial storage limit
                'features_enabled' => [
                    'Basic Inventory Management',
                    'Sales Tracking',
                    'Basic Reports',
                    'User Management'
                ],
            ]);

            $this->command->info("Updated license for company: {$company->name} (Trial expires: {$company->license_expires_at->format('Y-m-d')})");
        }

        $this->command->info('Company license seeding completed.');
    }
}
