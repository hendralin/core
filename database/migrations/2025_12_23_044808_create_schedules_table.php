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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waha_session_id')->constrained('waha_sessions');
            $table->string('name')->index();
            $table->string('description');
            $table->text('message');
            $table->string('wa_id')->nullable()->index();
            $table->string('group_wa_id')->nullable()->index();
            $table->string('received_number')->nullable()->index();
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);
            $table->time('time');
            $table->integer('day_of_week')->nullable();
            $table->integer('day_of_month')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run')->nullable();
            $table->timestamp('next_run')->nullable();
            $table->text('options')->nullable();
            $table->integer('usage_count')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
