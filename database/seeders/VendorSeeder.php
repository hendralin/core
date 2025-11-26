<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Bengkel Aman',
                'contact' => 'Bengkel Aman',
                'phone' => '62711715462',
                'email' => 'bengkelaman@gmail.com',
                'address' => 'Jl. Sutan Syahrir No.57, Ilir Timur II. Palembang - Sumatera Selatan',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }
}
