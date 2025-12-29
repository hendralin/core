<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyBond extends Model
{
    protected $table = 'company_bonds';

    protected $fillable = [
        'source_id',
        'kode_emiten',
        'nama_emisi',
        'isin_code',
        'listing_date',
        'mature_date',
        'rating',
        'nominal',
        'margin',
        'wali_amanat',
    ];

    protected $casts = [
        'listing_date' => 'date',
        'mature_date' => 'date',
        'nominal' => 'decimal:2',
    ];

    /**
     * Get the stock company that owns this bond
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Check if bond is matured
     */
    public function isMatured(): bool
    {
        return $this->mature_date && $this->mature_date->isPast();
    }

    /**
     * Check if bond is active
     */
    public function isActive(): bool
    {
        return $this->mature_date && $this->mature_date->isFuture();
    }

    /**
     * Get formatted nominal value
     */
    public function getFormattedNominalAttribute(): string
    {
        if (is_null($this->nominal)) {
            return '-';
        }

        return 'IDR ' . number_format($this->nominal, 0, ',', '.');
    }

    /**
     * Scope for active bonds
     */
    public function scopeActive($query)
    {
        return $query->where('mature_date', '>', now());
    }

    /**
     * Scope for matured bonds
     */
    public function scopeMatured($query)
    {
        return $query->where('mature_date', '<=', now());
    }
}

