<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyAuditor extends Model
{
    protected $table = 'company_auditors';

    protected $fillable = [
        'kode_emiten',
        'nama',
        'kap',
        'signing_partner',
        'tahun_buku',
        'tanggal_tahun_buku',
        'akhir_periode',
        'tgl_opini',
    ];

    protected $casts = [
        'tahun_buku' => 'integer',
        'tanggal_tahun_buku' => 'date',
        'akhir_periode' => 'date',
        'tgl_opini' => 'date',
    ];

    /**
     * Get the stock company that owns this auditor record
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Scope to get latest auditor
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('tahun_buku', 'desc');
    }
}

