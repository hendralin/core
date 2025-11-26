<?php

namespace App\Livewire\Salesman;

use Livewire\Component;
use App\Models\Salesman;
use Livewire\Attributes\Title;

#[Title('Show Salesman')]
class SalesmanShow extends Component
{
    public Salesman $salesman;

    public function mount(Salesman $salesman): void
    {
        $this->salesman = $salesman;
    }

    public function render()
    {
        return view('livewire.salesman.salesman-show');
    }
}