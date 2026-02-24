<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Comment
 */
class CommentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->created_at?->toIso8601String(),
            'user' => $this->when($this->relationLoaded('user') && $this->user, [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'replies' => $this->when(
                $this->relationLoaded('approvedReplies'),
                fn () => CommentResource::collection($this->approvedReplies)->resolve()
            ),
        ];
    }
}
