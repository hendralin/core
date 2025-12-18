<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'waha_session_id',
        'wa_id',
        'name',
        'verified_name',
        'push_name',
        'profile_picture_url',
    ];

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'waha_session_id',
                'wa_id',
                'name',
                'verified_name',
                'push_name',
                'profile_picture_url',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /*
    * Get the session that the contact belongs to
    */
    public function wahaSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'waha_session_id');
    }
}
