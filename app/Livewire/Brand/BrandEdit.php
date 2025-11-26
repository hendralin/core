<?php

namespace App\Livewire\Brand;

use Livewire\Component;
use App\Models\Brand;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Brand')]
class BrandEdit extends Component
{
    public Brand $brand;

    public string $name;
    public string $description;

    public function mount(Brand $brand): void
    {
        $this->brand = $brand;

        $this->name = $brand->name;
        $this->description = $brand->description ?? '';
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $this->brand->id,
            'description' => 'nullable|string',
        ]);

        // Store old values for logging
        $oldValues = [
            'name' => $this->brand->name,
            'description' => $this->brand->description,
        ];

        $this->brand->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Log the update activity with detailed information
        activity()
            ->performedOn($this->brand)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            ])
            ->log('updated brand');

        session()->flash('success', 'Brand updated.');

        return $this->redirect('/brands', true);
    }

    public function render()
    {
        return view('livewire.brand.brand-edit');
    }
}
