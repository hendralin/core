<?php

namespace App\Livewire\CashDisbursement;

use App\Models\Cost;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Detail Biaya Showroom')]
class CashDisbursementShow extends Component
{
    public Cost $cost;

    public function mount(Cost $cost): void
    {
        $this->cost = $cost->load(['createdBy']);

        // Check if this is actually a cash disbursement
        if (!in_array($cost->cost_type, ['showroom'])) {
            abort(403, 'Record ini bukan merupakan biaya showroom.');
        }
    }

    public function approve()
    {
        if ($this->cost->status !== 'pending') {
            session()->flash('error', 'Biaya showroom ini sudah diproses.');
            return;
        }

        $this->cost->update(['status' => 'approved']);
        session()->flash('success', 'Biaya showroom disetujui.');
        $this->cost->refresh();
        $this->modal('approve-modal')->close();
    }

    public function reject()
    {
        if ($this->cost->status !== 'pending') {
            session()->flash('error', 'Biaya showroom ini sudah diproses.');
            return;
        }

        $this->cost->update(['status' => 'rejected']);
        session()->flash('success', 'Biaya showroom ditolak.');
        $this->cost->refresh();
        $this->modal('reject-modal')->close();
    }

    public function render()
    {
        return view('livewire.cash-disbursement.cash-disbursement-show');
    }
}
