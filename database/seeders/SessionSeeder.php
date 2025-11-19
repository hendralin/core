<?php

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessions = [
            [
                'name' => 'Primary WhatsApp Session',
                'session_id' => 'SiBerat_internal',
                'is_active' => true,
            ],
        ];

        foreach ($sessions as $sessionData) {
            Session::create($sessionData);
        }

        $this->command->info('Sample WAHA sessions created successfully.');
    }
}
