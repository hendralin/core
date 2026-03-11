<?php

use App\Models\Vehicle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('police_number');
        });

        // Backfill slug using same logic as Vehicle model (brand + type + model + year)
        Vehicle::query()
            ->with(['brand', 'type', 'vehicle_model'])
            ->orderBy('id')
            ->chunkById(200, function ($vehicles) {
                foreach ($vehicles as $vehicle) {
                    $slug = $vehicle->generateUniqueSlug();
                    \Illuminate\Support\Facades\DB::table('vehicles')
                        ->where('id', $vehicle->id)
                        ->update(['slug' => $slug]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};

