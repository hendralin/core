<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Template extends Model
{
    use HasFactory, LogsActivity;

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

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    protected $attributes = [
        'usage_count' => 0,
    ];

    public function incrementUsageCount()
    {
        $this->increment('usage_count');
    }

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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function wahaSession()
    {
        return $this->belongsTo(Session::class, 'waha_session_id');
    }
}
