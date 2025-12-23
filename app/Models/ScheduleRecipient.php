<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScheduleRecipient extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'schedule_id',
        'recipient_type',
        'contact_id',
        'group_id',
        'wa_id',
        'group_wa_id',
        'received_number',
    ];

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'schedule_id',
                'recipient_type',
                'contact_id',
                'group_id',
                'wa_id',
                'group_wa_id',
                'received_number',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the schedule that owns this recipient
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the contact (if recipient_type is 'contact')
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the group (if recipient_type is 'group')
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
