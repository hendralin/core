<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateCostWarehouseFromVehicle extends Command
{
    /**
     * Jalankan dengan:
     * php artisan costs:update-warehouse
     */
    protected $signature = 'costs:update-warehouse';

    protected $description = 'Update costs.warehouse_id berdasarkan warehouse_id dari vehicles (single UPDATE query)';

    public function handle(): int
    {
        $this->info('Menjalankan UPDATE warehouse_id di tabel costs...');

        // Sesuaikan nama tabel jika berbeda
        $affected = DB::update("
            UPDATE costs
            JOIN vehicles ON vehicles.id = costs.vehicle_id
            SET costs.warehouse_id = vehicles.warehouse_id
            WHERE costs.warehouse_id IS NULL
              AND costs.vehicle_id IS NOT NULL
              AND vehicles.warehouse_id IS NOT NULL
        ");

        $this->info("Selesai. Total baris yang ter-update: {$affected}");

        return self::SUCCESS;
    }
}
