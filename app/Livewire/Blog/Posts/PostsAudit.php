<?php

namespace App\Livewire\Blog\Posts;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

#[Title('Post Audit Trail')]
class PostsAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public Post $post;

    public string $search = '';

    public $perPage = 10;

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function updating($field): void
    {
        if (in_array($field, ['search', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    : View
    {
        $query = $this->post->activities()
            ->with('causer')
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('description', 'like', '%' . $this->search . '%')
                        ->orWhere('event', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($causerQuery) {
                            $causerQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'desc');

        $perPage = (strtolower((string) $this->perPage) === 'all') ? null : (max(1, (int) $this->perPage) ?: 10);

        if ($perPage === null) {
            $items = $query->get();
            $total = $items->count();
            $activities = new LengthAwarePaginator($items, $total, max(1, $total), 1);
        } else {
            $activities = $query->paginate($perPage);
        }

        return view('livewire.blog.posts.posts-audit', compact('activities'));
    }

    public function getPerPageOptionsProperty(): array
    {
        return [5, 10, 25, 50, 'all'];
    }
}
