<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use App\Models\UserHasWarehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::create([
            'name' => 'Central',
            'address' => 'Central Warehouse',
        ]);

        UserHasWarehouse::create([
            'user_id' => 1,
            'warehouse_id' => 1,
        ]);
    }
}
