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
            if (!Schema::hasColumn('merchant_wallets', 'pending_balance')) {
                $table->decimal('pending_balance', 15, 2)->default(0)->after('balance');
            }
            if (!Schema::hasColumn('merchant_wallets', 'total_earned')) {
                $table->decimal('total_earned', 15, 2)->default(0)->after('pending_balance');
            }
            if (!Schema::hasColumn('merchant_wallets', 'total_withdrawn')) {
                $table->decimal('total_withdrawn', 15, 2)->default(0)->after('total_earned');
            }
            
            // حدود السحب
            if (!Schema::hasColumn('merchant_wallets', 'withdrawal_limit_daily')) {
                $table->decimal('withdrawal_limit_daily', 15, 2)->default(1000)->after('total_withdrawn');
            }
            if (!Schema::hasColumn('merchant_wallets', 'withdrawal_limit_monthly')) {
                $table->decimal('withdrawal_limit_monthly', 15, 2)->default(10000)->after('withdrawal_limit_daily');
            }
            if (!Schema::hasColumn('merchant_wallets', 'minimum_withdrawal')) {
                $table->decimal('minimum_withdrawal', 15, 2)->default(50)->after('withdrawal_limit_monthly');
            }
            
            // إعدادات المحفظة
            if (!Schema::hasColumn('merchant_wallets', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('minimum_withdrawal');
            }
            if (!Schema::hasColumn('merchant_wallets', 'auto_withdraw')) {
                $table->boolean('auto_withdraw')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('merchant_wallets', 'auto_withdraw_threshold')) {
                $table->decimal('auto_withdraw_threshold', 15, 2)->nullable()->after('auto_withdraw');
            }
            
            // معلومات البنك للسحب التلقائي
            if (!Schema::hasColumn('merchant_wallets', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('auto_withdraw_threshold');
            }
            if (!Schema::hasColumn('merchant_wallets', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('merchant_wallets', 'bank_routing_number')) {
                $table->string('bank_routing_number')->nullable()->after('bank_account_number');
            }
            if (!Schema::hasColumn('merchant_wallets', 'account_holder_name')) {
                $table->string('account_holder_name')->nullable()->after('bank_routing_number');
            }
            
            // إعدادات الأمان
            if (!Schema::hasColumn('merchant_wallets', 'require_verification')) {
                $table->boolean('require_verification')->default(true)->after('account_holder_name');
            }
            if (!Schema::hasColumn('merchant_wallets', 'verification_method')) {
                $table->string('verification_method')->default('email')->after('require_verification'); // email, sms, both
            }
            
            // الإحصائيات والتتبع
            if (!Schema::hasColumn('merchant_wallets', 'last_transaction_at')) {
                $table->timestamp('last_transaction_at')->nullable()->after('verification_method');
            }
            if (!Schema::hasColumn('merchant_wallets', 'last_withdrawal_at')) {
                $table->timestamp('last_withdrawal_at')->nullable()->after('last_transaction_at');
            }
            if (!Schema::hasColumn('merchant_wallets', 'transaction_count')) {
                $table->integer('transaction_count')->default(0)->after('last_withdrawal_at');
            }
            if (!Schema::hasColumn('merchant_wallets', 'withdrawal_count')) {
                $table->integer('withdrawal_count')->default(0)->after('transaction_count');
            }
        });
        
        // إضافة الفهارس في خطوة منفصلة
        Schema::table('merchant_wallets', function (Blueprint $table) {
            if (!$this->indexExists('merchant_wallets', 'merchant_wallets_is_active_index')) {
                $table->index('is_active');
            }
            if (!$this->indexExists('merchant_wallets', 'merchant_wallets_auto_withdraw_index')) {
                $table->index('auto_withdraw');
            }
            if (!$this->indexExists('merchant_wallets', 'merchant_wallets_user_id_is_active_index')) {
                $table->index(['user_id', 'is_active']);
            }
            if (!$this->indexExists('merchant_wallets', 'merchant_wallets_last_transaction_at_index')) {
                $table->index('last_transaction_at');
            }
        });
    }

    private function indexExists($table, $name): bool
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $sm->listTableIndexes($table);
        return isset($indexes[$name]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchant_wallets', function (Blueprint $table) {
            // إزالة الفهارس أولاً إذا كانت موجودة
            if ($this->indexExists('merchant_wallets', 'merchant_wallets_is_active_index')) {
                $table->dropIndex(['merchant_wallets_is_active_index']);
            }
            if ($this->indexExists('merchant_wallets', 'merchant_wallets_auto_withdraw_index')) {
                $table->dropIndex(['merchant_wallets_auto_withdraw_index']);
            }
            if ($this->indexExists('merchant_wallets', 'merchant_wallets_user_id_is_active_index')) {
                $table->dropIndex(['merchant_wallets_user_id_is_active_index']);
            }
            if ($this->indexExists('merchant_wallets', 'merchant_wallets_last_transaction_at_index')) {
                $table->dropIndex(['merchant_wallets_last_transaction_at_index']);
            }
        });
        
        Schema::table('merchant_wallets', function (Blueprint $table) {
            // إزالة الأعمدة المضافة
            $columns = [
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
            ];
            
            // فقط إزالة الأعمدة الموجودة
            $existingColumns = [];
            foreach ($columns as $column) {
                if (Schema::hasColumn('merchant_wallets', $column)) {
                    $existingColumns[] = $column;
                }
            }
            
            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
