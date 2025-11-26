<?php

namespace App\Livewire\Salesman;

use Livewire\Component;
use App\Models\Salesman;
use App\Models\User;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

#[Title('Create Salesman')]
class SalesmanCreate extends Component
{
    public $name, $phone, $email, $address;

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:salesmen,name',
            'phone' => 'nullable|string|max:50',
            'email' => 'required|email|max:255|unique:salesmen,email|unique:users,email',
            'address' => 'nullable|string',
        ]);

        DB::transaction(function () {
            // Create user with salesman role
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'password' => Hash::make('password'),
                'status' => '1',
                'timezone' => 'Asia/Jakarta',
                'email_verified_at' => now(),
                'is_email_verified' => true,
            ]);

            // Assign salesman role
            $user->syncRoles(['salesman']);

            // Create salesman record
            $salesman = Salesman::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'user_id' => $user->id,
            ]);

            // Log the creation activity with detailed information
            activity()
                ->performedOn($salesman)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'name' => $this->name,
                        'phone' => $this->phone,
                        'email' => $this->email,
                        'address' => $this->address,
                        'user_id' => $user->id,
                    ]
                ])
                ->log('created salesman');
        });

        session()->flash('success', 'Salesman created.');

        return $this->redirect('/salesmen', true);
    }

    public function render()
    {
        return view('livewire.salesman.salesman-create');
    }
}
