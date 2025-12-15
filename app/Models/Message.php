<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Contact;

class Message extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'waha_session_id',
        'template_id',
        'wa_id',
        'group_wa_id',
        'received_number',
        'message',
        'created_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'waha_session_id',
                'template_id',
                'wa_id',
                'group_wa_id',
                'received_number',
                'message',
                'created_by',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function wahaSession()
    {
        return $this->belongsTo(Session::class, 'waha_session_id');
    }

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'received_number', 'wa_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_wa_id', 'group_wa_id');
    }
}
