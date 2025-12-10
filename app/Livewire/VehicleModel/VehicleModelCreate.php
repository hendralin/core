<?php

namespace App\Livewire\VehicleModel;

use Livewire\Component;
use App\Models\VehicleModel;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Create Vehicle Model')]
class VehicleModelCreate extends Component
{
    public $name, $description;

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:vehicle_models,name',
            'description' => 'nullable|string',
        ]);

        $vehicleModel = VehicleModel::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Log the creation activity with detailed information
        activity()
            ->performedOn($vehicleModel)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            ])
            ->log('created vehicle model');

        session()->flash('success', 'Vehicle model created.');

        return $this->redirect('/models', true);
    }

    public function render()
    {
        return view('livewire.vehicle-model.vehicle-model-create');
    }
}
