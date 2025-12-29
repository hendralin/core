<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyShareholder extends Model
{
    protected $table = 'company_shareholders';

    protected $fillable = [
        'kode_emiten',
        'nama',
        'kategori',
        'jumlah',
        'persentase',
        'pengendali',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'persentase' => 'decimal:4',
        'pengendali' => 'boolean',
    ];

    /**
     * Get the stock company that owns this shareholder record
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Scope for major shareholders (>5%)
     */
    public function scopeMajor($query)
    {
        return $query->where('kategori', 'Lebih dari 5%');
    }

    /**
     * Scope for controlling shareholders
     */
    public function scopePengendali($query)
    {
        return $query->where('pengendali', true);
    }

    /**
     * Format share amount
     */
    public function getFormattedJumlahAttribute(): string
    {
        return number_format($this->jumlah, 0, ',', '.');
    }

    /**
     * Format percentage
     */
    public function getFormattedPersentaseAttribute(): string
    {
        return number_format($this->persentase, 4, ',', '.') . '%';
    }
}

