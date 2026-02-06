<?php

namespace App\Livewire\PublicBlog;

use App\Models\Comment;
use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Illuminate\Contracts\View\View;

class BlogShow extends Component
{
    #[Locked]
    public Post $post;

    #[Validate('required|string|max:5000')]
    public string $commentContent = '';

    public ?int $replyingToId = null;

    public function mount(Post $post): void
    {
        if ($post->status !== 'published') {
            abort(404);
        }

        $this->post = $post->load(['user', 'categories', 'tags']);
    }

    public function addComment(): void
    {
        if (! auth()->check()) {
            return;
        }

        $this->validate();

        Comment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'parent_id' => null,
            'content' => $this->commentContent,
            'status' => 'approved',
        ]);

        $this->reset('commentContent', 'replyingToId');
        $this->dispatch('comment-added');
    }

    public function setReplyingTo(?int $commentId): void
    {
        if (! auth()->check()) {
            return;
        }

        $this->replyingToId = $commentId;
        if ($commentId === null) {
            $this->commentContent = '';
        }
    }

    public function addReply(): void
    {
        if (! auth()->check() || ! $this->replyingToId) {
            return;
        }

        $parent = Comment::where('post_id', $this->post->id)
            ->approved()
            ->find($this->replyingToId);

        if (! $parent) {
            $this->reset('replyingToId', 'commentContent');
            return;
        }

        $this->validate();

        Comment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'parent_id' => $this->replyingToId,
            'content' => $this->commentContent,
            'status' => 'approved',
        ]);

        $this->reset('commentContent', 'replyingToId');
        $this->dispatch('comment-added');
    }

    public function cancelReply(): void
    {
        $this->reset('replyingToId', 'commentContent');
    }

    public function getCommentsProperty()
    {
        return Comment::query()
            ->where('post_id', $this->post->id)
            ->approved()
            ->topLevel()
            ->with(['user', 'approvedReplies'])
            ->orderBy('created_at')
            ->get();
    }

    public function render(): View
    {
        return view('public.blog.show', [
            'post' => $this->post,
        ])->layout('layouts.landing', [
            'title' => $this->post->title,
        ]);
    }
}

