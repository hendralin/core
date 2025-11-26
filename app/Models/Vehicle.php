<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use LogsActivity;

    protected $fillable = [
        'police_number',
        'brand_id',
        'type_id',
        'category_id',
        'vehicle_model_id',
        'year',
        'cylinder_capacity',
        'chassis_number',
        'engine_number',
        'color',
        'fuel_type',
        'kilometer',
        'vehicle_registration_date',
        'vehicle_registration_expiry_date',
        'file_stnk',
        'warehouse_id',
        'salesman_id',
        'purchase_date',
        'purchase_price',
        'display_price',
        'selling_date',
        'selling_price',
        'buyer_name',
        'buyer_phone',
        'buyer_address',
        'receipt_number',
        'status',
        'description',
    ];

    protected $dates = [
        'vehicle_registration_date',
        'vehicle_registration_expiry_date',
        'purchase_date',
        'selling_date',
    ];

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'police_number',
                'brand_id',
                'type_id',
                'category_id',
                'vehicle_model_id',
                'year',
                'cylinder_capacity',
                'chassis_number',
                'engine_number',
                'color',
                'fuel_type',
                'kilometer',
                'vehicle_registration_date',
                'vehicle_registration_expiry_date',
                'file_stnk',
                'warehouse_id',
                'salesman_id',
                'purchase_date',
                'purchase_price',
                'display_price',
                'selling_date',
                'selling_price',
                'buyer_name',
                'buyer_phone',
                'buyer_address',
                'receipt_number',
                'status',
                'description',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the brand that owns the vehicle
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the type that owns the vehicle
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Get the category that owns the vehicle
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the model that owns the vehicle
     */
    public function vehicle_model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    /**
     * Get the warehouse that owns the vehicle
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the salesman that owns the vehicle
     */
    public function salesman(): BelongsTo
    {
        return $this->belongsTo(Salesman::class);
    }

    /**
     * Get the images for the vehicle
     */
    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    /**
     * Get the costs for the vehicle
     */
    public function costs(): HasMany
    {
        return $this->hasMany(Cost::class);
    }
}
