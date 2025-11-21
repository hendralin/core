<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'waha_session_id',
        'wa_id',
        'name',
        'verified_name',
        'push_name',
        'profile_picture_url',
    ];

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

    public function wahaSession()
    {
        return $this->belongsTo(Session::class, 'waha_session_id');
    }
}
