<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Config extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'api_url',
        'api_key',
    ];

    /**
     * Get the user that owns the config.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
