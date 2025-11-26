<?php

namespace App\Livewire\Type;

use Livewire\Component;
use App\Models\Type;
use App\Models\Brand;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Type')]
class TypeEdit extends Component
{
    public Type $type;

    public $brand_id, $name, $description;
    public $brands = [];

    public function mount(Type $type): void
    {
        $this->type = $type;
        $this->brands = Brand::all();

        $this->brand_id = $type->brand_id;
        $this->name = $type->name;
        $this->description = $type->description ?? '';
    }

    public function submit()
    {
        $this->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255|unique:types,name,' . $this->type->id,
            'description' => 'nullable|string',
        ]);

        // Store old values for logging
        $oldValues = [
            'brand_id' => $this->type->brand_id,
            'name' => $this->type->name,
            'description' => $this->type->description,
        ];

        $this->type->update([
            'brand_id' => $this->brand_id,
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Log the update activity with detailed information
        activity()
            ->performedOn($this->type)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'brand_id' => $this->brand_id,
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            ])
            ->log('updated type');

        session()->flash('success', 'Type updated.');

        return $this->redirect('/types', true);
    }

    public function render()
    {
        return view('livewire.type.type-edit');
    }
}
