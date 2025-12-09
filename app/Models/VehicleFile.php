<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleFile extends Model
{
    use LogsActivity;

    protected $fillable = [
        'vehicle_id',
        'vehicle_file_title_id',
        'file_path',
        'created_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'vehicle_id',
                'vehicle_file_title_id',
                'file_path',
                'created_by',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the vehicle that owns the file
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Get the vehicle file title that owns the file
     */
    public function vehicleFileTitle(): BelongsTo
    {
        return $this->belongsTo(VehicleFileTitle::class, 'vehicle_file_title_id');
    }

    /**
     * Get the user that created the file
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
