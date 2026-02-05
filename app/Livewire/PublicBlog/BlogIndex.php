<?php

namespace App\Livewire\PublicBlog;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Contracts\View\View;

#[Title('Blogs')]
class BlogIndex extends Component
{
    use WithPagination;

    public function render(): View
    {
        $posts = Post::where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('public.blog.index', [
            'posts' => $posts,
        ])->layout('layouts.landing', [
            'title' => config('app.name'),
        ]);
    }
}

