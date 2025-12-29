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
        Schema::create('company_directors', function (Blueprint $table) {
            $table->id();
            $table->string('kode_emiten', 10)->index();
            $table->string('nama')->comment('Director name');
            $table->string('jabatan')->comment('Position: DIREKTUR UTAMA, DIREKTUR, etc.');
            $table->boolean('afiliasi')->default(false)->comment('Affiliation status');
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
        Schema::dropIfExists('company_directors');
    }
};

