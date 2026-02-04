<?php

namespace App\Livewire\Blog\Categories;

use App\Models\Category;
use Livewire\Component;
use App\Services\CategoryService;
use Livewire\Attributes\Title;

#[Title('View Blog Category')]
class CategoryShow extends Component
{
    protected CategoryService $categoryService;

    public Category $category;

    public function boot(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function mount(Category $category)
    {
        $this->category = $category;
    }

    public function render()
    {
        $statistics = $this->categoryService->getCategoryStatistics($this->category);
        $usageStatus = $this->categoryService->getCategoryUsageStatus($this->category);
        $colorData = $this->categoryService->getColorData($this->category->color);
        $recentPosts = $this->categoryService->formatPostsForDisplay($this->category, 5);

        return view('livewire.blog.categories.category-show', compact(
            'statistics',
            'usageStatus',
            'colorData',
            'recentPosts'
        ));
    }
}
