<?php

namespace App\Livewire\Blog\Categories;

use App\Models\Category;
use Livewire\Component;
use App\Services\CategoryService;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Blog Category')]
class CategoryEdit extends Component
{
    protected CategoryService $categoryService;

    public Category $category;

    public $name;
    public $description;
    public $color;
    public $customColor;

    public function boot(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function mount(Category $category)
    {
        $this->category = $category;

        $this->name = $category->name;
        $this->description = $category->description;

        // Check if color is a custom hex or from palette
        $availableColors = $this->categoryService->getAvailableColors();
        if (array_key_exists($category->color, $availableColors)) {
            $this->color = $category->color;
            $this->customColor = null;
        } else {
            $this->color = null;
            $this->customColor = $category->color;
        }
    }

    public function submit()
    {
        // Determine final color value
        $finalColor = $this->customColor ?: $this->color;

        $this->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'unique:categories,name,' . $this->category->id,
                'regex:/^[a-zA-Z0-9\s\-_]+$/'
            ],
            'description' => 'nullable|string|max:500',
            'customColor' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
        ], [
            'name.required' => 'Category name is required.',
            'name.min' => 'Category name must be at least 2 characters.',
            'name.max' => 'Category name cannot exceed 100 characters.',
            'name.unique' => 'This category name already exists.',
            'name.regex' => 'Category name can only contain letters, numbers, spaces, hyphens, and underscores.',
            'description.max' => 'Description cannot exceed 500 characters.',
            'customColor.regex' => 'Custom color must be a valid hex color code (e.g., #FF5733).',
        ]);

        // Additional validation for color selection
        if (!$this->customColor && !$this->color) {
            $this->addError('color', 'Please select a color from the palette or choose a custom color.');
            return;
        }

        if (!$this->customColor && !array_key_exists($this->color, $this->categoryService->getAvailableColors())) {
            $this->addError('color', 'Selected color is not valid.');
            return;
        }

        try {
            $oldData = [
                'name' => $this->category->name,
                'description' => $this->category->description,
                'color' => $this->category->color,
            ];

            $this->category->update([
                'name' => trim($this->name),
                'description' => trim($this->description) ?: null,
                'color' => $finalColor,
            ]);

            // Log the update activity with detailed information
            activity()
                ->performedOn($this->category)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => $oldData,
                    'attributes' => [
                        'name' => trim($this->name),
                        'description' => trim($this->description) ?: null,
                        'color' => $finalColor,
                        'color_source' => $this->customColor ? 'custom' : 'palette',
                    ]
                ])
                ->log('updated blog category');

            session()->flash('success', 'Category updated successfully.');

            return $this->redirect('/categories', true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update category. Please try again.');
            throw $e;
        }
    }

    public function render()
    {
        $availableColors = $this->categoryService->getAvailableColors();

        return view('livewire.blog.categories.category-edit', compact('availableColors'));
    }
}
