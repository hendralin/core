<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use LogsActivity;

    protected $fillable = [
        'commission_date',
        'type',
        'vehicle_id',
        'amount',
        'description',
        'status',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'commission_date',
                'type',
                'vehicle_id',
                'amount',
                'description',
                'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
