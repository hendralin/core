<?php

namespace App\Livewire\Blog\Posts;

use App\Models\Post;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Title('Posts')]
class PostsIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public string $search = '';

    public string $status = 'all';

    public string $author = 'all';

    public $perPage = 10;

    public ?int $postIdToDelete = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingAuthor(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->status = 'all';
        $this->author = 'all';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function getHasActiveFiltersProperty(): bool
    {
        if ($this->search !== '') {
            return true;
        }
        if ($this->status !== 'all') {
            return true;
        }
        if ($this->author !== 'all') {
            return true;
        }
        $perPage = $this->perPage;
        if ($perPage === 'all' || (is_numeric($perPage) && (int) $perPage !== 10)) {
            return true;
        }

        return false;
    }

    public function setPostToDelete(int $postId): void
    {
        $this->postIdToDelete = $postId;
    }

    public function delete(): void
    {
        if (! $this->postIdToDelete) {
            session()->flash('error', __('No post selected for deletion.'));
            return;
        }

        $post = Post::find($this->postIdToDelete);
        if (! $post) {
            session()->flash('error', __('Post not found.'));
            $this->reset('postIdToDelete');
            return;
        }

        $canDelete = auth()->user()->can('blog.post.delete.all')
            || (auth()->user()->can('blog.post.delete.own') && $post->user_id === auth()->id());

        if (! $canDelete) {
            session()->flash('error', __('You do not have permission to delete this post.'));
            $this->reset('postIdToDelete');
            return;
        }

        DB::transaction(function () use ($post) {
            $payload = [
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
                    'categories' => $post->categories()->pluck('categories.id')->all(),
                    'tags' => $post->tags()->pluck('tags.id')->all(),
                ],
            ];

            activity()
                ->performedOn($post)
                ->causedBy(Auth::user())
                ->withProperties($payload)
                ->log('deleted blog post');

            $post->delete();
        });

        $this->reset('postIdToDelete');
        session()->flash('success', __('Post deleted successfully.'));
    }

    public function render(): View
    {
        $baseQuery = Post::query();
        if (auth()->user()->hasRole('author')) {
            $baseQuery->where('user_id', auth()->id());
        }

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'published' => (clone $baseQuery)->where('status', 'published')->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'archived' => (clone $baseQuery)->where('status', 'archived')->count(),
        ];

        $query = Post::with(['user', 'categories', 'tags'])
            ->withCount('comments')
            ->latest();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->author !== '' && $this->author !== 'all') {
            $query->where('user_id', $this->author);
        }

        if (auth()->user()->hasRole('author')) {
            $query->where('user_id', auth()->id());
        }

        $perPage = (strtolower((string) $this->perPage) === 'all') ? null : (max(1, (int) $this->perPage) ?: 10);

        if ($perPage === null) {
            $items = $query->get();
            $total = $items->count();
            $posts = new LengthAwarePaginator($items, $total, max(1, $total), 1);
        } else {
            $posts = $query->paginate($perPage);
        }

        $authors = auth()->user()->hasRole('author')
            ? collect([auth()->user()])
            : User::permission('blog.post.create')->orderBy('name')->get(['id', 'name']);

        return view('livewire.blog.posts.posts-index', [
            'posts' => $posts,
            'stats' => $stats,
            'authors' => $authors,
        ]);
    }

    public function getPerPageOptionsProperty(): array
    {
        return [5, 10, 25, 50, 'all'];
    }
}
