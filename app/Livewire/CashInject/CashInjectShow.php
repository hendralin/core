<?php

namespace App\Livewire\CashInject;

use App\Models\Cost;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Detail Inject Kas')]
class CashInjectShow extends Component
{
    public Cost $cost;

    public function mount(Cost $cost): void
    {
        $this->cost = $cost->load(['createdBy']);

        // Check if this is actually a cash inject
        if (!in_array($cost->cost_type, ['cash'])) {
            abort(403, 'Record ini bukan merupakan inject kas.');
        }
    }


    public function render()
    {
        return view('livewire.cash-inject.cash-inject-show');
    }
}
