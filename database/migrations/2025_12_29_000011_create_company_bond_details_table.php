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
        Schema::create('company_bond_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('source_id')->nullable()->comment('Original id from source');
            $table->string('kode_emiten', 10)->index();
            $table->string('nama_seri')->nullable()->comment('Series name');
            $table->string('amortisasi_value')->nullable()->comment('Amortization value');
            $table->string('sinking_fund')->nullable()->comment('Sinking fund');
            $table->string('coupon_detail')->nullable()->comment('Coupon details');
            $table->date('coupon_payment_detail')->nullable()->comment('Coupon payment date');
            $table->date('mature_date')->nullable()->comment('Maturity date');
            $table->timestamps();

            $table->foreign('kode_emiten')
                ->references('kode_emiten')
                ->on('stock_companies')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_bond_details');
    }
};

