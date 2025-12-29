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
        Schema::create('stock_companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('source_id')->nullable()->comment('Original id from source API');
            $table->unsignedInteger('data_id')->nullable()->comment('DataID from source');
            $table->string('kode_emiten', 10)->unique()->comment('Stock code, e.g., AADI');
            $table->string('nama_emiten')->comment('Company name');
            $table->text('alamat')->nullable()->comment('Company address');
            $table->string('bae')->nullable()->comment('Biro Administrasi Efek');
            $table->string('divisi')->nullable();
            $table->string('kode_divisi')->nullable();
            $table->string('jenis_emiten')->nullable();
            $table->text('kegiatan_usaha_utama')->nullable()->comment('Main business activities');

            // Efek Emiten flags
            $table->boolean('efek_emiten_eba')->default(false);
            $table->boolean('efek_emiten_etf')->default(false);
            $table->boolean('efek_emiten_obligasi')->default(false);
            $table->boolean('efek_emiten_saham')->default(false);
            $table->boolean('efek_emiten_spei')->default(false);

            // Sector & Industry
            $table->string('sektor')->nullable();
            $table->string('sub_sektor')->nullable();
            $table->string('industri')->nullable();
            $table->string('sub_industri')->nullable();

            // Contact information
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('website')->nullable();

            // Tax information
            $table->string('npkp', 50)->nullable();
            $table->string('npwp', 50)->nullable();

            // Listing information
            $table->string('papan_pencatatan', 50)->nullable()->comment('Listing board: Utama, Pengembangan, etc.');
            $table->date('tanggal_pencatatan')->nullable()->comment('Listing date');

            // Status & Logo
            $table->tinyInteger('status')->default(0);
            $table->string('logo')->nullable()->comment('Logo URL path');

            $table->timestamps();

            // Indexes
            $table->index('sektor');
            $table->index('sub_sektor');
            $table->index('industri');
            $table->index('status');
            $table->index('tanggal_pencatatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_companies');
    }
};

