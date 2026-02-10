<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryDetail extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'salary_id',
        'salary_component_id',
        'vehicle_id',
        'quantity',
        'amount',
        'total_amount',
    ];

    protected $casts = [
        'vehicle_id' => 'integer',
        'quantity' => 'integer',
        'amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'salary_id',
                'salary_component_id',
                'vehicle_id',
                'quantity',
                'amount',
                'total_amount',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the salary that owns the salary detail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salary(): BelongsTo
    {
        return $this->belongsTo(Salary::class);
    }

    /**
     * Get the salary component that owns the salary detail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salaryComponent(): BelongsTo
    {
        return $this->belongsTo(SalaryComponent::class);
    }

    /**
     * Get the vehicle that owns the salary detail (optional)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
