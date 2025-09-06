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
        Schema::create('partner_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('pending_balance', 15, 2)->default(0);
            $table->decimal('total_earned', 15, 2)->default(0);
            $table->decimal('total_withdrawn', 15, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            
            // إعدادات السحب
            $table->decimal('withdrawal_limit_daily', 15, 2)->default(1000);
            $table->decimal('withdrawal_limit_monthly', 15, 2)->default(10000);
            $table->decimal('minimum_withdrawal', 15, 2)->default(50);
            
            // إعدادات المحفظة
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_withdraw')->default(false);
            $table->decimal('auto_withdraw_threshold', 15, 2)->nullable();
            
            // معلومات البنك
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_routing_number')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('swift_code')->nullable();
            
            // إعدادات الأمان
            $table->boolean('require_verification')->default(true);
            $table->string('verification_method')->default('email');
            
            // الإحصائيات
            $table->timestamp('last_transaction_at')->nullable();
            $table->timestamp('last_withdrawal_at')->nullable();
            $table->integer('transaction_count')->default(0);
            $table->integer('withdrawal_count')->default(0);
            $table->integer('referral_count')->default(0);
            
            $table->timestamps();
            
            // فهارس
            $table->index('partner_id');
            $table->index('is_active');
            $table->index(['partner_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_wallets');
    }
};
