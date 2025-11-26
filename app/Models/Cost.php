<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cost extends Model
{
    use LogsActivity;

    protected $fillable = [
        'cost_type',
        'vehicle_id',
        'cost_date',
        'vendor_id',
        'description',
        'total_price',
        'document',
        'created_by',
        'status',
    ];

    protected $dates = [
        'cost_date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'cost_type',
                'vehicle_id',
                'cost_date',
                'vendor_id',
                'description',
                'total_price',
                'document',
                'created_by',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
