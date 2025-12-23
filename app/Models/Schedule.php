<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
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
        'description',
        'message',
        'wa_id',
        'group_wa_id',
        'received_number',
        'frequency',
        'time',
        'day_of_week',
        'day_of_month',
        'is_active',
        'last_run',
        'next_run',
        'options',
        'usage_count',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'time' => 'datetime:H:i',
        'last_run' => 'datetime',
        'next_run' => 'datetime',
        'options' => 'array',
        'day_of_week' => 'integer',
        'day_of_month' => 'integer',
        'usage_count' => 'integer',
    ];

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'waha_session_id',
                'name',
                'description',
                'message',
                'wa_id',
                'group_wa_id',
                'received_number',
                'frequency',
                'time',
                'day_of_week',
                'day_of_month',
                'is_active',
                'last_run',
                'next_run',
                'options',
                'usage_count',
                'created_by',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Calculate the next run time based on frequency
     * Uses the user's timezone for calculation
     */
    public function calculateNextRun(): Carbon
    {
        // Get user's timezone, fallback to app timezone
        $userTimezone = $this->createdBy->timezone ?? config('app.timezone', 'UTC');

        // Get the time string (HH:mm format)
        $timeString = $this->time ? $this->time->format('H:i') : '09:00';
        list($hour, $minute) = explode(':', $timeString);

        // Get current time in user's timezone
        $now = Carbon::now($userTimezone);

        switch ($this->frequency) {
            case 'daily':
                $nextRun = $now->copy()->setTime((int)$hour, (int)$minute, 0);
                if ($nextRun->isPast()) {
                    $nextRun = $nextRun->addDay();
                }
                break;

            case 'weekly':
                $dayOfWeek = $this->day_of_week ?? 1; // Monday default (1 = Monday in Carbon)
                $nextRun = $now->copy()->next($dayOfWeek)->setTime((int)$hour, (int)$minute, 0);
                if ($nextRun->isPast()) {
                    $nextRun = $nextRun->addWeek();
                }
                break;

            case 'monthly':
                $dayOfMonth = min($this->day_of_month ?? 1, 28); // Max 28 to avoid issues
                $nextRun = $now->copy()->setDay($dayOfMonth)->setTime((int)$hour, (int)$minute, 0);
                if ($nextRun->isPast()) {
                    $nextRun = $nextRun->addMonth();
                }
                break;

            default:
                $nextRun = $now->copy()->addDay();
        }

        // Convert to UTC for storage
        return $nextRun->utc();
    }

    /**
     * Update the last run and calculate the next run
     */
    public function markAsRun(): void
    {
        $this->update([
            'last_run' => Carbon::now(),
            'next_run' => $this->calculateNextRun(),
            'usage_count' => $this->usage_count + 1,
        ]);
    }

    /**
     * Check if the schedule should run now
     * Compares UTC times for consistency
     */
    public function shouldRunNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->next_run) {
            return true; // First time run
        }

        // Compare in UTC for consistency
        return Carbon::now('UTC')->gte($this->next_run);
    }

    /*
    * Get the session that the schedule belongs to
    */
    public function wahaSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'waha_session_id');
    }

    /*
    * Get the group that the schedule belongs to (by group_wa_id)
    */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_wa_id', 'group_wa_id');
    }

    /*
    * Get the contact that the schedule belongs to (by received_number)
    */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'received_number', 'wa_id');
    }

    /*
    * Get the user that created the schedule
    */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    * Get all recipients for this schedule
    */
    public function recipients(): HasMany
    {
        return $this->hasMany(ScheduleRecipient::class);
    }
}
