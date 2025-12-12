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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waha_session_id')->constrained();
            $table->foreignId('template_id')->nullable()->constrained();
            $table->string('wa_id')->nullable()->index();
            $table->string('group_wa_id')->nullable()->index();
            $table->string('received_number')->nullable()->index();
            $table->text('message');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
