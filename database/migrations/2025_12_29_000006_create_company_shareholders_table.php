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
        Schema::create('company_shareholders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_emiten', 10)->index();
            $table->string('nama')->comment('Shareholder name');
            $table->string('kategori')->comment('Category: Lebih dari 5%, Komisaris, Direksi, Masyarakat, etc.');
            $table->unsignedBigInteger('jumlah')->default(0)->comment('Number of shares');
            $table->decimal('persentase', 10, 4)->default(0)->comment('Ownership percentage');
            $table->boolean('pengendali')->default(false)->comment('Controlling shareholder flag');
            $table->timestamps();

            $table->foreign('kode_emiten')
                ->references('kode_emiten')
                ->on('stock_companies')
                ->onDelete('cascade');

            $table->index('kategori');
            $table->index('pengendali');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_shareholders');
    }
};

