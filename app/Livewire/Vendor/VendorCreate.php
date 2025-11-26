<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use App\Models\Vendor;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Create Vendor')]
class VendorCreate extends Component
{
    public $name, $contact, $phone, $email, $address;

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:vendors,name',
            'contact' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $vendor = Vendor::create([
            'name' => $this->name,
            'contact' => $this->contact,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
        ]);

        // Log the creation activity with detailed information
        activity()
            ->performedOn($vendor)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'name' => $this->name,
                    'contact' => $this->contact,
                    'phone' => $this->phone,
                    'email' => $this->email,
                    'address' => $this->address,
                ]
            ])
            ->log('created vendor');

        session()->flash('success', 'Vendor created.');

        return $this->redirect('/vendors', true);
    }

    public function render()
    {
        return view('livewire.vendor.vendor-create');
    }
}
