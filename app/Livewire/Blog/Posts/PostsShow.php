<?php

namespace App\Livewire\Blog\Posts;

use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Contracts\View\View;

#[Title('Post Details')]
class PostsShow extends Component
{
    public Post $post;

    public function mount(Post $post): void
    {
        $this->post = $post->load(['user', 'categories', 'tags'])->loadCount('comments');
    }

    public function render(): View
    {
        return view('livewire.blog.posts.posts-show');
    }
}
