<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleHandover extends Model
{
    use LogsActivity;

    protected $fillable = [
        'vehicle_id',
        'handover_number',
        'handover_date',
        'handover_from',
        'handover_from_address',
        'handover_to',
        'handover_to_address',
        'transferee',
        'receiving_party',
        'handover_file',
        'created_by',
        'print_count',
        'printed_at',
    ];

    protected $dates = [
        'handover_date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'vehicle_id',
                'handover_number',
                'handover_date',
                'handover_from',
                'handover_from_address',
                'handover_to',
                'handover_to_address',
                'transferee',
                'receiving_party',
                'handover_file',
                'created_by',
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
