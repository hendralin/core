<?php

namespace App\Livewire\Public;

use App\Models\Vehicle;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detail Kendaraan')]
#[Layout('layouts.public')]
class VehiclePublicShow extends Component
{
    public Vehicle $vehicle;

    public function mount(Vehicle $vehicle): void
    {
        // Hanya tampilkan kendaraan dengan status Available
        if ($vehicle->status !== '1') {
            abort(404);
        }

        $this->vehicle = $vehicle->load([
            'brand',
            'type',
            'category',
            'vehicle_model',
            'warehouse',
            'images',
        ]);
    }

    public function render()
    {
        return view('livewire.public.vehicle-show', [
            'vehicle' => $this->vehicle,
        ]);
    }
}

