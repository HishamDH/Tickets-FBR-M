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
        Schema::table('users', function (Blueprint $table) {
            $table->json('notification_preferences')->nullable()->after('email_verified_at');
            $table->boolean('push_notifications_enabled')->default(true)->after('notification_preferences');
            $table->string('push_token')->nullable()->after('push_notifications_enabled');
            $table->timestamp('last_notification_read_at')->nullable()->after('push_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notification_preferences',
                'push_notifications_enabled',
                'push_token',
                'last_notification_read_at'
            ]);
        });
    }
};
