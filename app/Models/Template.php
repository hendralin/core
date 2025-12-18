<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Template extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'waha_session_id',
        'name',
        'header',
        'body',
        'usage_count',
        'created_by',
        'updated_by',
        'is_active',
        'last_used_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * The attributes that should be set to default.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'usage_count' => 0,
    ];

    /*
    * Increment the usage count of the template
    */
    public function incrementUsageCount(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'waha_session_id',
                'name',
                'header',
                'body',
                'usage_count',
                'created_by',
                'updated_by',
                'is_active',
                'last_used_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /*
    * Get the user that created the template
    */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    * Get the user that updated the template
    */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    * Get the session that the template belongs to
    */
    public function wahaSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'waha_session_id');
    }
}
