<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestLicenseExpiredSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder is for testing license expiration functionality.
     * It will set the license expiration date to yesterday to simulate expired license.
     */
    public function run(): void
    {
        $company = \App\Models\Company::first();

        if ($company) {
            $company->update([
                'license_expires_at' => now()->subDay(), // Set to yesterday (expired)
                'license_status' => 'expired',
            ]);

            $this->command->info('✅ Test license expired setup completed!');
            $this->command->info("Company: {$company->name}");
            $this->command->info("License expired at: {$company->license_expires_at->format('Y-m-d H:i:s')}");
            $this->command->warn('⚠️  All users will now be redirected to license expired page!');
            $this->command->warn('To restore access, update license_expires_at to future date or run:');
            $this->command->warn('php artisan tinker');
            $this->command->warn('App\Models\Company::first()->update(["license_expires_at" => now()->addDays(30), "license_status" => "active"]);');
        } else {
            $this->command->error('❌ No company found. Please run CompanySeeder first.');
        }
    }
}
