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
        Schema::create('schedule_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->enum('recipient_type', ['contact', 'group', 'number'])->index();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('cascade');
            $table->string('wa_id')->nullable()->index(); // For WAHA format (with @s.whatsapp.net)
            $table->string('group_wa_id')->nullable()->index(); // For group WAHA format
            $table->string('received_number')->nullable()->index(); // Original number format
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['schedule_id', 'recipient_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_recipients');
    }
};
