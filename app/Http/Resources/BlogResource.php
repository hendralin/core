<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class BlogResource extends JsonResource
{
    protected bool $includeContent = false;

    /** @var array<int, array<string, mixed>>|null */
    protected ?array $comments = null;

    /**
     * Include full content and comments (for detail view).
     *
     * @param  array<int, array<string, mixed>>  $comments
     */
    public function withDetail(array $comments): static
    {
        $this->includeContent = true;
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'featured_image_url' => $this->featured_image_url,
            'published_at' => $this->published_at?->toIso8601String(),
            'views_count' => $this->views_count ?? 0,
            'user' => $this->when($this->relationLoaded('user') && $this->user, [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'categories' => $this->when($this->relationLoaded('categories'), fn () => $this->categories->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
            ])),
            'tags' => $this->when($this->relationLoaded('tags'), fn () => $this->tags->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
            ])),
        ];

        if ($this->includeContent && $this->comments !== null) {
            $data['content'] = $this->content;
            $data['comments'] = $this->comments;
        }

        return $data;
    }
}
