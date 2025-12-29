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
        Schema::create('company_auditors', function (Blueprint $table) {
            $table->id();
            $table->string('kode_emiten', 10)->index();
            $table->string('nama')->comment('KAP name');
            $table->string('kap')->nullable()->comment('KAP code/name');
            $table->string('signing_partner')->nullable()->comment('Signing partner name');
            $table->year('tahun_buku')->nullable()->comment('Fiscal year');
            $table->date('tanggal_tahun_buku')->nullable()->comment('Fiscal year date');
            $table->date('akhir_periode')->nullable()->comment('Period end date');
            $table->date('tgl_opini')->nullable()->comment('Opinion date');
            $table->timestamps();

            $table->foreign('kode_emiten')
                ->references('kode_emiten')
                ->on('stock_companies')
                ->onDelete('cascade');

            $table->index('tahun_buku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_auditors');
    }
};

