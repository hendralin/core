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
        Schema::create('backup_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama schedule
            $table->string('frequency'); // daily, weekly, monthly
            $table->time('time')->nullable(); // Waktu eksekusi
            $table->integer('day_of_week')->nullable(); // 0-6 untuk weekly (0=Sunday)
            $table->integer('day_of_month')->nullable(); // 1-31 untuk monthly
            $table->boolean('is_active')->default(true); // Status aktif/non-aktif
            $table->timestamp('last_run')->nullable(); // Waktu terakhir dijalankan
            $table->timestamp('next_run')->nullable(); // Waktu berikutnya akan dijalankan
            $table->text('description')->nullable(); // Deskripsi
            $table->json('options')->nullable(); // Opsi tambahan (retention, dll)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_schedules');
    }
};
