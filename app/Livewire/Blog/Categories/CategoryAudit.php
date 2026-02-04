<?php

namespace App\Livewire\Blog\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Blog Category Audit Trail')]
class CategoryAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public Category $category;

    public $search = '';
    public $perPage = 10;

    public function mount(Category $category)
    {
        $this->category = $category;
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $activities = $this->category->activities()
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

        return view('livewire.blog.categories.category-audit', compact('activities'));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
