<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleCertificateReceipt extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vehicle_id',
        'certificate_receipt_number',
        'in_the_name_of',
        'original_invoice_name',
        'photocopy_id_card_name',
        'receipt_form',
        'nik',
        'form_a',
        'release_of_title_letter',
        'others',
        'receipt_date',
        'transferee',
        'receiving_party',
        'receipt_file',
        'created_by',
        'print_count',
        'printed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $dates = [
        'receipt_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'vehicle_id' => 'integer',
    ];

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'vehicle_id',
                'certificate_receipt_number',
                'in_the_name_of',
                'original_invoice_name',
                'photocopy_id_card_name',
                'receipt_form',
                'nik',
                'form_a',
                'release_of_title_letter',
                'others',
                'receipt_date',
                'transferee',
                'receiving_party',
                'receipt_file',
                'created_by',
                'print_count',
                'printed_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the vehicle that owns the certificate receipt
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
