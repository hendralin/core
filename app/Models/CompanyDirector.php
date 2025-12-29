<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDirector extends Model
{
    protected $table = 'company_directors';

    protected $fillable = [
        'kode_emiten',
        'nama',
        'jabatan',
        'afiliasi',
    ];

    protected $casts = [
        'afiliasi' => 'boolean',
    ];

    /**
     * Get the stock company that owns this director
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Check if this is the main director (Direktur Utama)
     */
    public function isDirekturUtama(): bool
    {
        return str_contains(strtoupper($this->jabatan), 'UTAMA');
    }
}

