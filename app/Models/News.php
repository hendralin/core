<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'idx_news';

    protected $fillable = [
        'item_id',
        'published_date',
        'image_url',
        'locale',
        'title',
        'path_base',
        'path_file',
        'tags',
        'is_headline',
        'summary',
        'contents',
    ];

    protected $casts = [
        'published_date' => 'datetime',
        'is_headline' => 'boolean',
    ];
}
