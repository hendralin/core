<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySubsidiary extends Model
{
    protected $table = 'company_subsidiaries';

    protected $fillable = [
        'kode_emiten',
        'nama',
        'bidang_usaha',
        'lokasi',
        'persentase',
        'jumlah_aset',
        'mata_uang',
        'satuan',
        'status_operasi',
        'tahun_komersil',
    ];

    protected $casts = [
        'persentase' => 'decimal:2',
        'jumlah_aset' => 'decimal:2',
    ];

    /**
     * Get the stock company that owns this subsidiary
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Scope for active subsidiaries
     */
    public function scopeActive($query)
    {
        return $query->where('status_operasi', 'Aktif');
    }

    /**
     * Scope for Indonesian subsidiaries
     */
    public function scopeIndonesia($query)
    {
        return $query->where('lokasi', 'Indonesia');
    }

    /**
     * Check if subsidiary is active
     */
    public function isActive(): bool
    {
        return $this->status_operasi === 'Aktif';
    }

    /**
     * Get formatted asset value
     */
    public function getFormattedAsetAttribute(): string
    {
        if (is_null($this->jumlah_aset)) {
            return '-';
        }

        $satuan = match ($this->satuan) {
            'RIBUAN' => 'K',
            'JUTAAN' => 'M',
            'MILIAR' => 'B',
            default => ''
        };

        return $this->mata_uang . ' ' . number_format($this->jumlah_aset, 0, ',', '.') . $satuan;
    }
}

