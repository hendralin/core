<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'bpkb_number',
        'bpkb_file',
        'warehouse_id',
        'salesman_id',
        'purchase_date',
        'purchase_price',
        'display_price',
        'loan_price',
        'roadside_allowance',
        'selling_date',
        'selling_price',
        'buyer_name',
        'buyer_phone',
        'buyer_address',
        'payment_type',
        'leasing_id',
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
                'bpkb_number',
                'bpkb_file',
                'warehouse_id',
                'salesman_id',
                'purchase_date',
                'purchase_price',
                'display_price',
                'loan_price',
                'roadside_allowance',
                'selling_date',
                'selling_price',
                'buyer_name',
                'buyer_phone',
                'buyer_address',
                'payment_type',
                'leasing_id',
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
     * Get the leasing that owns the vehicle
     */
    public function leasing(): BelongsTo
    {
        return $this->belongsTo(Leasing::class);
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

    /**
     * Get the commissions for the vehicle
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    /**
     * Get the equipment for the vehicle
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(VehicleEquipment::class);
    }

    /**
     * Get the loan calculations for the vehicle
     */
    public function loanCalculations(): HasMany
    {
        return $this->hasMany(LoanCalculation::class);
    }

    /**
     * Get the purchase payments for the vehicle
     */
    public function purchasePayments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class);
    }

    /**
     * Get the payment receipts for the vehicle
     */
    public function paymentReceipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    /**
     * Get the certificate receipts for the vehicle
     */
    public function vehicleCertificateReceipts(): HasMany
    {
        return $this->hasMany(VehicleCertificateReceipt::class);
    }

    /**
     * Get the handovers for the vehicle
     */
    public function vehicleHandovers(): HasMany
    {
        return $this->hasMany(VehicleHandover::class);
    }

    /**
     * Get the equipment for the vehicle
     */
    public function vehicleEquipment(): HasOne
    {
        return $this->hasOne(VehicleEquipment::class);
    }

    /**
     * Get the files for the vehicle
     */
    public function vehicleFiles(): HasMany
    {
        return $this->hasMany(VehicleFile::class);
    }
}
