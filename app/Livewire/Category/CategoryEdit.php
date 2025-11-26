<?php

namespace App\Livewire\Category;

use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Category')]
class CategoryEdit extends Component
{
    public Category $category;

    public string $name;
    public string $description;

    public function mount(Category $category): void
    {
        $this->category = $category;

        $this->name = $category->name;
        $this->description = $category->description ?? '';
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $this->category->id,
            'description' => 'nullable|string',
        ]);

        // Store old values for logging
        $oldValues = [
            'name' => $this->category->name,
            'description' => $this->category->description,
        ];

        $this->category->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Log the update activity with detailed information
        activity()
            ->performedOn($this->category)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            ])
            ->log('updated category');

        session()->flash('success', 'Category updated.');

        return $this->redirect('/categories', true);
    }

    public function render()
    {
        return view('livewire.category.category-edit');
    }
}
