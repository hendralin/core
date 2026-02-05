<?php

namespace App\Livewire\Blog\Comments;

use App\Models\Comment;
use Livewire\Component;
use Livewire\WithPagination;

class CommentsIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public ?int $commentIdToDelete = null;

    public function with(): array
    {
        $query = Comment::with(['user', 'post'])
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('content', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // $user = auth()->user();
        // if ($user && $user->hasRole('author')) {
        //     $query->whereHas('post', function ($q) use ($user) {
        //         $q->where('user_id', $user->id);
        //     });
        // }

        return [
            'comments' => $query->paginate(20),
            'stats' => $this->getStats(),
        ];
    }

    protected function getStats(): array
    {
        $baseQuery = Comment::query();
        $user = auth()->user();
        if ($user && $user->hasRole('author')) {
            $baseQuery->whereHas('post', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return [
            'total' => (clone $baseQuery)->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'spam' => (clone $baseQuery)->where('status', 'spam')->count(),
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function approveComment(Comment $comment): void
    {
        $comment->update(['status' => 'approved']);
        session()->flash('success', __('Comment approved!'));
    }

    public function markAsSpam(Comment $comment): void
    {
        $comment->update(['status' => 'spam']);
        session()->flash('success', __('Comment marked as spam!'));
    }

    public function setCommentToDelete(int $commentId): void
    {
        $this->commentIdToDelete = $commentId;
    }

    public function delete(): void
    {
        if (! $this->commentIdToDelete) {
            session()->flash('error', __('No comment selected for deletion.'));
            return;
        }

        $comment = Comment::find($this->commentIdToDelete);
        if (! $comment) {
            session()->flash('error', __('Comment not found.'));
            $this->reset('commentIdToDelete');
            return;
        }

        $comment->delete();
        $this->reset('commentIdToDelete');
        session()->flash('success', __('Comment deleted!'));
    }

    public function render()
    {
        return view('livewire.blog.comments.comments-index');
    }
}
