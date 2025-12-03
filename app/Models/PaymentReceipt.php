<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceipt extends Model
{
    use LogsActivity;

    protected $fillable = [
        'vehicle_id',
        'payment_number',
        'payment_date',
        'amount',
        'description',
        'remaining_balance',
        'must_be_settled_date',
        'document',
        'created_by',
        'status',
        'print_count',
        'printed_at',
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
                'remaining_balance',
                'must_be_settled_date',
                'created_by',
                'status',
                'print_count',
                'printed_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

}
