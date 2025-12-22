<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'birth_date',
        'address',
        'password',
        'avatar',
        'status',
        'last_login_at',
        'timezone',
        'is_email_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'last_login_at' => 'datetime',
            'is_email_verified' => 'boolean',
            'status' => 'integer',
        ];
    }

    /**
     * Set the status attribute
     */
    public function setStatusAttribute($value)
    {
        // Convert to integer first, then to string for MySQL enum compatibility
        $intValue = (int) $value;
        $this->attributes['status'] = (string) $intValue;
    }

    /**
     * Get the status attribute
     */
    public function getStatusAttribute($value)
    {
        // Ensure we return integer for consistency in application logic
        return (int) $value;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the user's avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && \Illuminate\Support\Facades\Storage::disk('avatars')->exists($this->avatar)) {
            return \Illuminate\Support\Facades\Storage::disk('avatars')->url($this->avatar);
        }

        // Return initials-based avatar from UI Avatars service
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&color=7F9CF5&background=EBF4FF&size=128&font-size=0.6";
    }

    /**
     * Get the user's account age
     */
    public function getAccountAgeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

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
                'birth_date',
                'address',
                'status',
                'timezone',
                'avatar',
                'is_email_verified'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('user');
    }

    /**
     * Get the description for the activity log
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        return match($eventName) {
            'created' => "User :name was created",
            'updated' => "User :name was updated",
            'deleted' => "User :name was deleted",
            default => "User :name was {$eventName}",
        };
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status == 1;
    }

    /**
     * Check if user is inactive
     */
    public function isInactive(): bool
    {
        return $this->status == 0;
    }

    /**
     * Check if user is pending
     */
    public function isPending(): bool
    {
        return $this->status == 2;
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            0 => 'Inactive',
            1 => 'Active',
            2 => 'Pending',
            default => 'Unknown'
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            0 => 'danger',
            1 => 'success',
            2 => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Activate the user
     */
    public function activate(): void
    {
        $this->update(['status' => 1]);
    }

    /**
     * Deactivate the user
     */
    public function deactivate(): void
    {
        $this->update(['status' => 0]);
    }

    /**
     * Get the company logo URL directly
     */
    public function getCompanyLogoUrlAttribute(): ?string
    {
        $company = Company::first();
        if ($company && $company->logo) {
            return Storage::disk('logos')->url($company->logo);
        }
        return null;
    }

    /**
     * Get the company logo path directly
     */
    public function getCompanyLogoAttribute(): ?string
    {
        $company = Company::first();
        return $company ? $company->logo : null;
    }

    /**
     * Get the config for the user.
     */
    public function config()
    {
        return $this->hasOne(Config::class);
    }
}
