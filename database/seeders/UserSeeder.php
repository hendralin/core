<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SUPERADMIN
        $superadmin = User::create([
            'name' => env('SUPERADMIN_NAME'),
            'email' => env('SUPERADMIN_EMAIL'),
            'phone' => env('SUPERADMIN_PHONE'),
            'birth_date' => env('SUPERADMIN_BIRTH_DATE'),
            'address' => env('SUPERADMIN_ADDRESS'),
            'email_verified_at' => now(),
            'password' => env('SUPERADMIN_PASSWORD'),
            'remember_token' => Str::random(60),
            'avatar' => 'd8613573-f1c4-4235-a906-b689bdeca6c7.png',
            'status' => 1,
        ]);

        $superadmin->syncRoles('superadmin');
    }
}
