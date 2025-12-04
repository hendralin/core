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
        Schema::create('vehicle_certificate_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained();
            $table->string('certificate_receipt_number')->unique();
            $table->string('in_the_name_of'); // BPKB A/N
            $table->string('original_invoice_name'); // Faktur asli A/N
            $table->string('photocopy_id_card_name'); // Fotocopy KTP A/N
            $table->string('receipt_form'); // Blangko Kwitansi
            $table->string('nik'); // NIK
            $table->string('form_a'); // Form A
            $table->string('release_of_title_letter'); // Surat Pelepasan Hak
            $table->string('others')->nullable(); // Dll
            $table->date('receipt_date'); // Tanggal tanda terima
            $table->string('transferee'); // Yang menyerahkan
            $table->string('receiving_party'); // Yang menerima
            $table->string('receipt_file')->nullable(); // File tanda terima yang telah di sign kemudian diupload ke server
            $table->foreignId('created_by')->constrained('users');
            $table->tinyInteger('print_count')->unsigned()->default(0); // Count how many times the receipt has been printed
            $table->datetime('printed_at')->nullable(); // Hanya record pertama yang dicetak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_registration_certificate_receipts');
    }
};
