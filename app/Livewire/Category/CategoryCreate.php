<?php

namespace App\Livewire\Category;

use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Create Category')]
class CategoryCreate extends Component
{
    public $name, $description;

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Log the creation activity with detailed information
        activity()
            ->performedOn($category)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            ])
            ->log('created category');

        session()->flash('success', 'Category created.');

        return $this->redirect('/categories', true);
    }

    public function render()
    {
        return view('livewire.category.category-create');
    }
}
