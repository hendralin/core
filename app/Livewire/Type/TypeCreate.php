<?php

namespace App\Livewire\Type;

use Livewire\Component;
use App\Models\Type;
use App\Models\Brand;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Create Type')]
class TypeCreate extends Component
{
    public $brand_id, $name, $description;
    public $brands = [];

    public function mount()
    {
        $this->brands = Brand::all();
    }

    public function submit()
    {
        $this->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255|unique:types,name',
            'description' => 'nullable|string',
        ]);

        $type = Type::create([
            'brand_id' => $this->brand_id,
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Log the creation activity with detailed information
        activity()
            ->performedOn($type)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'brand_id' => $this->brand_id,
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            ])
            ->log('created type');

        session()->flash('success', 'Type created.');

        return $this->redirect('/types', true);
    }

    public function render()
    {
        return view('livewire.type.type-create');
    }
}
