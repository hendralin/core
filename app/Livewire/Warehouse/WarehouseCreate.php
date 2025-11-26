<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Warehouse;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Create Warehouse')]
class WarehouseCreate extends Component
{
    public $name, $address;

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:warehouses,name',
            'address' => 'required|string',
        ]);

        $warehouse = Warehouse::create([
            'name' => $this->name,
            'address' => $this->address,
        ]);

        // Log the creation activity with detailed information
        activity()
            ->performedOn($warehouse)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'name' => $this->name,
                    'address' => $this->address,
                ]
            ])
            ->log('created warehouse');

        session()->flash('success', 'Warehouse created.');

        return $this->redirect('/warehouses', true);
    }

    public function render()
    {
        return view('livewire.warehouse.warehouse-create');
    }
}
