<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'status',
    ];

    /**
     * Get the options for activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'post_id',
                'user_id',
                'parent_id',
                'content',
                'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the post that the comment belongs to
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user that the comment belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment that the comment belongs to
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the replies for the comment
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user', 'replies');
    }

    /**
     * Get only approved replies (for public display)
     */
    public function approvedReplies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->approved()
            ->with(['user', 'approvedReplies'])
            ->orderBy('created_at');
    }

    /**
     * Scope to filter by approved comments
     */
    public function scopeApproved($query)
    {
        $query->where('status', 'approved');
    }

    /**
     * Scope to filter by top level comments
     */
    public function scopeTopLevel($query)
    {
        $query->whereNull('parent_id');
    }
}
