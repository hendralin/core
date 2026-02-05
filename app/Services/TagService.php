<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorConcrete;

class TagService
{
    /**
     * Check if a tag can be deleted
     */
    public function canDeleteTag(Tag $tag): array
    {
        $errors = [];

        // Check if tag is still assigned to posts
        if ($tag->posts()->exists()) {
            $postsCount = $tag->posts()->count();
            $errors[] = "Tag is still assigned to {$postsCount} post(s). Please reassign posts first.";
        }

        return [
            'can_delete' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get tags with post counts for index page
     * When $perPage is null, returns all results in a single page (no pagination limit).
     */
    public function getTagsForIndex(?string $search = null, string $sortField = 'name', string $sortDirection = 'asc', ?int $perPage = 10): LengthAwarePaginator
    {
        // Validate sort field
        $validSortFields = ['name', 'posts_count', 'created_at', 'updated_at'];
        $sortField = in_array($sortField, $validSortFields) ? $sortField : 'name';

        $query = Tag::query()
            ->with('posts')
            ->withCount('posts')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('slug', 'like', '%' . $search . '%');
                });
            })
            ->orderBy($sortField, $sortDirection);

        if ($perPage === null) {
            $items = $query->get();
            $total = $items->count();

            return new LengthAwarePaginatorConcrete($items, $total, max(1, $total), 1);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get tag statistics
     */
    public function getTagStatistics(Tag $tag): array
    {
        if (!$tag->exists) {
            throw new \InvalidArgumentException('Tag does not exist');
        }

        return [
            'posts_count' => $tag->posts()->count(),
            'created_at' => $tag->created_at,
            'updated_at' => $tag->updated_at,
        ];
    }

    /**
     * Get tag usage status
     */
    public function getTagUsageStatus(Tag $tag): array
    {
        if (!$tag->exists) {
            throw new \InvalidArgumentException('Tag does not exist');
        }

        $postsCount = $tag->posts()->count();

        $status = 'unused';
        $statusColor = 'gray';

        if ($postsCount > 10) {
            $status = 'popular';
            $statusColor = 'green';
        } elseif ($postsCount > 0) {
            $status = 'active';
            $statusColor = 'blue';
        }

        return [
            'status' => $status,
            'status_color' => $statusColor,
            'is_used' => $postsCount > 0,
            'has_posts' => $postsCount > 0
        ];
    }

    /**
     * Get enhanced tags data for index page
     */
    public function getEnhancedTagsForIndex(?string $search = null, string $sortField = 'name', string $sortDirection = 'asc', ?int $perPage = 10): LengthAwarePaginator
    {
        $tags = $this->getTagsForIndex($search, $sortField, $sortDirection, $perPage);

        // Add additional data to each tag
        $tags->getCollection()->transform(function ($tag) {
            $tag->usage_status = $this->getTagUsageStatus($tag);

            return $tag;
        });

        return $tags;
    }

    /**
     * Format tag posts for display (limit and show more)
     */
    public function formatPostsForDisplay(Tag $tag, int $limit = 3): array
    {
        $posts = $tag->posts()->latest()->take($limit + 1)->get();
        $total = $tag->posts()->count();

        if ($total <= $limit) {
            return [
                'posts' => $posts,
                'has_more' => false,
                'remaining_count' => 0
            ];
        }

        return [
            'posts' => $posts->take($limit),
            'has_more' => true,
            'remaining_count' => $total - $limit
        ];
    }
}
