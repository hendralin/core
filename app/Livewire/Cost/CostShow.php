<?php

namespace App\Livewire\Cost;

use App\Models\Cost;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Pembukuan Modal Details')]
class CostShow extends Component
{
    public Cost $cost;

    public function mount(Cost $cost): void
    {
        $this->cost = $cost->load(['vehicle.brand', 'vehicle.vehicle_model', 'vehicle.type', 'vendor', 'createdBy']);
    }

    public function approve()
    {
        $this->cost->update(['status' => 'approved']);
        session()->flash('success', 'Pembukuan modal disetujui.');
        $this->cost->refresh();
    }

    public function reject()
    {
        $this->cost->update(['status' => 'rejected']);
        session()->flash('success', 'Pembukuan modal ditolak.');
        $this->cost->refresh();
    }

    public function render()
    {
        return view('livewire.cost.cost-show');
    }
}
