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
        Schema::create('vehicle_equipment', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->unsigned()->comment('1=sales, 2=purchase');
            $table->foreignId('vehicle_id')->constrained();
            $table->tinyInteger('stnk_asli')->unsigned()->comment('1=available, 0=not available');
            $table->tinyInteger('kunci_roda')->unsigned()->nullable()->comment('1=available, 0=not available');
            $table->tinyInteger('ban_serep')->unsigned()->nullable()->comment('1=available, 0=not available');
            $table->tinyInteger('kunci_serep')->unsigned()->nullable()->comment('1=available, 0=not available');
            $table->tinyInteger('dongkrak')->unsigned()->nullable()->comment('1=available, 0=not available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_equipment');
    }
};
