<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Api\ApiController;

final class TagController extends ApiController
{
    /**
     * Get list of tags that have published posts.
     */
    public function index(): JsonResponse
    {
        $tags = Tag::query()
            ->whereHas('posts', fn ($q) => $q->where('status', 'published'))
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name')
            ->get();

        return $this->success(TagResource::collection($tags)->resolve());
    }

    /**
     * Show a single tag by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $tag = Tag::query()
            ->where('slug', $slug)
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')])
            ->first();

        if (! $tag) {
            return $this->notFound('Tag not found');
        }

        return $this->success(TagResource::make($tag)->resolve());
    }
}
