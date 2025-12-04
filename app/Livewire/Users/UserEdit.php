<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use App\Models\Warehouse;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

#[Title('Edit User')]
class UserEdit extends Component
{
    public User $user;

    public $activeTab = 'profile';

    protected $queryString = [
        'activeTab' => ['except' => 'profile'],
    ];
    public $name, $email, $phone, $birth_date, $address, $timezone, $password, $confirm_password, $allRoles, $allWarehouses, $status;

    public $roles = [];

    public $warehouses = [];

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->birth_date = $user->birth_date?->format('Y-m-d');
        $this->address = $user->address;
        $this->timezone = $user->timezone;
        $this->status = $user->status;

        $this->allRoles = Role::whereNotIn('name', ['salesman', 'customer', 'supplier', 'cashier'])->get();
        $this->roles = $user->roles()->pluck('name');

        $this->allWarehouses = Warehouse::all();
        $this->warehouses = $user->warehouses()->pluck('id');
    }

    public function submit()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $this->user->id,
            'phone' => 'nullable|string|regex:/^[\d\s\-\+\(\)]{10,15}$/',
            'birth_date' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'timezone' => 'required|string',
            'status' => 'required|integer|in:0,1,2',
            'roles' => 'required|array',
            'warehouses' => 'required|array|min:1',
        ];

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Nama lengkap harus berupa teks.',
            'name.max' => 'Nama lengkap tidak boleh lebih dari 255 karakter.',

            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.lowercase' => 'Alamat email harus menggunakan huruf kecil.',
            'email.max' => 'Alamat email tidak boleh lebih dari 255 karakter.',
            'email.unique' => 'Alamat email sudah digunakan oleh pengguna lain.',

            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format: +62xxxxxxxxxx atau 08xxxxxxxxxx',

            'birth_date.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',

            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat tidak boleh lebih dari 500 karakter.',

            'timezone.required' => 'Zona waktu wajib dipilih.',

            'status.required' => 'Status pengguna wajib dipilih.',
            'status.integer' => 'Status pengguna harus berupa angka.',
            'status.in' => 'Status pengguna tidak valid.',

            'roles.required' => 'Setidaknya satu peran harus dipilih.',
            'roles.array' => 'Format peran tidak valid.',

            'warehouses.required' => 'Setidaknya satu gudang harus dipilih.',
            'warehouses.array' => 'Format gudang tidak valid.',
            'warehouses.min' => 'Setidaknya satu gudang harus dipilih.',
        ];

        // Add password validation only if password is provided
        if ($this->password) {
            $rules['password'] = 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/|same:confirm_password';
            $messages = array_merge($messages, [
                'password.required' => 'Kata sandi wajib diisi.',
                'password.string' => 'Kata sandi harus berupa teks.',
                'password.min' => 'Kata sandi minimal 8 karakter.',
                'password.regex' => 'Kata sandi harus mengandung setidaknya satu huruf kecil, satu huruf besar, dan satu angka.',
                'password.same' => 'Konfirmasi kata sandi tidak cocok.',
            ]);
        }

        $this->validate($rules, $messages);

        // Store old values for logging
        $oldValues = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'birth_date' => $this->user->birth_date,
            'address' => $this->user->address,
            'timezone' => $this->user->timezone,
            'status' => $this->user->status,
            'roles' => $this->user->roles()->pluck('name')->toArray(),
            'warehouses' => $this->user->warehouses()->pluck('id')->toArray(),
        ];

        $this->user->name = $this->name;
        $this->user->email = $this->email;
        $this->user->phone = $this->phone;
        $this->user->birth_date = $this->birth_date;
        $this->user->address = $this->address;
        $this->user->timezone = $this->timezone;
        $this->user->status = $this->status;

        if ($this->password) {
            $this->user->password = Hash::make($this->password);
        }

        $this->user->save();

        $this->user->syncRoles($this->roles);

        $this->user->warehouses()->sync($this->warehouses);

        // Log activity with detailed before/after information
        activity()
            ->performedOn($this->user)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'birth_date' => $this->birth_date,
                    'address' => $this->address,
                    'timezone' => $this->timezone,
                    'status' => $this->status,
                    'roles' => $this->roles,
                    'warehouses' => $this->warehouses,
                    'password_changed' => !empty($this->password),
                ],
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('updated user profile information');

        session()->flash('success', 'User updated.');

        return $this->redirect('/users', true);
    }

    public function render()
    {
        return view('livewire.users.user-edit');
    }
}
