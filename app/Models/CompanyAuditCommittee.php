<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyAuditCommittee extends Model
{
    protected $table = 'company_audit_committees';

    protected $fillable = [
        'kode_emiten',
        'nama',
        'jabatan',
    ];

    /**
     * Get the stock company that owns this audit committee member
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Check if this is the chairman
     */
    public function isKetua(): bool
    {
        return strtoupper($this->jabatan) === 'KETUA';
    }
}

