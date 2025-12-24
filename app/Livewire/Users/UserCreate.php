<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

#[Title('Create User')]
class UserCreate extends Component
{
    public $name, $email, $phone, $birth_date, $address, $timezone, $password, $confirm_password, $allRoles;

    public $roles = [];

    public function mount()
    {
        $this->allRoles = Role::whereNotIn('name', ['salesman', 'customer', 'supplier', 'cashier'])->get();
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'phone' => 'nullable|string|regex:/^[\d\s\-\+\(\)]{10,15}$/',
            'birth_date' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'timezone' => 'required|string',
            'roles' => 'required|array',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/|same:confirm_password',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Nama lengkap harus berupa teks.',
            'name.max' => 'Nama lengkap tidak boleh lebih dari 255 karakter.',

            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.lowercase' => 'Alamat email harus menggunakan huruf kecil.',
            'email.max' => 'Alamat email tidak boleh lebih dari 255 karakter.',
            'email.unique' => 'Alamat email sudah terdaftar dalam sistem.',

            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format: +62xxxxxxxxxx atau 08xxxxxxxxxx',

            'birth_date.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',

            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat tidak boleh lebih dari 500 karakter.',

            'timezone.required' => 'Zona waktu wajib dipilih.',

            'roles.required' => 'Setidaknya satu peran harus dipilih.',
            'roles.array' => 'Format peran tidak valid.',

            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus mengandung huruf besar, huruf kecil, dan angka.',
            'password.same' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'timezone' => $this->timezone,
            'password' => Hash::make($this->password),
            'status' => '1',
            'email_verified_at' => now(), // Auto-verify for admin-created users
            'is_email_verified' => true,
        ]);
        $apiToken = $user->regenerateApiToken();

        $user->syncRoles($this->roles);

        // Log activity with detailed information
        activity()
            ->performedOn($user)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'birth_date' => $this->birth_date,
                    'address' => $this->address,
                    'timezone' => $this->timezone,
                    'status' => '1',
                    'roles' => $this->roles,
                ],
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('created a new user account');

        session()->flash('success', 'User created.');
        session()->flash('api_token', $apiToken);

        return $this->redirect('/users', true);
    }

    public function render()
    {
        return view('livewire.users.user-create');
    }
}
