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
        Schema::create('costs', function (Blueprint $table) {
            $table->id();
            $table->enum('cost_type', ['service_parts', 'other_cost', 'showroom', 'cash', 'sales_commission'])->default('service_parts');
            $table->foreignId('vehicle_id')->nullable()->constrained();
            $table->date('cost_date');
            $table->foreignId('vendor_id')->nullable()->constrained();
            $table->foreignId('commission_id')->nullable()->onDelete('cascade')->onUpdate('restrict');
            $table->string('description');
            $table->decimal('total_price', 15, 2)->unsigned();
            $table->string('document')->nullable();
            $table->boolean('big_cash')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costs');
    }
};
