<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyCommissioner extends Model
{
    protected $table = 'company_commissioners';

    protected $fillable = [
        'kode_emiten',
        'nama',
        'jabatan',
        'independen',
    ];

    protected $casts = [
        'independen' => 'boolean',
    ];

    /**
     * Get the stock company that owns this commissioner
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Check if this is the main commissioner (Komisaris Utama)
     */
    public function isKomisarisUtama(): bool
    {
        return str_contains(strtoupper($this->jabatan), 'UTAMA');
    }
}

