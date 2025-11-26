<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use App\Models\Vendor;
use Livewire\Attributes\Title;

#[Title('Show Vendor')]
class VendorShow extends Component
{
    public Vendor $vendor;

    public function mount(Vendor $vendor): void
    {
        $this->vendor = $vendor;
    }

    public function render()
    {
        return view('livewire.vendor.vendor-show');
    }
}
