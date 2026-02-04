<?php

namespace App\Livewire\Blog\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\CategoryService;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;

#[Title('Blog Categories')]
class CategoryIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected CategoryService $categoryService;

    public $categoryIdToDelete = null;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $sortableFields = ['name', 'posts_count'];

    public function boot(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function sortBy($field)
    {
        if (!in_array($field, $this->sortableFields)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function setCategoryToDelete($categoryId)
    {
        $this->categoryIdToDelete = $categoryId;
    }

    public function delete()
    {
        try {
            if (!$this->categoryIdToDelete) {
                session()->flash('error', 'No category selected for deletion.');
                return;
            }

            $category = Category::findOrFail($this->categoryIdToDelete);

            // Check if category can be deleted using CategoryService
            $canDelete = $this->categoryService->canDeleteCategory($category);

            if (!$canDelete['can_delete']) {
                session()->flash('error', implode(' ', $canDelete['errors']));
                return;
            }

            DB::transaction(function () use ($category) {
                // Store category data for logging before deletion
                $categoryData = [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'color' => $category->color,
                ];

                $category->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($category)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $categoryData
                    ])
                    ->log('deleted blog category');
            });

            $this->reset(['categoryIdToDelete']);

            session()->flash('success', 'Category deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Category not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $categories = $this->categoryService->getEnhancedCategoriesForIndex(
            $this->search,
            $this->sortField,
            $this->sortDirection
        );

        return view('livewire.blog.categories.category-index', compact('categories'));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
