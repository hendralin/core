<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use App\Models\Vendor;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Vendor')]
class VendorEdit extends Component
{
    public Vendor $vendor;

    public string $name;
    public string $contact;
    public string $phone;
    public string $email;
    public string $address;

    public function mount(Vendor $vendor): void
    {
        $this->vendor = $vendor;

        $this->name = $vendor->name;
        $this->contact = $vendor->contact ?? '';
        $this->phone = $vendor->phone ?? '';
        $this->email = $vendor->email ?? '';
        $this->address = $vendor->address ?? '';
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:vendors,name,' . $this->vendor->id,
            'contact' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        // Store old values for logging
        $oldValues = [
            'name' => $this->vendor->name,
            'contact' => $this->vendor->contact,
            'phone' => $this->vendor->phone,
            'email' => $this->vendor->email,
            'address' => $this->vendor->address,
        ];

        $this->vendor->update([
            'name' => $this->name,
            'contact' => $this->contact,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
        ]);

        // Log the update activity with detailed information
        activity()
            ->performedOn($this->vendor)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'name' => $this->name,
                    'contact' => $this->contact,
                    'phone' => $this->phone,
                    'email' => $this->email,
                    'address' => $this->address,
                ]
            ])
            ->log('updated vendor');

        session()->flash('success', 'Vendor updated.');

        return $this->redirect('/vendors', true);
    }

    public function render()
    {
        return view('livewire.vendor.vendor-edit');
    }
}
