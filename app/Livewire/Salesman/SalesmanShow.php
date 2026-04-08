<?php

namespace App\Livewire\Salesman;

use Livewire\Component;
use App\Models\Salesman;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;

#[Title('Show Salesman')]
class SalesmanShow extends Component
{
    public Salesman $salesman;

    public function mount(Salesman $salesman): void
    {
        $this->salesman = $salesman->load('user');
    }

    public function render()
    {
        $signatureUrl = null;
        if (filled($this->salesman->signature)) {
            $relative = 'salesmen/signatures/'.$this->salesman->signature;
            if (Storage::disk('photos')->exists($relative)) {
                $signatureUrl = asset('photos/'.$relative);
            }
        }

        return view('livewire.salesman.salesman-show', [
            'signatureUrl' => $signatureUrl,
        ]);
    }
}