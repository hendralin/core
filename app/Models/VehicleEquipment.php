<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleEquipment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'type',
        'vehicle_id',
        'stnk_asli',
        'kunci_roda',
        'ban_serep',
        'kunci_serep',
        'dongkrak',
    ];

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'type',
                'vehicle_id',
                'stnk_asli',
                'kunci_roda',
                'ban_serep',
                'kunci_serep',
                'dongkrak',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the vehicle that owns the vehicle equipment
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
