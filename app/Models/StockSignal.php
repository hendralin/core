<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockSignal extends Model
{
    use LogsActivity;

    protected $fillable = [
        'signal_type',
        'kode_emiten',
        'market_cap',
        'pbv',
        'per',
        'before_date',
        'before_value',
        'before_close',
        'before_volume',
        'hit_date',
        'hit_value',
        'hit_close',
        'hit_volume',
        'after_date',
        'after_value',
        'after_close',
        'after_volume',
        'status',
        'published_at',
        'notes',
        'recommendation',
        'user_id',
    ];

    protected $attributes = [
        'signal_type' => 'value_breakthrough',
    ];

    protected $casts = [
        'market_cap' => 'decimal:2',
        'pbv' => 'decimal:4',
        'per' => 'decimal:4',
        'before_value' => 'decimal:2',
        'before_close' => 'decimal:2',
        'before_volume' => 'integer',
        'hit_value' => 'decimal:2',
        'hit_close' => 'decimal:2',
        'hit_volume' => 'integer',
        'after_value' => 'decimal:2',
        'after_close' => 'decimal:2',
        'after_volume' => 'integer',
        'before_date' => 'date',
        'hit_date' => 'date',
        'after_date' => 'date',
        'published_at' => 'datetime',
    ];

    /**
     * Get the activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'kode_emiten',
                'signal_type',
                'market_cap',
                'pbv',
                'per',
                'before_date',
                'before_value',
                'before_close',
                'before_volume',
                'hit_date',
                'hit_value',
                'hit_close',
                'hit_volume',
                'after_date',
                'after_value',
                'after_close',
                'after_volume',
                'status',
                'published_at',
                'notes',
                'recommendation',
                'user_id',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the user that created this signal
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the stock company information
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Scope to filter by signal type
     */
    public function scopeSignalType($query, string $type)
    {
        return $query->where('signal_type', $type);
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get active signals
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get published signals
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to filter by stock code
     */
    public function scopeKodeEmiten($query, string $kode)
    {
        return $query->where('kode_emiten', $kode);
    }

    /**
     * Check if signal is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if signal is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Publish the signal
     */
    public function publish(): bool
    {
        return $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Mark signal as active
     */
    public function activate(): bool
    {
        return $this->update(['status' => 'active']);
    }

    /**
     * Cancel the signal
     */
    public function cancel(): bool
    {
        return $this->update(['status' => 'cancelled']);
    }

    /**
     * Get formatted market cap
     */
    public function getFormattedMarketCapAttribute(): string
    {
        if (!$this->market_cap) return '-';

        if ($this->market_cap >= 1000000000000) { // Triliun
            return number_format($this->market_cap / 1000000000000, 2) . 'T';
        } elseif ($this->market_cap >= 1000000000) { // Miliar
            return number_format($this->market_cap / 1000000000, 2) . 'B';
        } else {
            return number_format($this->market_cap);
        }
    }

    /**
     * Get formatted before value
     */
    public function getFormattedBeforeValueAttribute(): string
    {
        if (!$this->before_value) return '-';

        if ($this->before_value >= 1000000000000) { // Triliun
            return number_format($this->before_value / 1000000000000, 2) . 'T';
        } elseif ($this->before_value >= 1000000000) { // Miliar
            return number_format($this->before_value / 1000000000, 2) . 'B';
        } elseif ($this->before_value >= 1000000) { // Juta
            return number_format($this->before_value / 1000000, 2) . 'M';
        } elseif ($this->before_value >= 1000) { // Ribu
            return number_format($this->before_value / 1000, 2) . 'K';
        } else {
            return number_format($this->before_value);
        }
    }

    /**
     * Get formatted before volume
     */
    public function getFormattedBeforeVolumeAttribute(): string
    {
        if (!$this->before_volume) return '-';

        if ($this->before_volume >= 1000000000000) { // Triliun
            return number_format($this->before_volume / 1000000000000, 2) . 'T';
        } elseif ($this->before_volume >= 1000000000) { // Miliar
            return number_format($this->before_volume / 1000000000, 2) . 'B';
        } elseif ($this->before_volume >= 1000000) { // Juta
            return number_format($this->before_volume / 1000000, 2) . 'M';
        } else {
            return number_format($this->before_volume);
        }
    }

    /**
     * Get formatted hit value
     */
    public function getFormattedHitValueAttribute(): string
    {
        if (!$this->hit_value) return '-';

        if ($this->hit_value >= 1000000000000) { // Triliun
            return number_format($this->hit_value / 1000000000000, 2) . 'T';
        } elseif ($this->hit_value >= 1000000000) { // Miliar
            return number_format($this->hit_value / 1000000000, 2) . 'B';
        } elseif ($this->hit_value >= 1000000) { // Juta
            return number_format($this->hit_value / 1000000, 2) . 'M';
        } else {
            return number_format($this->hit_value);
        }
    }

    /**
     * Get formatted hit volume
     */
    public function getFormattedHitVolumeAttribute(): string
    {
        if (!$this->hit_volume) return '-';

        if ($this->hit_volume >= 1000000000000) { // Triliun
            return number_format($this->hit_volume / 1000000000000, 2) . 'T';
        } elseif ($this->hit_volume >= 1000000000) { // Miliar
            return number_format($this->hit_volume / 1000000000, 2) . 'B';
        } elseif ($this->hit_volume >= 1000000) { // Juta
            return number_format($this->hit_volume / 1000000, 2) . 'M';
        } else {
            return number_format($this->hit_volume);
        }
    }

    /**
     * Get formatted after value
     */
    public function getFormattedAfterValueAttribute(): string
    {
        if (!$this->after_value) return '-';

        if ($this->after_value >= 1000000000000) { // Triliun
            return number_format($this->after_value / 1000000000000, 2) . 'T';
        } elseif ($this->after_value >= 1000000000) { // Miliar
            return number_format($this->after_value / 1000000000, 2) . 'B';
        } elseif ($this->after_value >= 1000000) { // Juta
            return number_format($this->after_value / 1000000, 2) . 'M';
        } else {
            return number_format($this->after_value);
        }
    }

    /**
     * Get formatted after volume
     */
    public function getFormattedAfterVolumeAttribute(): string
    {
        if (!$this->after_volume) return '-';

        if ($this->before_volume >= 1000000000000) { // Triliun
            return number_format($this->after_volume / 1000000000000, 2) . 'T';
        } elseif ($this->after_volume >= 1000000000) { // Miliar
            return number_format($this->after_volume / 1000000000, 2) . 'B';
        } elseif ($this->after_volume >= 1000000) { // Juta
            return number_format($this->after_volume / 1000000, 2) . 'M';
        } else {
            return number_format($this->after_volume);
        }
    }

    /**
     * Get formatted PBV
     */
    public function getFormattedPbvAttribute(): string
    {
        return $this->pbv ? number_format($this->pbv, 2) . 'x' : '-';
    }

    /**
     * Get formatted PER
     */
    public function getFormattedPerAttribute(): string
    {
        return $this->per ? number_format($this->per, 2) . 'x' : '-';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'active' => 'Active',
            'published' => 'Published',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status)
        };
    }
}
