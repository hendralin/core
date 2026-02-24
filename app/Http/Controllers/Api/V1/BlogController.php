<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\BlogResource;
use App\Http\Resources\CommentResource;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreCommentRequest;

final class BlogController extends ApiController
{
    /**
     * Get list of published posts (paginated).
     * Query: page, q (search), category (slug), tags[] (slugs).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Post::query()
            ->where('status', 'published')
            ->with(['user:id,name', 'categories:id,name,slug', 'tags:id,name,slug'])
            ->orderByDesc('published_at');

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('categories.slug', $request->category));
        }

        if ($request->filled('tags') && is_array($request->tags)) {
            $query->whereHas('tags', fn ($q) => $q->whereIn('tags.slug', $request->tags));
        }

        $search = $request->filled('q') ? trim((string) $request->q) : '';
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('excerpt', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $perPage = min(max((int) $request->get('per_page', 15), 1), 50);
        $posts = $query->paginate($perPage);

        $data = [
            'posts' => BlogResource::collection($posts->getCollection())->resolve(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ];

        return $this->success($data);
    }

    /**
     * Show a single published post by slug (with comments).
     */
    public function show(string $slug): JsonResponse
    {
        $post = Post::query()
            ->where('status', 'published')
            ->where('slug', $slug)
            ->with(['user:id,name', 'categories:id,name,slug', 'tags:id,name,slug'])
            ->first();

        if (! $post) {
            return $this->notFound('Post not found');
        }

        // add view count
        $post->increment('views_count');

        $post->refresh();

        $comments = Comment::query()
            ->where('post_id', $post->id)
            ->approved()
            ->topLevel()
            ->with(['user:id,name', 'approvedReplies' => fn ($q) => $q->with('user:id,name')])
            ->orderBy('created_at')
            ->get();

        $data = BlogResource::make($post)
            ->withDetail(CommentResource::collection($comments)->resolve())
            ->resolve();

        return $this->success($data);
    }

    /**
     * Add a comment to a post (authenticated users only).
     */
    public function storeComment(StoreCommentRequest $request, string $slug): JsonResponse
    {
        $post = Post::query()
            ->where('status', 'published')
            ->where('slug', $slug)
            ->first();

        if (! $post) {
            return $this->notFound('Post not found');
        }

        $parentId = $request->validated('parent_id');
        if ($parentId !== null) {
            $parentExists = Comment::query()
                ->where('id', $parentId)
                ->where('post_id', $post->id)
                ->approved()
                ->exists();
            if (! $parentExists) {
                return $this->error('Invalid parent comment.', 422);
            }
        }

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'parent_id' => $request->validated('parent_id'),
            'content' => $request->validated('content'),
            'status' => 'approved',
        ]);

        $comment->load('user:id,name');

        $data = [
            'id' => $comment->id,
            'post_id' => $comment->post_id,
            'parent_id' => $comment->parent_id,
            'content' => $comment->content,
            'status' => $comment->status,
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
            ],
            'created_at' => $comment->created_at?->toIso8601String(),
        ];

        return $this->created($data, 'Comment added successfully');
    }
}
