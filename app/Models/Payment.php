<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cost_id',
        'payment_date',
        'amount',
        'note',
    ];

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'cost_id',
                'payment_date',
                'amount',
                'note'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the cost that owns the payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cost(): BelongsTo
    {
        return $this->belongsTo(Cost::class);
    }
}
