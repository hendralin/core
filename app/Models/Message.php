<?php

namespace App\Models;

use App\Models\Contact;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'waha_session_id',
        'template_id',
        'wa_id',
        'group_wa_id',
        'received_number',
        'message',
        'status',
        'error_message',
        'scheduled_at',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Get the options for activity logging
     */
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
                'status',
                'error_message',
                'scheduled_at',
                'created_by',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /*
    * Get the session that the message belongs to
    */
    public function wahaSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'waha_session_id');
    }

    /*
    * Get the template that the message belongs to
    */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    /*
    * Get the user that created the message
    */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    * Get the contact that received the message
    */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'received_number', 'wa_id');
    }

    /*
    * Get the group that received the message
    */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_wa_id', 'group_wa_id');
    }
}
