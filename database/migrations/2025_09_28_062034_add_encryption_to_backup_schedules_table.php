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
        Schema::table('backup_schedules', function (Blueprint $table) {
            $table->boolean('encryption_enabled')->default(false)->after('description');
            $table->string('encryption_password')->nullable()->after('encryption_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backup_schedules', function (Blueprint $table) {
            $table->dropColumn(['encryption_enabled', 'encryption_password']);
        });
    }
};
