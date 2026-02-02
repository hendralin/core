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
        'employee_salary_component_id',
        'quantity',
        'amount',
        'total_amount',
    ];

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'salary_id',
                'employee_salary_component_id',
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
     * Get the employee salary component that owns the salary detail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employeeSalaryComponent(): BelongsTo
    {
        return $this->belongsTo(EmployeeSalaryComponent::class);
    }
}
