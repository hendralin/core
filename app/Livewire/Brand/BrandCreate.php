<?php

namespace App\Livewire\Brand;

use Livewire\Component;
use App\Models\Brand;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Create Brand')]
class BrandCreate extends Component
{
    public $name, $description;

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
        ]);

        $brand = Brand::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Log the creation activity with detailed information
        activity()
            ->performedOn($brand)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            ])
            ->log('created brand');

        session()->flash('success', 'Brand created.');

        return $this->redirect('/brands', true);
    }

    public function render()
    {
        return view('livewire.brand.brand-create');
    }
}
