<?php

namespace App\Livewire\Blog\Posts;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Title('Create Post')]
class PostsCreate extends Component
{
    use WithFileUploads;

    #[Validate('required|string|min:3|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:500')]
    public string $excerpt = '';

    #[Validate('required|string|min:10')]
    public string $content = '';

    #[Validate('nullable|image|max:2048')]
    public $featured_image;

    #[Validate('required|in:draft,published')]
    public string $status = 'draft';

    #[Validate('required|array|min:1')]
    public array $selectedCategories = [];

    #[Validate('nullable|array')]
    public array $selectedTags = [];

    public function save(): void
    {
        $this->validate();

        DB::transaction(function () use (&$post) {
            $post = new Post();
            $post->user_id = auth()->id();
            $post->title = $this->title;
            $post->slug = \Illuminate\Support\Str::slug($this->title);
            $post->excerpt = $this->excerpt;
            $post->content = $this->content;
            $post->status = $this->status;

            if ($this->featured_image) {
                $storedPath = $this->featured_image->store('media', 'public');
                $post->featured_image = basename($storedPath);
            }

            if ($this->status === 'published') {
                $post->published_at = now();
            }

            $post->save();
            $post->categories()->attach($this->selectedCategories);
            if (! empty($this->selectedTags)) {
                $post->tags()->attach($this->selectedTags);
            }

            activity()
                ->performedOn($post)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'user_id' => $post->user_id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'excerpt' => $post->excerpt,
                        'status' => $post->status,
                        'featured_image' => $post->featured_image,
                        'published_at' => optional($post->published_at)->toDateTimeString(),
                    ],
                    'relations' => [
                        'categories' => $this->selectedCategories,
                        'tags' => $this->selectedTags,
                    ],
                ])
                ->log('created blog post');
        });

        session()->flash('success', __('Post created successfully.'));
        $this->redirect(route('blog.posts.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.blog.posts.posts-create', [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }
}
