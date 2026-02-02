<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryComponent extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all of the employee salary components for the SalaryComponent
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeSalaryComponents(): HasMany
    {
        return $this->hasMany(EmployeeSalaryComponent::class);
    }

    /**
     * Get all of the employees for the SalaryComponent through employee_salary_components
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function employees()
    {
        return $this->hasManyThrough(Employee::class, EmployeeSalaryComponent::class, 'salary_component_id', 'id', 'id', 'employee_id');
    }

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'description',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
