<?php

namespace App\Livewire\Salesman;

use Livewire\Component;
use App\Models\Salesman;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

#[Title('Edit Salesman')]
class SalesmanEdit extends Component
{
    use WithFileUploads;

    public Salesman $salesman;

    public string $name;
    public string $phone;
    public string $email;
    public string $address;
    public string $status;
    public $signature;
    public ?string $existing_signature = null;

    public function mount(Salesman $salesman): void
    {
        $this->salesman = $salesman;

        $this->name = $salesman->name;
        $this->phone = $salesman->phone ?? '';
        $this->email = $salesman->email;
        $this->address = $salesman->address ?? '';
        $this->status = $salesman->user->status ?? '1';
        $this->existing_signature = $salesman->signature;
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:salesmen,name,' . $this->salesman->id,
            'phone' => 'nullable|string|max:50',
            'email' => 'required|email|max:255|unique:salesmen,email,' . $this->salesman->id . '|unique:users,email,' . $this->salesman->user_id,
            'address' => 'nullable|string',
            'status' => 'required|in:0,1',
            'signature' => [
                Rule::requiredIf(fn () => blank($this->existing_signature)),
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048',
            ],
        ], [
            'signature.required' => 'Tanda tangan wajib diunggah.',
            'signature.image' => 'Tanda tangan harus berupa gambar.',
            'signature.mimes' => 'Tanda tangan harus berformat JPEG, JPG, PNG, atau WebP.',
            'signature.max' => 'Ukuran gambar tanda tangan maksimal 2MB.',
        ]);

        $signaturePath = $this->existing_signature;
        if ($this->signature) {
            if ($this->existing_signature) {
                Storage::disk('photos')->delete('salesmen/signatures/' . $this->existing_signature);
            }
            $storedPath = $this->signature->store('salesmen/signatures', 'photos');
            $signaturePath = basename($storedPath);
            $this->existing_signature = $signaturePath;
            $this->signature = null;
        }

        DB::transaction(function () use ($signaturePath) {
            // Store old values for logging
            $oldValues = [
                'name' => $this->salesman->name,
                'phone' => $this->salesman->phone,
                'email' => $this->salesman->email,
                'address' => $this->salesman->address,
                'user_id' => $this->salesman->user_id,
                'status' => $this->salesman->user->status,
                'signature' => $this->salesman->signature,
            ];

            // Update salesman record
            $this->salesman->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'signature' => $signaturePath,
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
                        'signature' => $signaturePath,
                    ]
                ])
                ->log('updated salesman');
        });

        session()->flash('success', 'Salesman updated.');

        return $this->redirect('/salesmen', true);
    }

    public function removeSignature(): void
    {
        $this->signature = null;
    }

    public function render()
    {
        return view('livewire.salesman.salesman-edit');
    }
}
