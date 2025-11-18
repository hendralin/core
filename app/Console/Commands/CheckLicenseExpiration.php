<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use App\Notifications\LicenseExpirationAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckLicenseExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:check-expiration {--days=30 : Check for licenses expiring within specified days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for license expiration and send alerts to administrators';

    /**
     * Alert thresholds in days
     */
    protected array $alertThresholds = [30, 7, 1];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking license expiration...');

        $company = Company::first();

        if (!$company) {
            $this->error('❌ No company found in database');
            return Command::FAILURE;
        }

        $this->info("📋 Company: {$company->name}");
        $this->info("🔑 License Key: " . ($company->license_key ?? 'N/A'));
        $this->info("📅 License Expires: " . ($company->license_expires_at ? $company->license_expires_at->format('Y-m-d H:i:s') : 'Never'));

        // Check if license is already expired
        if ($company->isLicenseExpired()) {
            $this->error('🚨 LICENSE IS EXPIRED!');
            $this->sendExpiredLicenseAlert($company);
            return Command::FAILURE;
        }

        // Check for upcoming expiration
        $daysUntilExpiration = $company->getDaysUntilExpiration();

        if ($daysUntilExpiration === null) {
            $this->info('✅ License has no expiration date');
            return Command::SUCCESS;
        }

        $this->info("⏰ Days until expiration: {$daysUntilExpiration}");

        // Check if we need to send alerts
        $alertSent = false;
        foreach ($this->alertThresholds as $threshold) {
            if ($daysUntilExpiration <= $threshold && $daysUntilExpiration >= 0) {
                $this->warn("⚠️  License expires in {$daysUntilExpiration} days (threshold: {$threshold} days)");
                $this->sendExpirationAlert($company, $daysUntilExpiration, $threshold);
                $alertSent = true;
                break; // Send only one alert (the most urgent one)
            }
        }

        if (!$alertSent && $daysUntilExpiration > max($this->alertThresholds)) {
            $this->info("✅ License is valid (expires in {$daysUntilExpiration} days)");
        }

        // Log the check
        Log::info('License expiration check completed', [
            'company_id' => $company->id,
            'license_key' => $company->license_key,
            'days_until_expiration' => $daysUntilExpiration,
            'checked_at' => now(),
        ]);

        return Command::SUCCESS;
    }

    /**
     * Send alert for expired license
     */
    protected function sendExpiredLicenseAlert(Company $company): void
    {
        $this->info('📧 Sending expired license alert...');

        $adminUsers = User::role(['superadmin', 'admin'])->where('status', '1')->get();

        if ($adminUsers->isEmpty()) {
            $this->warn('⚠️  No admin users found to notify');
            return;
        }

        Notification::send($adminUsers, new LicenseExpirationAlert($company, 0, true));

        $this->info("✅ Alert sent to {$adminUsers->count()} admin user(s)");
    }

    /**
     * Send alert for upcoming license expiration
     */
    protected function sendExpirationAlert(Company $company, int $daysUntilExpiration, int $threshold): void
    {
        $this->info("📧 Sending expiration alert (expires in {$daysUntilExpiration} days)...");

        $adminUsers = User::role(['superadmin', 'admin'])->where('status', '1')->get();

        if ($adminUsers->isEmpty()) {
            $this->warn('⚠️  No admin users found to notify');
            return;
        }

        Notification::send($adminUsers, new LicenseExpirationAlert($company, $daysUntilExpiration, false));

        $this->info("✅ Alert sent to {$adminUsers->count()} admin user(s)");
    }
}
