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
        Schema::create('vehicle_handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles'); // Kendaraan yang diserahkan
            $table->string('handover_number')->unique(); // Nomor Serah Terima Kendaraan
            $table->date('handover_date'); // Tanggal Serah Terima Kendaraan
            $table->string('handover_from'); // Dari
            $table->string('handover_to'); // Ke
            $table->string('transferee'); // Yang menyerahkan
            $table->string('receiving_party'); // Yang menerima
            $table->string('handover_file')->nullable(); // File tanda terima yang telah di sign kemudian diupload ke server
            $table->foreignId('created_by')->constrained('users');
            $table->tinyInteger('print_count')->unsigned()->default(0); // Count how many times the receipt has been printed
            $table->datetime('printed_at')->nullable(); // Only the first record that has been printed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_handovers');
    }
};
