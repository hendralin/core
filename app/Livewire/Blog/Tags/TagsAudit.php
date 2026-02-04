<?php

namespace App\Livewire\Blog\Tags;

use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Blog Tag Audit Trail')]
class TagsAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public Tag $tag;

    public $search = '';
    public $perPage = 10;

    public function mount(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $activities = $this->tag->activities()
            ->with('causer')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                      ->orWhere('event', 'like', '%' . $this->search . '%')
                      ->orWhereHas('causer', function ($causerQuery) {
                          $causerQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.blog.tags.tags-audit', compact('activities'));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
