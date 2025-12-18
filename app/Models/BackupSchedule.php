<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BackupSchedule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'frequency',
        'time',
        'day_of_week',
        'day_of_month',
        'is_active',
        'last_run',
        'next_run',
        'description',
        'options',
        'encryption_enabled',
        'encryption_password'
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
        'encryption_enabled' => 'boolean'
    ];

    /**
     * Calculate the next run time based on frequency
     */
    public function calculateNextRun(): Carbon
    {
        $now = Carbon::now();
        $time = $this->time ? Carbon::createFromTimeString($this->time->format('H:i')) : Carbon::createFromTimeString('02:00');

        switch ($this->frequency) {
            case 'daily':
                $nextRun = Carbon::today()->setTimeFrom($time);
                if ($nextRun->isPast()) {
                    $nextRun = $nextRun->addDay();
                }
                break;

            case 'weekly':
                $dayOfWeek = $this->day_of_week ?? 1; // Monday default
                $nextRun = Carbon::today()->next($dayOfWeek)->setTimeFrom($time);
                if ($nextRun->isPast()) {
                    $nextRun = $nextRun->addWeek();
                }
                break;

            case 'monthly':
                $dayOfMonth = min($this->day_of_month ?? 1, 28); // Max 28 to avoid issues
                $nextRun = Carbon::create(null, null, $dayOfMonth)->setTimeFrom($time);

                if ($nextRun->isPast()) {
                    $nextRun = $nextRun->addMonth();
                }
                break;

            default:
                $nextRun = $now->addDay();
        }

        return $nextRun;
    }

    /**
     * Update the last run and calculate the next run
     */
    public function markAsRun(): void
    {
        $this->update([
            'last_run' => Carbon::now(),
            'next_run' => $this->calculateNextRun()
        ]);
    }

    /**
     * Check if the schedule should run now
     */
    public function shouldRunNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->next_run) {
            return true; // First time run
        }

        return Carbon::now()->gte($this->next_run);
    }

    /**
     * Get the frequency options for forms
     */
    public static function getFrequencyOptions(): array
    {
        return [
            'daily' => 'Harian',
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan'
        ];
    }

    /**
     * Get the day of week options
     */
    public static function getDayOfWeekOptions(): array
    {
        return [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu'
        ];
    }

    /**
     * Get the day of month options
     */
    public static function getDayOfMonthOptions(): array
    {
        $options = [];
        for ($i = 1; $i <= 28; $i++) {
            $options[$i] = $i;
        }
        return $options;
    }
}
