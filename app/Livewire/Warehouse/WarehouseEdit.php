<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Warehouse;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Warehouse')]
class WarehouseEdit extends Component
{
    public Warehouse $warehouse;

    public string $name;

    public string $address;

    public function mount(Warehouse $warehouse): void
    {
        $this->warehouse = $warehouse;

        $this->name = $warehouse->name;

        $this->address = $warehouse->address;
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:warehouses,name,' . $this->warehouse->id,
            'address' => 'required|string',
        ]);

        // Store old values for logging
        $oldValues = [
            'name' => $this->warehouse->name,
            'address' => $this->warehouse->address,
        ];

        $this->warehouse->update([
            'name' => $this->name,
            'address' => $this->address,
        ]);

        // Log the update activity with detailed information
        activity()
            ->performedOn($this->warehouse)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'name' => $this->name,
                    'address' => $this->address,
                ]
            ])
            ->log('updated warehouse');

        session()->flash('success', 'Warehouse updated.');

        return $this->redirect('/warehouses', true);
    }

    public function render()
    {
        return view('livewire.warehouse.warehouse-edit');
    }
}
