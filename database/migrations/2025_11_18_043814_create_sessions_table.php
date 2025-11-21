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
        Schema::create('waha_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Session name
            $table->string('session_id')->unique(); // WAHA session ID
            $table->boolean('is_active')->unsigned()->default(true); // Whether session is active
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waha_sessions');
    }
};
