<?php

namespace App\Livewire\PublicBlog;

use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Illuminate\Contracts\View\View;

class BlogShow extends Component
{
    #[Locked]
    public Post $post;

    public function mount(Post $post): void
    {
        if ($post->status !== 'published') {
            abort(404);
        }

        $this->post = $post->load(['user', 'categories', 'tags']);
    }

    public function render(): View
    {
        return view('public.blog.show', [
            'post' => $this->post,
        ])->layout('layouts.landing', [
            'title' => $this->post->title,
        ]);;
    }
}

