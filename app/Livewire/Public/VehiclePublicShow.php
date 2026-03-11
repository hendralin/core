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

    public function incrementWhatsAppShare(): void
    {
        $this->vehicle->increment('whatsapp_share_count');
        $this->vehicle->refresh();
    }

    public function incrementLinkCopy(): void
    {
        $this->vehicle->increment('link_copy_count');
        $this->vehicle->refresh();
    }

    public function incrementChatWhatsApp(): void
    {
        $this->vehicle->increment('chat_whatsapp_count');
    }

    public function render()
    {
        return view('livewire.public.vehicle-show', [
            'vehicle' => $this->vehicle,
        ]);
    }
}

