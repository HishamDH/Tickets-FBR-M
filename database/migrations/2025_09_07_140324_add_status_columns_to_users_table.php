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
            // Add status columns if they don't exist
            if (!Schema::hasColumn('users', 'merchant_status')) {
                $table->enum('merchant_status', ['pending', 'approved', 'rejected', 'suspended'])->nullable()->after('user_type');
            }
            
            if (!Schema::hasColumn('users', 'partner_status')) {
                $table->enum('partner_status', ['pending', 'approved', 'rejected', 'suspended'])->nullable()->after('merchant_status');
            }
            
            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('partner_status');
            }
            
            if (!Schema::hasColumn('users', 'suspension_reason')) {
                $table->text('suspension_reason')->nullable()->after('rejection_reason');
            }
            
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('suspension_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'merchant_status', 
                'partner_status', 
                'rejection_reason', 
                'suspension_reason', 
                'last_login_at'
            ]);
        });
    }
};
