<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use App\Notifications\LicenseExpirationAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class TestEmailLicense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:test-email {email? : Email address to send test to} {--type=expiring : Test type: expiring, expired, or all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test license expiration email notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing License Email Notifications');
        $this->info('=====================================');

        $company = Company::first();

        if (!$company) {
            $this->error('❌ No company found. Please create a company first.');
            return Command::FAILURE;
        }

        $this->info("📋 Company: {$company->name}");

        $email = $this->argument('email') ?? $this->ask('Enter email address to send test to');
        $type = $this->option('type');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Invalid email address');
            return Command::FAILURE;
        }

        // Create a mock user for testing
        $testUser = (object) [
            'name' => 'Test Administrator',
            'email' => $email
        ];

        $this->info("📧 Sending test emails to: {$email}");

        if ($type === 'all' || $type === 'expiring') {
            $this->info('📤 Sending expiring license alert...');

            // Test expiring license (7 days)
            $notification = new LicenseExpirationAlert($company, 7, false);
            Notification::route('mail', $email)->notify($notification);

            $this->info('✅ Expiring license alert sent');
        }

        if ($type === 'all' || $type === 'expired') {
            $this->info('📤 Sending expired license alert...');

            // Test expired license
            $notification = new LicenseExpirationAlert($company, 0, true);
            Notification::route('mail', $email)->notify($notification);

            $this->info('✅ Expired license alert sent');
        }

        // Also test basic email functionality
        $this->info('📤 Sending basic test email...');
        try {
            Mail::raw("License Monitoring System Test\n\nThis is a test email from your license monitoring system.\n\nSent at: " . now()->format('Y-m-d H:i:s'), function($message) use ($email) {
                $message->to($email)
                        ->subject('License Monitoring - Email Test');
            });
            $this->info('✅ Basic test email sent');
        } catch (\Exception $e) {
            $this->error('❌ Basic test email failed: ' . $e->getMessage());
        }

        $this->info('');
        $this->info('🎉 Email testing completed!');
        $this->info('📬 Check your inbox for test emails');
        $this->info('');
        $this->info('💡 If emails don\'t arrive:');
        $this->info('   1. Check your spam/junk folder');
        $this->info('   2. Verify MAIL_* settings in .env file');
        $this->info('   3. Check Laravel logs: storage/logs/laravel.log');
        $this->info('   4. Test with: php artisan license:test-email --type=all');

        return Command::SUCCESS;
    }
}
