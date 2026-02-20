<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PostView extends Model
{
    use LogsActivity;

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'post_id',
                'ip_address',
                'user_agent',
                'user_id',
                'viewed_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'post_id',
        'ip_address',
        'user_agent',
        'user_id',
        'viewed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'viewed_at' => 'datetime'
    ];

    /**
     * Get the post that the view belongs to
     */
    public function post(){
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user that the view belongs to
     */
    public function user(){
        return $this->belongsTo(User::class);
    }
}
