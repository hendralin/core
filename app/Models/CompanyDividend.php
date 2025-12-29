<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDividend extends Model
{
    protected $table = 'company_dividends';

    protected $fillable = [
        'kode_emiten',
        'nama',
        'jenis',
        'tahun_buku',
        'total_saham_bonus',
        'cash_dividen_per_saham_mu',
        'cash_dividen_per_saham',
        'cash_dividen_total_mu',
        'cash_dividen_total',
        'tanggal_cum',
        'tanggal_ex_reguler_dan_negosiasi',
        'tanggal_dps',
        'tanggal_pembayaran',
        'rasio1',
        'rasio2',
    ];

    protected $casts = [
        'total_saham_bonus' => 'integer',
        'cash_dividen_per_saham' => 'decimal:2',
        'cash_dividen_total' => 'decimal:2',
        'tanggal_cum' => 'date',
        'tanggal_ex_reguler_dan_negosiasi' => 'date',
        'tanggal_dps' => 'date',
        'tanggal_pembayaran' => 'date',
        'rasio1' => 'integer',
        'rasio2' => 'integer',
    ];

    /**
     * Get the stock company that owns this dividend
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Check if this is a cash dividend
     */
    public function isCashDividend(): bool
    {
        return strtolower($this->jenis) === 'dt';
    }

    /**
     * Check if this is a stock dividend
     */
    public function isStockDividend(): bool
    {
        return strtolower($this->jenis) === 'ds';
    }

    /**
     * Get dividend type label
     */
    public function getJenisLabelAttribute(): string
    {
        return match (strtolower($this->jenis)) {
            'dt' => 'Dividen Tunai',
            'ds' => 'Dividen Saham',
            default => $this->jenis ?? '-'
        };
    }

    /**
     * Get formatted cash dividend per share
     */
    public function getFormattedDpsAttribute(): string
    {
        if (is_null($this->cash_dividen_per_saham)) {
            return '-';
        }

        return $this->cash_dividen_per_saham_mu . ' ' . number_format($this->cash_dividen_per_saham, 2, ',', '.');
    }

    /**
     * Get formatted total cash dividend
     */
    public function getFormattedTotalAttribute(): string
    {
        if (is_null($this->cash_dividen_total)) {
            return '-';
        }

        return $this->cash_dividen_total_mu . ' ' . number_format($this->cash_dividen_total, 0, ',', '.');
    }

    /**
     * Scope for upcoming dividends
     */
    public function scopeUpcoming($query)
    {
        return $query->where('tanggal_pembayaran', '>=', now())->orderBy('tanggal_pembayaran');
    }

    /**
     * Scope for past dividends
     */
    public function scopePast($query)
    {
        return $query->where('tanggal_pembayaran', '<', now())->orderBy('tanggal_pembayaran', 'desc');
    }
}

