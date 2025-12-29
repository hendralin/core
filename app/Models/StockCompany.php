<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StockCompany extends Model
{
    use LogsActivity;

    protected $table = 'stock_companies';
    protected $primaryKey = 'id';

    /**
     * Get route key for model binding
     */
    public function getRouteKeyName(): string
    {
        return 'kode_emiten';
    }

    protected $fillable = [
        'source_id',
        'data_id',
        'kode_emiten',
        'nama_emiten',
        'alamat',
        'bae',
        'divisi',
        'kode_divisi',
        'jenis_emiten',
        'kegiatan_usaha_utama',
        'efek_emiten_eba',
        'efek_emiten_etf',
        'efek_emiten_obligasi',
        'efek_emiten_saham',
        'efek_emiten_spei',
        'sektor',
        'sub_sektor',
        'industri',
        'sub_industri',
        'email',
        'telepon',
        'fax',
        'website',
        'npkp',
        'npwp',
        'papan_pencatatan',
        'tanggal_pencatatan',
        'status',
        'logo',
    ];

    protected $casts = [
        'efek_emiten_eba' => 'boolean',
        'efek_emiten_etf' => 'boolean',
        'efek_emiten_obligasi' => 'boolean',
        'efek_emiten_saham' => 'boolean',
        'efek_emiten_spei' => 'boolean',
        'tanggal_pencatatan' => 'date',
        'status' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'kode_emiten',
                'nama_emiten',
                'sektor',
                'sub_sektor',
                'industri',
                'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Scope to filter by sector
     */
    public function scopeSektor($query, string $sektor)
    {
        return $query->where('sektor', $sektor);
    }

    /**
     * Scope to filter by industry
     */
    public function scopeIndustri($query, string $industri)
    {
        return $query->where('industri', $industri);
    }

    /**
     * Scope to filter only stocks (saham)
     */
    public function scopeSaham($query)
    {
        return $query->where('efek_emiten_saham', true);
    }

    /**
     * Scope to filter by listing board
     */
    public function scopePapan($query, string $papan)
    {
        return $query->where('papan_pencatatan', $papan);
    }

    /**
     * Scope to filter active companies
     */
    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Get full logo URL from IDX
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo)) {
            return null;
        }

        return 'https://www.idx.co.id' . $this->logo;
    }

    /**
     * Get full website URL
     */
    public function getWebsiteUrlAttribute(): ?string
    {
        if (empty($this->website)) {
            return null;
        }

        $website = $this->website;
        if (!str_starts_with($website, 'http://') && !str_starts_with($website, 'https://')) {
            $website = 'https://' . $website;
        }

        return $website;
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the financial ratios for this company
     */
    public function financialRatios(): HasMany
    {
        return $this->hasMany(FinancialRatio::class, 'code', 'kode_emiten');
    }

    /**
     * Get the secretaries for this company
     */
    public function secretaries(): HasMany
    {
        return $this->hasMany(CompanySecretary::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Get the directors for this company
     */
    public function directors(): HasMany
    {
        return $this->hasMany(CompanyDirector::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Get the commissioners for this company
     */
    public function commissioners(): HasMany
    {
        return $this->hasMany(CompanyCommissioner::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Get the audit committee members for this company
     */
    public function auditCommittees(): HasMany
    {
        return $this->hasMany(CompanyAuditCommittee::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Get the shareholders for this company
     */
    public function shareholders(): HasMany
    {
        return $this->hasMany(CompanyShareholder::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Get the subsidiaries for this company
     */
    public function subsidiaries(): HasMany
    {
        return $this->hasMany(CompanySubsidiary::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Get the auditors (KAP) for this company
     */
    public function auditors(): HasMany
    {
        return $this->hasMany(CompanyAuditor::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Get the dividends for this company
     */
    public function dividends(): HasMany
    {
        return $this->hasMany(CompanyDividend::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Get the bonds for this company
     */
    public function bonds(): HasMany
    {
        return $this->hasMany(CompanyBond::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Get the bond details for this company
     */
    public function bondDetails(): HasMany
    {
        return $this->hasMany(CompanyBondDetail::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Get the latest financial ratio
     */
    public function latestFinancialRatio()
    {
        return $this->hasOne(FinancialRatio::class, 'code', 'kode_emiten')->latestOfMany('fs_date');
    }

    /**
     * Get the trading infos for this company
     */
    public function tradingInfos(): HasMany
    {
        return $this->hasMany(TradingInfo::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Get the latest trading info
     */
    public function latestTradingInfo()
    {
        return $this->hasOne(TradingInfo::class, 'kode_emiten', 'kode_emiten')->latestOfMany('date');
    }
}

