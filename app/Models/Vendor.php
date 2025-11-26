<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Vendor extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'contact',
        'phone',
        'email',
        'address',
    ];

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'contact',
                'phone',
                'email',
                'address',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
