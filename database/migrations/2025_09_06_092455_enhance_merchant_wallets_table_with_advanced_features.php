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
        Schema::table('merchant_wallets', function (Blueprint $table) {
            // الأرصدة المتقدمة
            $table->decimal('pending_balance', 15, 2)->default(0)->after('balance');
            $table->decimal('total_earned', 15, 2)->default(0)->after('pending_balance');
            $table->decimal('total_withdrawn', 15, 2)->default(0)->after('total_earned');
            
            // حدود السحب
            $table->decimal('withdrawal_limit_daily', 15, 2)->default(1000)->after('total_withdrawn');
            $table->decimal('withdrawal_limit_monthly', 15, 2)->default(10000)->after('withdrawal_limit_daily');
            $table->decimal('minimum_withdrawal', 15, 2)->default(50)->after('withdrawal_limit_monthly');
            
            // إعدادات المحفظة
            $table->boolean('is_active')->default(true)->after('minimum_withdrawal');
            $table->boolean('auto_withdraw')->default(false)->after('is_active');
            $table->decimal('auto_withdraw_threshold', 15, 2)->nullable()->after('auto_withdraw');
            
            // معلومات البنك للسحب التلقائي
            $table->string('bank_name')->nullable()->after('auto_withdraw_threshold');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_routing_number')->nullable()->after('bank_account_number');
            $table->string('account_holder_name')->nullable()->after('bank_routing_number');
            
            // إعدادات الأمان
            $table->boolean('require_verification')->default(true)->after('account_holder_name');
            $table->string('verification_method')->default('email')->after('require_verification'); // email, sms, both
            
            // الإحصائيات والتتبع
            $table->timestamp('last_transaction_at')->nullable()->after('verification_method');
            $table->timestamp('last_withdrawal_at')->nullable()->after('last_transaction_at');
            $table->integer('transaction_count')->default(0)->after('last_withdrawal_at');
            $table->integer('withdrawal_count')->default(0)->after('transaction_count');
        });
        
        // إضافة الفهارس في خطوة منفصلة
        Schema::table('merchant_wallets', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('auto_withdraw');
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchant_wallets', function (Blueprint $table) {
            // إزالة الفهارس أولاً
            $table->dropIndex(['merchant_wallets_is_active_index']);
            $table->dropIndex(['merchant_wallets_auto_withdraw_index']);
            $table->dropIndex(['merchant_wallets_user_id_is_active_index']);
        });
        
        Schema::table('merchant_wallets', function (Blueprint $table) {
            // إزالة الأعمدة
            $table->dropColumn([
                'pending_balance',
                'total_earned',
                'total_withdrawn',
                'withdrawal_limit_daily',
                'withdrawal_limit_monthly',
                'minimum_withdrawal',
                'is_active',
                'auto_withdraw',
                'auto_withdraw_threshold',
                'bank_name',
                'bank_account_number',
                'bank_routing_number',
                'account_holder_name',
                'require_verification',
                'verification_method',
                'last_transaction_at',
                'last_withdrawal_at',
                'transaction_count',
                'withdrawal_count'
            ]);
        });
    }
};
