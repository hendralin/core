<?php

namespace App\Livewire\VehicleTaxPayment;

use App\Models\Cost;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Detail Pembayaran PKB')]
class VehicleTaxPaymentShow extends Component
{
    public Cost $cost;

    public function mount(Cost $vehicleTaxPayment): void
    {
        // Route model binding uses {vehicleTaxPayment}; keep variable name aligned with routes.
        $this->cost = $vehicleTaxPayment->load(['createdBy', 'warehouse', 'vehicle', 'activities.causer']);

        if ($this->cost->cost_type !== 'vehicle_tax') {
            abort(403, 'Record ini bukan merupakan pembayaran PKB.');
        }
    }

    public function approve()
    {
        if ($this->cost->status !== 'pending') {
            session()->flash('error', 'Pembayaran PKB ini sudah diproses.');
            return;
        }

        $this->cost->update(['status' => 'approved']);
        session()->flash('success', 'Pembayaran PKB disetujui.');
        $this->cost->refresh();
        $this->modal('approve-modal')->close();
    }

    public function reject()
    {
        if ($this->cost->status !== 'pending') {
            session()->flash('error', 'Pembayaran PKB ini sudah diproses.');
            return;
        }

        $this->cost->update(['status' => 'rejected']);
        session()->flash('success', 'Pembayaran PKB ditolak.');
        $this->cost->refresh();
        $this->modal('reject-modal')->close();
    }

    public function render()
    {
        return view('livewire.vehicle-tax-payment.vehicle-tax-payment-show');
    }
}
