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
        Schema::create('goapi_stock_prices', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 5)->index();
            $table->timestamp('date');
            $table->decimal('open', 10, 2);
            $table->decimal('high', 10, 2);
            $table->decimal('low', 10, 2);
            $table->decimal('close', 10, 2);
            $table->decimal('volume', 20, 2);
            $table->decimal('change', 10, 2);
            $table->decimal('change_pct', 10, 2);
            $table->decimal('value', 20, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goapi_stock_prices');
    }
};
