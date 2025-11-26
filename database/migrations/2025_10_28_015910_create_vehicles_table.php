<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('police_number', 11)->unique();
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('type_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('vehicle_model_id')->constrained();
            $table->string('year');
            $table->decimal('cylinder_capacity', 15, 2)->unsigned()->nullable();
            $table->string('chassis_number');
            $table->string('engine_number');
            $table->string('color')->nullable();
            $table->string('fuel_type')->nullable();
            $table->decimal('kilometer', 15, 2)->unsigned();
            $table->date('vehicle_registration_date');
            $table->date('vehicle_registration_expiry_date');
            $table->string('file_stnk');
            $table->foreignId('warehouse_id')->constrained();
            $table->date('purchase_date');
            $table->decimal('purchase_price', 15, 2)->unsigned();
            $table->decimal('display_price', 15, 2)->unsigned();
            $table->date('selling_date')->nullable();
            $table->decimal('selling_price', 15, 2)->unsigned()->nullable();
            $table->enum('status', [0, 1])->default(1)->comment('0=sold, 1=available');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index('police_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
