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
        Schema::create('company_dividends', function (Blueprint $table) {
            $table->id();
            $table->string('kode_emiten', 10)->index();
            $table->string('nama')->nullable()->comment('Company name');
            $table->string('jenis', 50)->nullable()->comment('Dividend type: dt (tunai), ds (saham)');
            $table->string('tahun_buku', 10)->nullable()->comment('Fiscal year');
            $table->unsignedBigInteger('total_saham_bonus')->default(0)->comment('Total bonus shares');
            $table->string('cash_dividen_per_saham_mu', 10)->nullable()->comment('Currency for cash dividend per share');
            $table->decimal('cash_dividen_per_saham', 15, 2)->nullable()->comment('Cash dividend per share');
            $table->string('cash_dividen_total_mu', 10)->nullable()->comment('Currency for total cash dividend');
            $table->decimal('cash_dividen_total', 20, 2)->nullable()->comment('Total cash dividend');
            $table->date('tanggal_cum')->nullable()->comment('Cum date');
            $table->date('tanggal_ex_reguler_dan_negosiasi')->nullable()->comment('Ex date for regular and negotiation');
            $table->date('tanggal_dps')->nullable()->comment('DPS date');
            $table->date('tanggal_pembayaran')->nullable()->comment('Payment date');
            $table->integer('rasio1')->default(0)->comment('Ratio 1 for stock dividend');
            $table->integer('rasio2')->default(0)->comment('Ratio 2 for stock dividend');
            $table->timestamps();

            $table->foreign('kode_emiten')
                ->references('kode_emiten')
                ->on('stock_companies')
                ->onDelete('cascade');

            $table->index('tahun_buku');
            $table->index('tanggal_cum');
            $table->index('tanggal_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_dividends');
    }
};

