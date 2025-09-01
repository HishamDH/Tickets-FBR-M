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
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->enum('user_type', ['customer', 'merchant', 'partner', 'admin'])->default('customer')->after('avatar');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('user_type');
            $table->string('language', 5)->default('ar')->after('status');
            $table->string('timezone', 50)->default('Asia/Riyadh')->after('language');

            $table->index(['user_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['user_type', 'status']);
            $table->dropColumn([
                'phone', 'avatar', 'user_type', 'status', 'language', 'timezone',
            ]);
        });
    }
};
