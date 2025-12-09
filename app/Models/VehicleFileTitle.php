<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleFileTitle extends Model
{
    use LogsActivity;

    protected $fillable = [
        'title',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function vehicleFiles(): HasMany
    {
        return $this->hasMany(VehicleFile::class);
    }
}
