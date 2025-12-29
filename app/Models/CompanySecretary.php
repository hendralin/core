<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySecretary extends Model
{
    protected $table = 'company_secretaries';

    protected $fillable = [
        'kode_emiten',
        'nama',
        'telepon',
        'email',
        'fax',
        'hp',
        'website',
    ];

    /**
     * Get the stock company that owns this secretary
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }
}

