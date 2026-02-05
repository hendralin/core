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
use Illuminate\Support\Facades\Storage;

#[Title('Edit Post')]
class PostsEdit extends Component
{
    use WithFileUploads;

    public Post $post;

    #[Validate('required|string|min:3|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:500')]
    public string $excerpt = '';

    #[Validate('required|string|min:10')]
    public string $content = '';

    #[Validate('nullable|image|max:2048')]
    public $featured_image;

    #[Validate('required|in:draft,published,archived')]
    public string $status = 'draft';

    public string $existing_image = '';

    #[Validate('required|array|min:1')]
    public array $selectedCategories = [];

    #[Validate('nullable|array')]
    public array $selectedTags = [];

    public function mount(Post $post): void
    {
        $canEdit = auth()->user()->can('blog.post.edit.all')
            || (auth()->user()->can('blog.post.edit.own') && $post->user_id === auth()->id());

        if (! $canEdit) {
            abort(403);
        }

        $this->post = $post;
        $this->title = $post->title;
        $this->excerpt = $post->excerpt ?? '';
        $this->content = $post->content;
        $this->status = $post->status;
        $this->existing_image = $post->featured_image ?? '';
        $this->selectedCategories = $post->categories->pluck('id')->toArray();
        $this->selectedTags = $post->tags->pluck('id')->toArray();
    }

    public function update(): void
    {
        $this->validate();

        DB::transaction(function () {
            $old = [
                'title' => $this->post->title,
                'slug' => $this->post->slug,
                'excerpt' => $this->post->excerpt,
                'content' => $this->post->content,
                'status' => $this->post->status,
                'featured_image' => $this->post->featured_image,
                'published_at' => optional($this->post->published_at)->toDateTimeString(),
                'categories' => $this->post->categories()->pluck('categories.id')->all(),
                'tags' => $this->post->tags()->pluck('tags.id')->all(),
            ];

            $this->post->title = $this->title;
            $this->post->slug = \Illuminate\Support\Str::slug($this->title);
            $this->post->excerpt = $this->excerpt;
            $this->post->content = $this->content;
            $this->post->status = $this->status;

            if ($this->featured_image) {
                if ($this->existing_image) {
                    Storage::disk('public')->delete($this->existing_image);
                }
                $this->post->featured_image = $this->featured_image->store('posts', 'public');
                $this->existing_image = $this->post->featured_image;
            }

            if ($this->status === 'published' && ! $this->post->published_at) {
                $this->post->published_at = now();
            }

            $this->post->save();
            $this->post->categories()->sync($this->selectedCategories);
            $this->post->tags()->sync($this->selectedTags);

            $new = [
                'title' => $this->post->title,
                'slug' => $this->post->slug,
                'excerpt' => $this->post->excerpt,
                'content' => $this->post->content,
                'status' => $this->post->status,
                'featured_image' => $this->post->featured_image,
                'published_at' => optional($this->post->published_at)->toDateTimeString(),
                'categories' => $this->selectedCategories,
                'tags' => $this->selectedTags,
            ];

            activity()
                ->performedOn($this->post)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => $old,
                    'attributes' => $new,
                ])
                ->log('updated blog post');
        });

        session()->flash('success', __('Post updated successfully.'));
        $this->redirect(route('blog.posts.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.blog.posts.posts-edit', [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }
}
