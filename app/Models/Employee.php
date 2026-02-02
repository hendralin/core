<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'join_date',
        'position_id',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'join_date' => 'date',
    ];

    /**
     * Set the status attribute
     */
    public function setStatusAttribute($value)
    {
        // Convert to integer first, then to string for MySQL enum compatibility
        $intValue = (int) $value;
        $this->attributes['status'] = (string) $intValue;
    }

    /**
     * Get the options for activity logging
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id',
                'name',
                'join_date',
                'position_id',
                'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the user that owns the employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the position that owns the employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get all of the employee salary components for the employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeSalaryComponents(): HasMany
    {
        return $this->hasMany(EmployeeSalaryComponent::class);
    }

    /**
     * Get all of the salaries for the employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }
}
