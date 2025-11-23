<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'waha_session_id',
        'group_wa_id',
        'name',
        'detail',
        'picture_url',
    ];

    protected $casts = [
        'detail' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'waha_session_id',
                'group_wa_id',
                'name',
                'picture_url',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function wahaSession()
    {
        return $this->belongsTo(Session::class, 'waha_session_id');
    }
}
