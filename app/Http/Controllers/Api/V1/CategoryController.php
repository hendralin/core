<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Api\ApiController;

final class CategoryController extends ApiController
{
    /**
     * Get list of categories that have published posts.
     */
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->whereHas('posts', fn ($q) => $q->where('status', 'published'))
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name')
            ->get();

        return $this->success(CategoryResource::collection($categories)->resolve());
    }

    /**
     * Show a single category by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')])
            ->first();

        if (! $category) {
            return $this->notFound('Category not found');
        }

        return $this->success(CategoryResource::make($category)->resolve());
    }
}
