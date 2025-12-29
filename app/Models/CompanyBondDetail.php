<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyBondDetail extends Model
{
    protected $table = 'company_bond_details';

    protected $fillable = [
        'source_id',
        'kode_emiten',
        'nama_seri',
        'amortisasi_value',
        'sinking_fund',
        'coupon_detail',
        'coupon_payment_detail',
        'mature_date',
    ];

    protected $casts = [
        'coupon_payment_detail' => 'date',
        'mature_date' => 'date',
    ];

    /**
     * Get the stock company that owns this bond detail
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }
}

