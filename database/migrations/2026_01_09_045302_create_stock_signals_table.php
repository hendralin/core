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
        Schema::create('stock_signals', function (Blueprint $table) {
            $table->id();

            // Signal information
            $table->string('signal_type')->default('value_breakthrough');
            $table->string('kode_emiten', 10);
            $table->decimal('market_cap', 20, 2)->nullable();
            $table->decimal('pbv', 8, 4)->nullable();
            $table->decimal('per', 8, 4)->nullable();

            // Data H-1 (Before)
            $table->date('before_date')->nullable();
            $table->decimal('before_value', 20, 2)->nullable();
            $table->decimal('before_close', 10, 2)->nullable();
            $table->bigInteger('before_volume')->nullable();

            // Data H (Hit/Breakthrough)
            $table->date('hit_date');
            $table->decimal('hit_value', 20, 2);
            $table->decimal('hit_close', 10, 2);
            $table->bigInteger('hit_volume');

            // Data H+1 (After)
            $table->date('after_date')->nullable();
            $table->decimal('after_value', 20, 2)->nullable();
            $table->decimal('after_close', 10, 2)->nullable();
            $table->bigInteger('after_volume')->nullable();

            // Signal management
            $table->enum('status', ['draft', 'active', 'published', 'expired', 'cancelled'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('recommendation')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index(['kode_emiten', 'hit_date']);
            $table->index(['signal_type', 'status']);
            $table->index('published_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_signals');
    }
};
