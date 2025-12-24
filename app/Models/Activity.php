<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed this activity
     */
    public function getCauser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    /**
     * Get activities for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('causer_id', $userId)
                    ->where('causer_type', User::class);
    }

    /**
     * Get activities for a specific subject
     */
    public function scopeBySubject($query, $subject)
    {
        return $query->where('subject_type', get_class($subject))
                    ->where('subject_id', $subject->id);
    }

    /**
     * Get activities by log name
     */
    public function scopeByLogName($query, $logName)
    {
        return $query->where('log_name', $logName);
    }

    /**
     * Get recent activities
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get activity description with formatted data
     */
    public function getFormattedDescriptionAttribute(): string
    {
        $description = $this->description;

        // Replace :name placeholder with subject/causer name
        if (str_contains($description, ':name')) {
            $name = '';

            // Try to get name from subject (the model being acted upon)
            if ($this->subject_type === 'App\\Models\\User' && $this->subject_id) {
                // Use relationship if loaded, otherwise fallback to direct query
                if ($this->relationLoaded('subject') && $this->subject) {
                    $name = $this->subject->name ?? '';
                } else {
                    $subject = User::find($this->subject_id);
                    $name = $subject ? $subject->name : '';
                }
            }

            // If no subject name, try causer (the user performing the action)
            if (empty($name) && $this->causer_type === 'App\\Models\\User' && $this->causer_id) {
                // Use relationship if loaded, otherwise fallback to direct query
                if ($this->relationLoaded('causer') && $this->causer) {
                    $name = $this->causer->name ?? '';
                } else {
                    $causer = User::find($this->causer_id);
                    $name = $causer ? $causer->name : '';
                }
            }

            $description = str_replace(':name', $name, $description);
        }

        // Replace other placeholders with actual values from properties
        if ($this->properties && isset($this->properties['attributes'])) {
            foreach ($this->properties['attributes'] as $key => $value) {
                $stringValue = $this->convertValueToString($value);
                $description = str_replace(":{$key}", $stringValue, $description);
            }
        }

        return $description;
    }

    /**
     * Convert any value to a safe string representation
     */
    public function convertValueToString($value): string
    {
        if (is_array($value)) {
            // For simple arrays (like role names), join with commas
            if (count($value) > 0 && is_string($value[0] ?? null)) {
                return '[' . implode(', ', $value) . ']';
            }

            // For complex arrays (nested), use JSON
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_null($value)) {
            return '';
        }

        return (string) $value;
    }

    /**
     * Get activity icon based on log name
     */
    public function getIconAttribute(): string
    {
        return match($this->log_name) {
            'user' => 'user',
            'role' => 'shield-check',
            'warehouse' => 'building-storefront',
            'item' => 'cube',
            'sales' => 'shopping-bag',
            'purchase' => 'truck',
            'adjustment' => 'wrench-screwdriver',
            'transfer' => 'arrow-right-left',
            default => 'document-text',
        };
    }

    /**
     * Get activity color based on log name
     */
    public function getColorAttribute(): string
    {
        return match($this->log_name) {
            'user' => 'blue',
            'role' => 'purple',
            'warehouse' => 'green',
            'item' => 'yellow',
            'sales' => 'indigo',
            'purchase' => 'red',
            'adjustment' => 'orange',
            'transfer' => 'cyan',
            default => 'gray',
        };
    }
}
