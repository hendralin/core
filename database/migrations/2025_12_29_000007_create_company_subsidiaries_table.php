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
        Schema::create('company_subsidiaries', function (Blueprint $table) {
            $table->id();
            $table->string('kode_emiten', 10)->index();
            $table->string('nama')->comment('Subsidiary company name');
            $table->text('bidang_usaha')->nullable()->comment('Business field');
            $table->string('lokasi')->nullable()->comment('Location/Country');
            $table->decimal('persentase', 10, 2)->nullable()->comment('Ownership percentage');
            $table->decimal('jumlah_aset', 20, 2)->nullable()->comment('Total assets');
            $table->string('mata_uang', 10)->nullable()->comment('Currency: USD, IDR, etc.');
            $table->string('satuan', 20)->nullable()->comment('Unit: RIBUAN, JUTAAN, etc.');
            $table->string('status_operasi', 100)->nullable()->comment('Operating status: Aktif, Tidak aktif');
            $table->string('tahun_komersil', 10)->nullable()->comment('Commercial year');
            $table->timestamps();

            $table->foreign('kode_emiten')
                ->references('kode_emiten')
                ->on('stock_companies')
                ->onDelete('cascade');

            $table->index('lokasi');
            $table->index('status_operasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_subsidiaries');
    }
};

