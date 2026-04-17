<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedInteger('session_lifetime_minutes')
                ->default(480)
                ->after('max_storage_gb');
        });

        DB::table('companies')
            ->whereNull('session_lifetime_minutes')
            ->update(['session_lifetime_minutes' => 480]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('session_lifetime_minutes');
        });
    }
};
