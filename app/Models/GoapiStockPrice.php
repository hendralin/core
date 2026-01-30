<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoapiStockPrice extends Model
{

    protected $fillable = [
        'symbol',
        'date',
        'open',
        'high',
        'low',
        'close',
        'volume',
        'change',
        'change_pct',
        'value',
    ];

    protected $casts = [
        'open' => 'decimal:2',
        'high' => 'decimal:2',
        'low' => 'decimal:2',
        'close' => 'decimal:2',
        'volume' => 'decimal:2',
        'change' => 'decimal:2',
        'change_pct' => 'decimal:2',
        'date' => 'datetime',
        'value' => 'decimal:2',
    ];

    public function scopeSymbol($query, string $symbol)
    {
        return $query->where('symbol', $symbol);
    }

    public function scopeDate($query, string $date)
    {
        return $query->where('date', $date);
    }
}
