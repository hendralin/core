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
        Schema::create('idx_news', function (Blueprint $table) {
            $table->id();
            $table->string('item_id')->unique();
            $table->datetime('published_date');
            $table->string('image_url')->nullable();
            $table->string('locale', 10)->default('en-us');
            $table->string('title');
            $table->string('path_base')->nullable();
            $table->string('path_file')->nullable();
            $table->string('tags')->nullable();
            $table->boolean('is_headline')->default(false);
            $table->text('summary')->nullable();
            $table->longText('contents')->nullable(); // For detailed news content
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idx_news');
    }
};
