<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService
{
    /**
     * Check if a category can be deleted
     */
    public function canDeleteCategory(Category $category): array
    {
        $errors = [];

        // Check if category is still assigned to posts
        if ($category->posts()->exists()) {
            $postsCount = $category->posts()->count();
            $errors[] = "Category is still assigned to {$postsCount} post(s). Please reassign posts first.";
        }

        return [
            'can_delete' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get categories with post counts for index page
     */
    public function getCategoriesForIndex(?string $search = null, string $sortField = 'name', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        // Validate sort field
        $validSortFields = ['name', 'posts_count', 'created_at', 'updated_at'];
        $sortField = in_array($sortField, $validSortFields) ? $sortField : 'name';

        return Category::query()
            ->with('posts')
            ->withCount('posts')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('description', 'like', '%' . $search . '%')
                          ->orWhere('slug', 'like', '%' . $search . '%');
                });
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(10);
    }

    /**
     * Get category statistics
     */
    public function getCategoryStatistics(Category $category): array
    {
        if (!$category->exists) {
            throw new \InvalidArgumentException('Category does not exist');
        }

        return [
            'posts_count' => $category->posts()->count(),
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];
    }

    /**
     * Get category usage status
     */
    public function getCategoryUsageStatus(Category $category): array
    {
        if (!$category->exists) {
            throw new \InvalidArgumentException('Category does not exist');
        }

        $postsCount = $category->posts()->count();

        $status = 'empty';
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
     * Get enhanced categories data for index page
     */
    public function getEnhancedCategoriesForIndex(?string $search = null, string $sortField = 'name', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        $categories = $this->getCategoriesForIndex($search, $sortField, $sortDirection);

        // Add additional data to each category
        $categories->getCollection()->transform(function ($category) {
            $category->usage_status = $this->getCategoryUsageStatus($category);

            return $category;
        });

        return $categories;
    }

    /**
     * Get available colors for categories (Palette Mode)
     */
    public function getAvailableColors(): array
    {
        return [
            // Primary Colors
            'blue' => ['name' => 'Blue', 'hex' => '#3B82F6', 'category' => 'Primary'],
            'green' => ['name' => 'Green', 'hex' => '#10B981', 'category' => 'Primary'],
            'red' => ['name' => 'Red', 'hex' => '#EF4444', 'category' => 'Primary'],
            'yellow' => ['name' => 'Yellow', 'hex' => '#F59E0B', 'category' => 'Primary'],

            // Extended Palette
            'purple' => ['name' => 'Purple', 'hex' => '#8B5CF6', 'category' => 'Extended'],
            'pink' => ['name' => 'Pink', 'hex' => '#EC4899', 'category' => 'Extended'],
            'indigo' => ['name' => 'Indigo', 'hex' => '#6366F1', 'category' => 'Extended'],
            'cyan' => ['name' => 'Cyan', 'hex' => '#06B6D4', 'category' => 'Extended'],
            'orange' => ['name' => 'Orange', 'hex' => '#F97316', 'category' => 'Extended'],
            'teal' => ['name' => 'Teal', 'hex' => '#14B8A6', 'category' => 'Extended'],

            // Neutral Colors
            'gray' => ['name' => 'Gray', 'hex' => '#6B7280', 'category' => 'Neutral'],
            'slate' => ['name' => 'Slate', 'hex' => '#64748B', 'category' => 'Neutral'],
            'zinc' => ['name' => 'Zinc', 'hex' => '#71717A', 'category' => 'Neutral'],
            'stone' => ['name' => 'Stone', 'hex' => '#78716C', 'category' => 'Neutral'],
        ];
    }

    /**
     * Get colors grouped by category for palette display
     */
    public function getColorsGroupedByCategory(): array
    {
        $colors = $this->getAvailableColors();
        $grouped = [];

        foreach ($colors as $key => $color) {
            $category = $color['category'] ?? 'Other';
            $grouped[$category][$key] = $color;
        }

        return $grouped;
    }

    /**
     * Get color data by color key (supports both palette and custom hex)
     */
    public function getColorData(string $colorKey): ?array
    {
        $colors = $this->getAvailableColors();

        // Check if it's a predefined palette color
        if (isset($colors[$colorKey])) {
            return $colors[$colorKey];
        }

        // Check if it's a valid hex color
        if (preg_match('/^#[a-fA-F0-9]{6}$/', $colorKey)) {
            return [
                'name' => 'Custom',
                'hex' => $colorKey,
                'category' => 'Custom',
                'is_custom' => true
            ];
        }

        return null;
    }

    /**
     * Format category posts for display (limit and show more)
     */
    public function formatPostsForDisplay(Category $category, int $limit = 3): array
    {
        $posts = $category->posts()->latest()->take($limit + 1)->get();
        $total = $category->posts()->count();

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
