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
            if (!Schema::hasColumn('users', 'f_name')) {
                $table->string('f_name')->after('id');
            }
            if (!Schema::hasColumn('users', 'l_name')) {
                $table->string('l_name')->after('f_name');
            }
            if (!Schema::hasColumn('users', 'business_name')) {
                $table->string('business_name')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'business_type')) {
                $table->enum('business_type', ['restaurant', 'events', 'show', 'other'])->default('other')->after('business_name');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'merchant', 'user'])->default('user')->after('password');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'additional_data')) {
                $table->json('additional_data')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'is_accepted')) {
                $table->boolean('is_accepted')->default(false)->after('additional_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['f_name', 'l_name', 'business_name', 'business_type', 'role', 'phone', 'additional_data', 'is_accepted']);
        });
    }
};
