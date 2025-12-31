<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LqCompany extends Model
{
    protected $table = 'lq_companies';

    protected $fillable = [
        'kode_emiten',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(StockCompany::class, 'kode_emiten', 'kode_emiten');
    }
}
