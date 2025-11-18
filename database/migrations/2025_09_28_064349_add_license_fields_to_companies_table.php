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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('license_key')->unique()->nullable()->after('social_media');
            $table->enum('license_type', ['trial', 'basic', 'premium', 'enterprise'])->default('trial')->after('license_key');
            $table->enum('license_status', ['active', 'expired', 'suspended', 'cancelled'])->default('active')->after('license_type');
            $table->timestamp('license_issued_at')->nullable()->after('license_status');
            $table->timestamp('license_expires_at')->nullable()->after('license_issued_at');
            $table->integer('max_users')->nullable()->after('license_expires_at');
            $table->integer('max_storage_gb')->nullable()->after('max_users');
            $table->json('features_enabled')->nullable()->after('max_storage_gb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'license_key',
                'license_type',
                'license_status',
                'license_issued_at',
                'license_expires_at',
                'max_users',
                'max_storage_gb',
                'features_enabled'
            ]);
        });
    }
};
