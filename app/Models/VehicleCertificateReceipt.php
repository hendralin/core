<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleCertificateReceipt extends Model
{
    use LogsActivity;

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

    protected $dates = [
        'receipt_date',
    ];

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

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
