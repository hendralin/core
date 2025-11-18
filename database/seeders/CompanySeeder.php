<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'name' => env('COMPANY_NAME'),
            'address' => env('COMPANY_ADDRESS'),
            'phone' => env('COMPANY_PHONE'),
            'email' => env('COMPANY_EMAIL'),
        ]);
    }
}
