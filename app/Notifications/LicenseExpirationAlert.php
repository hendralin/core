<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseExpirationAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected Company $company;
    protected int $daysUntilExpiration;
    protected bool $isExpired;

    /**
     * Create a new notification instance.
     */
    public function __construct(Company $company, int $daysUntilExpiration, bool $isExpired = false)
    {
        $this->company = $company;
        $this->daysUntilExpiration = $daysUntilExpiration;
        $this->isExpired = $isExpired;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->isExpired
            ? "🚨 LICENSE EXPIRED - {$this->company->name}"
            : "⚠️ LICENSE EXPIRING SOON - {$this->company->name}";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},");

        if ($this->isExpired) {
            $mail->line('🚨 **URGENT: Your software license has expired!**')
                ->line('All users have been blocked from accessing the system until the license is renewed.')
                ->line('')
                ->line('**License Details:**')
                ->line("• Company: {$this->company->name}")
                ->line("• License Key: " . ($this->company->license_key ?? 'N/A'))
                ->line("• Expired On: " . ($this->company->license_expires_at ? $this->company->license_expires_at->format('F d, Y') : 'N/A'))
                ->line('')
                ->error('Immediate action required to restore system access.');
        } else {
            $daysText = $this->daysUntilExpiration === 1 ? '1 day' : "{$this->daysUntilExpiration} days";

            $mail->line("⚠️ **License Expiration Alert**")
                ->line("Your software license will expire in **{$daysText}**.")
                ->line('')
                ->line('**License Details:**')
                ->line("• Company: {$this->company->name}")
                ->line("• License Key: " . ($this->company->license_key ?? 'N/A'))
                ->line("• Expires On: " . ($this->company->license_expires_at ? $this->company->license_expires_at->format('F d, Y') : 'Never'))
                ->line("• License Type: {$this->company->getLicenseTypeDisplay()}")
                ->line('')
                ->line('Please renew your license before it expires to avoid service interruption.');
        }

        $mail->action('Manage License', url('/company'))
            ->line('If you need assistance with license renewal, please contact our support team.')
            ->line('Thank you for using our software!')
            ->salutation('Best regards,' . PHP_EOL . config('app.name') . ' Team');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'company_id' => $this->company->id,
            'company_name' => $this->company->name,
            'license_key' => $this->company->license_key,
            'license_expires_at' => $this->company->license_expires_at,
            'days_until_expiration' => $this->daysUntilExpiration,
            'is_expired' => $this->isExpired,
            'alert_type' => $this->isExpired ? 'expired' : 'expiring_soon',
        ];
    }
}
