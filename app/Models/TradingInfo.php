<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradingInfo extends Model
{
    protected $table = 'trading_infos';

    protected $fillable = [
        'kode_emiten',
        'date',
        'previous',
        'open_price',
        'first_trade',
        'high',
        'low',
        'close',
        'change',
        'volume',
        'value',
        'frequency',
        'index_individual',
        'offer',
        'offer_volume',
        'bid',
        'bid_volume',
        'listed_shares',
        'tradeble_shares',
        'weight_for_index',
        'foreign_sell',
        'foreign_buy',
        'delisting_date',
        'non_regular_volume',
        'non_regular_value',
        'non_regular_frequency',
    ];

    protected $casts = [
        'date' => 'date',
        'delisting_date' => 'date',
        'previous' => 'decimal:2',
        'open_price' => 'decimal:2',
        'first_trade' => 'decimal:2',
        'high' => 'decimal:2',
        'low' => 'decimal:2',
        'close' => 'decimal:2',
        'change' => 'decimal:2',
        'volume' => 'decimal:2',
        'value' => 'decimal:2',
        'frequency' => 'decimal:2',
        'index_individual' => 'decimal:4',
        'offer' => 'decimal:2',
        'offer_volume' => 'decimal:2',
        'bid' => 'decimal:2',
        'bid_volume' => 'decimal:2',
        'listed_shares' => 'decimal:2',
        'tradeble_shares' => 'decimal:2',
        'weight_for_index' => 'decimal:2',
        'foreign_sell' => 'decimal:2',
        'foreign_buy' => 'decimal:2',
        'non_regular_volume' => 'decimal:2',
        'non_regular_value' => 'decimal:2',
        'non_regular_frequency' => 'decimal:2',
    ];

    /**
     * Get the stock company
     */
    public function stockCompany(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }

    /**
     * Scope by stock code
     */
    public function scopeCode($query, string $code)
    {
        return $query->where('kode_emiten', $code);
    }

    /**
     * Scope by date range
     */
    public function scopeDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for latest trading date
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('date', 'desc');
    }

    /**
     * Get price change percentage
     */
    public function getChangePercentAttribute(): ?float
    {
        if (is_null($this->previous) || (float) $this->previous == 0) {
            return null;
        }

        return ((float) $this->change / (float) $this->previous) * 100;
    }

    /**
     * Get formatted change percentage
     */
    public function getFormattedChangePercentAttribute(): string
    {
        $percent = $this->change_percent;
        if (is_null($percent)) {
            return '-';
        }

        $sign = $percent >= 0 ? '+' : '';
        return $sign . number_format($percent, 2, ',', '.') . '%';
    }

    /**
     * Get net foreign flow (buy - sell)
     */
    public function getNetForeignAttribute(): ?float
    {
        if (is_null($this->foreign_buy) || is_null($this->foreign_sell)) {
            return null;
        }

        return $this->foreign_buy - $this->foreign_sell;
    }

    /**
     * Check if price went up
     */
    public function isUp(): bool
    {
        return $this->change > 0;
    }

    /**
     * Check if price went down
     */
    public function isDown(): bool
    {
        return $this->change < 0;
    }

    /**
     * Format large number
     */
    public function formatNumber($value, int $decimals = 0): string
    {
        if (is_null($value)) {
            return '-';
        }

        return number_format($value, $decimals, ',', '.');
    }

    /**
     * Format value in billions
     */
    public function formatBillion($value): string
    {
        if (is_null($value)) {
            return '-';
        }

        return number_format($value / 1000000000, 2, ',', '.') . ' B';
    }

    /**
     * Format value in millions
     */
    public function formatMillion($value): string
    {
        if (is_null($value)) {
            return '-';
        }

        return number_format($value / 1000000, 2, ',', '.') . ' M';
    }
}

