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
        'remaining_loan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'join_date' => 'date',
        'remaining_loan' => 'decimal:2',
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
                'remaining_loan',
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

    /**
     * Get all of the employee loans (loan_type = loan, i.e. pinjaman)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeLoans(): HasMany
    {
        return $this->hasMany(EmployeeLoan::class)->where('loan_type', 'loan');
    }

    /**
     * Get count of vehicles sold by this employee in the given month/year.
     * Used for Sales Executive Officer: employee is linked to salesman via user_id.
     *
     * @param int $month
     * @param int $year
     * @return int
     */
    public function getVehiclesSoldCount(int $month, int $year): int
    {
        if (!$this->user_id) {
            return 0;
        }
        $salesman = \App\Models\Salesman::where('user_id', $this->user_id)->first();
        if (!$salesman) {
            return 0;
        }
        return \App\Models\Vehicle::where('salesman_id', $salesman->id)
            ->where('status', 0)
            ->whereMonth('selling_date', $month)
            ->whereYear('selling_date', $year)
            ->whereNotNull('selling_date')
            ->count();
    }

    /**
     * Check if employee position is Sales Executive Officer (for commission by vehicles sold).
     *
     * @return bool
     */
    public function isSalesExecutiveOfficer(): bool
    {
        $name = $this->position?->name ?? '';
        return stripos($name, 'Sales Executive') !== false || stripos($name, 'SEO') !== false;
    }
}
