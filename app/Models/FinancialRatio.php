<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialRatio extends Model
{
    protected $table = 'financial_ratios';

    protected $fillable = [
        'code',
        'stock_name',
        'sharia',
        'sector',
        'sub_sector',
        'industry',
        'sub_industry',
        'sector_code',
        'sub_sector_code',
        'industry_code',
        'sub_industry_code',
        'sub_name',
        'sub_code',
        'fs_date',
        'fiscal_year_end',
        'assets',
        'liabilities',
        'equity',
        'sales',
        'ebt',
        'profit_period',
        'profit_attr_owner',
        'eps',
        'book_value',
        'per',
        'price_bv',
        'de_ratio',
        'roa',
        'roe',
        'npm',
        'audit',
        'opini',
    ];

    protected $casts = [
        'fs_date' => 'date',
        'assets' => 'decimal:2',
        'liabilities' => 'decimal:2',
        'equity' => 'decimal:2',
        'sales' => 'decimal:2',
        'ebt' => 'decimal:2',
        'profit_period' => 'decimal:2',
        'profit_attr_owner' => 'decimal:2',
        'eps' => 'decimal:2',
        'book_value' => 'decimal:2',
        'per' => 'decimal:4',
        'price_bv' => 'decimal:4',
        'de_ratio' => 'decimal:4',
        'roa' => 'decimal:4',
        'roe' => 'decimal:4',
        'npm' => 'decimal:4',
    ];

    /**
     * Get the stock company that owns this financial ratio
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'code', 'kode_emiten');
    }

    /**
     * Scope to filter by stock code
     */
    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Scope to filter by sector
     */
    public function scopeSektor($query, string $sector)
    {
        return $query->where('sector', $sector);
    }

    /**
     * Scope to filter by industry
     */
    public function scopeIndustri($query, string $industry)
    {
        return $query->where('industry', $industry);
    }

    /**
     * Scope to filter sharia stocks only
     */
    public function scopeSharia($query)
    {
        return $query->where('sharia', 'S');
    }

    /**
     * Scope to filter audited reports only
     */
    public function scopeAudited($query)
    {
        return $query->where('audit', 'A');
    }

    /**
     * Scope to filter by financial statement date
     */
    public function scopeFsDate($query, string $date)
    {
        return $query->where('fs_date', $date);
    }

    /**
     * Scope to get latest report for each stock
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('fs_date', 'desc');
    }

    /**
     * Check if this is a sharia-compliant stock
     */
    public function isSharia(): bool
    {
        return $this->sharia === 'S';
    }

    /**
     * Check if this report is audited
     */
    public function isAudited(): bool
    {
        return $this->audit === 'A';
    }

    /**
     * Get audit status label
     */
    public function getAuditLabelAttribute(): string
    {
        return match($this->audit) {
            'A' => 'Audited',
            'U' => 'Unaudited',
            default => 'Unknown'
        };
    }

    /**
     * Get audit opinion label
     */
    public function getOpiniLabelAttribute(): string
    {
        return match($this->opini) {
            'WTM' => 'Wajar Tanpa Modifikasi',
            'WTP' => 'Wajar Tanpa Pengecualian',
            'WDP' => 'Wajar Dengan Pengecualian',
            'TW' => 'Tidak Wajar',
            'TMP' => 'Tidak Memberikan Pendapat',
            default => $this->opini ?? '-'
        };
    }

    /**
     * Format currency value (in billions)
     */
    public function formatBillion($value): string
    {
        if (is_null($value)) {
            return '-';
        }
        return number_format($value, 2, ',', '.') . ' B';
    }

    /**
     * Format percentage value
     */
    public function formatPercent($value): string
    {
        if (is_null($value)) {
            return '-';
        }
        return number_format($value, 2, ',', '.') . '%';
    }

    /**
     * Format ratio value
     */
    public function formatRatio($value): string
    {
        if (is_null($value)) {
            return '-';
        }
        return number_format($value, 2, ',', '.');
    }
}

