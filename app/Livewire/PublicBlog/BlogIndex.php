<?php

namespace App\Livewire\PublicBlog;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Illuminate\Contracts\View\View;

#[Title('Blogs')]
class BlogIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'category')]
    public ?string $selectedCategory = null;

    #[Url(as: 'tags')]
    public array $selectedTags = [];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedTags(): void
    {
        $this->resetPage();
    }

    public function selectCategory(?string $slug): void
    {
        $this->selectedCategory = $slug;
        $this->resetPage();
    }

    public function toggleTag(string $slug): void
    {
        if (in_array($slug, $this->selectedTags)) {
            $this->selectedTags = array_values(array_diff($this->selectedTags, [$slug]));
        } else {
            $this->selectedTags = array_values([...$this->selectedTags, $slug]);
        }
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = null;
        $this->selectedTags = [];
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Post::where('status', 'published')
            ->with(['categories', 'tags', 'user'])
            ->orderByDesc('published_at');

        if ($this->selectedCategory) {
            $query->whereHas('categories', fn ($q) => $q->where('categories.slug', $this->selectedCategory));
        }

        if (! empty($this->selectedTags)) {
            $query->whereHas('tags', fn ($q) => $q->whereIn('tags.slug', $this->selectedTags));
        }

        $search = trim($this->search);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('excerpt', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $posts = $query->paginate(9);

        $categories = Category::whereHas('posts', fn ($q) => $q->where('status', 'published'))
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name')
            ->get();

        $tags = Tag::whereHas('posts', fn ($q) => $q->where('status', 'published'))
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name')
            ->get();

        return view('public.blog.index', [
            'posts' => $posts,
            'categories' => $categories,
            'tags' => $tags,
        ])->layout('layouts.landing', [
            'title' => config('app.name'),
        ]);
    }
}

