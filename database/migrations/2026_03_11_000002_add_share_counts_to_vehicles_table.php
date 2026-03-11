<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedInteger('whatsapp_share_count')->default(0)->after('description');
            $table->unsignedInteger('link_copy_count')->default(0)->after('whatsapp_share_count');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_share_count', 'link_copy_count']);
        });
    }
};
