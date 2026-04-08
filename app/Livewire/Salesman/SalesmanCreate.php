<?php

namespace App\Livewire\Salesman;

use Livewire\Component;
use App\Models\Salesman;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

#[Title('Create Salesman')]
class SalesmanCreate extends Component
{
    use WithFileUploads;

    public $name;
    public $phone;
    public $email;
    public $address;
    public $signature;

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:salesmen,name',
            'phone' => 'nullable|string|max:50',
            'email' => 'required|email|max:255|unique:salesmen,email|unique:users,email',
            'address' => 'nullable|string',
            'signature' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'signature.required' => 'Tanda tangan wajib diunggah.',
            'signature.image' => 'Tanda tangan harus berupa gambar.',
            'signature.mimes' => 'Tanda tangan harus berformat JPEG, JPG, PNG, atau WebP.',
            'signature.max' => 'Ukuran gambar tanda tangan maksimal 2MB.',
        ]);

        $storedPath = $this->signature->store('salesmen/signatures', 'photos');
        $signatureFile = basename($storedPath);

        DB::transaction(function () use ($signatureFile) {
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
                'signature' => $signatureFile,
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
                        'signature' => $signatureFile,
                    ]
                ])
                ->log('created salesman');
        });

        session()->flash('success', 'Salesman created.');

        return $this->redirect('/salesmen', true);
    }

    public function removeSignature(): void
    {
        $this->signature = null;
    }

    public function render()
    {
        return view('livewire.salesman.salesman-create');
    }
}
