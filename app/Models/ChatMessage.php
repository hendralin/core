<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'thread_id',
        'role',
        'content',
        'meta',
    ];

    protected $casts = [
        'content' => 'array',
        'meta' => 'array',
    ];
}
