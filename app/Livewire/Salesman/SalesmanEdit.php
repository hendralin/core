<?php

namespace App\Livewire\Salesman;

use Livewire\Component;
use App\Models\Salesman;
use App\Models\User;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

#[Title('Edit Salesman')]
class SalesmanEdit extends Component
{
    public Salesman $salesman;

    public string $name;
    public string $phone;
    public string $email;
    public string $address;
    public string $status;

    public function mount(Salesman $salesman): void
    {
        $this->salesman = $salesman;

        $this->name = $salesman->name;
        $this->phone = $salesman->phone ?? '';
        $this->email = $salesman->email;
        $this->address = $salesman->address ?? '';
        $this->status = $salesman->user->status ?? '1';
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:salesmen,name,' . $this->salesman->id,
            'phone' => 'nullable|string|max:50',
            'email' => 'required|email|max:255|unique:salesmen,email,' . $this->salesman->id . '|unique:users,email,' . $this->salesman->user_id,
            'address' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        DB::transaction(function () {
            // Store old values for logging
            $oldValues = [
                'name' => $this->salesman->name,
                'phone' => $this->salesman->phone,
                'email' => $this->salesman->email,
                'address' => $this->salesman->address,
                'user_id' => $this->salesman->user_id,
                'status' => $this->salesman->user->status,
            ];

            // Update salesman record
            $this->salesman->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
            ]);

            // Update associated user record
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'password' => Hash::make('password'), // Always reset to default password
                'status' => $this->status,
            ];

            $this->salesman->user->update($userData);

            // Log the update activity with detailed information
            activity()
                ->performedOn($this->salesman)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => $oldValues,
                    'attributes' => [
                        'name' => $this->name,
                        'phone' => $this->phone,
                        'email' => $this->email,
                        'address' => $this->address,
                        'user_id' => $this->salesman->user_id,
                        'status' => $this->status,
                    ]
                ])
                ->log('updated salesman');
        });

        session()->flash('success', 'Salesman updated.');

        return $this->redirect('/salesmen', true);
    }

    public function render()
    {
        return view('livewire.salesman.salesman-edit');
    }
}
