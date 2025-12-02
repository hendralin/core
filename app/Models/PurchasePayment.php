<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasePayment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'vehicle_id',
        'payment_number',
        'payment_date',
        'amount',
        'description',
        'document',
        'created_by',
        'status',
    ];

    protected $dates = [
        'payment_date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'vehicle_id',
                'payment_number',
                'payment_date',
                'amount',
                'description',
                'document',
                'created_by',
                'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
