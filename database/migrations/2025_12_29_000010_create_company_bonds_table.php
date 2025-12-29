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
        Schema::create('company_bonds', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('source_id')->nullable()->comment('Original id from source');
            $table->string('kode_emiten', 10)->index();
            $table->string('nama_emisi')->comment('Bond/Sukuk name');
            $table->string('isin_code', 50)->nullable()->comment('ISIN Code');
            $table->date('listing_date')->nullable()->comment('Listing date');
            $table->date('mature_date')->nullable()->comment('Maturity date');
            $table->string('rating', 50)->nullable()->comment('Bond rating: AAA, AA+, etc.');
            $table->decimal('nominal', 20, 2)->nullable()->comment('Nominal value');
            $table->string('margin', 50)->nullable()->comment('Margin/Coupon rate');
            $table->string('wali_amanat')->nullable()->comment('Trustee');
            $table->timestamps();

            $table->foreign('kode_emiten')
                ->references('kode_emiten')
                ->on('stock_companies')
                ->onDelete('cascade');

            $table->index('listing_date');
            $table->index('mature_date');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_bonds');
    }
};

