<?php

namespace App\Livewire\Blog\Categories;

use App\Models\Category;
use Livewire\Component;
use App\Services\CategoryService;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Create Blog Category')]
class CategoryCreate extends Component
{
    protected CategoryService $categoryService;

    public $name;
    public $description;
    public $color = 'blue';
    public $customColor;

    public function boot(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
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
                'unique:categories,name',
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
            $category = Category::create([
                'name' => trim($this->name),
                'description' => trim($this->description) ?: null,
                'color' => $finalColor,
            ]);

            // Log the creation activity with detailed information
            activity()
                ->performedOn($category)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'name' => trim($this->name),
                        'description' => trim($this->description) ?: null,
                        'color' => $finalColor,
                        'color_source' => $this->customColor ? 'custom' : 'palette',
                    ]
                ])
                ->log('created blog category');

            session()->flash('success', 'Category created successfully.');

            return $this->redirect('/categories', true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create category. Please try again.');
            throw $e;
        }
    }

    public function render()
    {
        $availableColors = $this->categoryService->getAvailableColors();

        return view('livewire.blog.categories.category-create', compact('availableColors'));
    }
}
