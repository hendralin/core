<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'logo',
        'website',
        'tax_id',
        'description',
        'social_media',
        'license_key',
        'license_type',
        'license_status',
        'license_issued_at',
        'license_expires_at',
        'max_users',
        'max_storage_gb',
        'features_enabled',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'social_media' => 'array',
        'features_enabled' => 'array',
        'license_issued_at' => 'datetime',
        'license_expires_at' => 'datetime',
    ];

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'email',
                'phone',
                'address',
                'website',
                'tax_id',
                'description',
                'logo',
                'social_media',
                'license_key',
                'license_type',
                'license_status',
                'license_issued_at',
                'license_expires_at',
                'max_users',
                'max_storage_gb',
                'features_enabled'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the attributes for activity logging
     *
     * @return array<string, string>
     */
    public function getActivitylogAttributes(): array
    {
        return [
            'name',
            'email',
            'phone',
            'address',
            'website',
            'tax_id',
            'description',
            'logo',
            'social_media',
            'license_key',
            'license_type',
            'license_status',
            'license_issued_at',
            'license_expires_at',
            'max_users',
            'max_storage_gb',
            'features_enabled'
        ];
    }

    /**
     * Check if the license is active
     *
     * @return bool
     */
    public function isLicenseActive(): bool
    {
        return $this->license_status === 'active' &&
               (!$this->license_expires_at || $this->license_expires_at->isFuture());
    }

    /**
     * Check if the license is expired
     *
     * @return bool
     */
    public function isLicenseExpired(): bool
    {
        return $this->license_status === 'expired' ||
               ($this->license_expires_at && $this->license_expires_at->isPast());
    }

    /**
     * Check if the license is in trial period
     *
     * @return bool
     */
    public function isTrialLicense(): bool
    {
        return $this->license_type === 'trial';
    }

    /**
     * Get days until license expires
     *
     * @return int|null
     */
    public function getDaysUntilExpiration(): ?int
    {
        if (!$this->license_expires_at) {
            return null;
        }

        return now()->diffInDays($this->license_expires_at, false);
    }

    /**
     * Check if license is expiring soon (within 30 days)
     *
     * @param int $days
     * @return bool
     */
    public function isLicenseExpiringSoon(int $days = 30): bool
    {
        $daysUntilExpiration = $this->getDaysUntilExpiration();
        return $daysUntilExpiration !== null && $daysUntilExpiration <= $days && $daysUntilExpiration >= 0;
    }

    /**
     * Get license status badge color
     *
     * @return string
     */
    public function getLicenseStatusColor(): string
    {
        return match($this->license_status) {
            'active' => $this->isLicenseExpired() ? 'red' : 'green',
            'expired' => 'red',
            'suspended' => 'yellow',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get license type display name
     *
     * @return string
     */
    public function getLicenseTypeDisplay(): string
    {
        return match($this->license_type) {
            'trial' => 'Trial',
            'basic' => 'Basic',
            'premium' => 'Premium',
            'enterprise' => 'Enterprise',
            default => ucfirst($this->license_type ?? 'Unknown')
        };
    }
}
