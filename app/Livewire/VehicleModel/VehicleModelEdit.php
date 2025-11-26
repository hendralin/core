<?php

namespace App\Livewire\VehicleModel;

use Livewire\Component;
use App\Models\VehicleModel;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Vehicle Model')]
class VehicleModelEdit extends Component
{
    public VehicleModel $vehicleModel;

    public string $name;
    public string $description;

    public function mount(VehicleModel $vehicleModel): void
    {
        $this->vehicleModel = $vehicleModel;

        $this->name = $vehicleModel->name;
        $this->description = $vehicleModel->description ?? '';
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:vehicle_models,name,' . $this->vehicleModel->id,
            'description' => 'nullable|string',
        ]);

        // Store old values for logging
        $oldValues = [
            'name' => $this->vehicleModel->name,
            'description' => $this->vehicleModel->description,
        ];

        $this->vehicleModel->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Log the update activity with detailed information
        activity()
            ->performedOn($this->vehicleModel)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            ])
            ->log('updated vehicle model');

        session()->flash('success', 'Vehicle model updated.');

        return $this->redirect('/vehicle-models', true);
    }

    public function render()
    {
        return view('livewire.vehicle-model.vehicle-model-edit');
    }
}
