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
        // إضافة فهارس لتحسين أداء نظام الشركاء
        
        // فهارس جدول partner_wallets
        Schema::table('partner_wallets', function (Blueprint $table) {
            if (!Schema::hasIndex('partner_wallets', 'idx_partner_created')) {
                $table->index(['partner_id', 'created_at'], 'idx_partner_created');
            }
        });

        // فهارس جدول partner_wallet_transactions
        Schema::table('partner_wallet_transactions', function (Blueprint $table) {
            if (!Schema::hasIndex('partner_wallet_transactions', 'idx_transaction_reference')) {
                $table->index('transaction_reference', 'idx_transaction_reference');
            }
        });

        // فهارس جدول partner_withdraws
        Schema::table('partner_withdraws', function (Blueprint $table) {
            if (!Schema::hasIndex('partner_withdraws', 'idx_status_requested')) {
                $table->index(['status', 'requested_at'], 'idx_status_requested');
            }
            if (!Schema::hasIndex('partner_withdraws', 'idx_wallet_status')) {
                $table->index(['partner_wallet_id', 'status'], 'idx_wallet_status');
            }
        });

        // فهارس جدول partner_invitations
        Schema::table('partner_invitations', function (Blueprint $table) {
            if (!Schema::hasIndex('partner_invitations', 'idx_status_expires')) {
                $table->index(['status', 'expires_at'], 'idx_status_expires');
            }
            if (!Schema::hasIndex('partner_invitations', 'idx_email_status')) {
                $table->index(['email', 'status'], 'idx_email_status');
            }
        });

        // فهارس جدول merchants للشركاء
        Schema::table('merchants', function (Blueprint $table) {
            if (!Schema::hasIndex('merchants', 'idx_partner_verification')) {
                $table->index(['partner_id', 'verification_status'], 'idx_partner_verification');
            }
        });

        // فهارس جدول partners
        Schema::table('partners', function (Blueprint $table) {
            if (!Schema::hasIndex('partners', 'idx_status_created')) {
                $table->index(['status', 'created_at'], 'idx_status_created');
            }
            if (!Schema::hasIndex('partners', 'idx_commission_rate')) {
                $table->index('commission_rate', 'idx_commission_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إزالة الفهارس
        
        Schema::table('partner_wallets', function (Blueprint $table) {
            if (Schema::hasIndex('partner_wallets', 'idx_partner_created')) {
                $table->dropIndex('idx_partner_created');
            }
        });

        Schema::table('partner_wallet_transactions', function (Blueprint $table) {
            if (Schema::hasIndex('partner_wallet_transactions', 'idx_transaction_reference')) {
                $table->dropIndex('idx_transaction_reference');
            }
        });

        Schema::table('partner_withdraws', function (Blueprint $table) {
            if (Schema::hasIndex('partner_withdraws', 'idx_status_requested')) {
                $table->dropIndex('idx_status_requested');
            }
            if (Schema::hasIndex('partner_withdraws', 'idx_wallet_status')) {
                $table->dropIndex('idx_wallet_status');
            }
        });

        Schema::table('partner_invitations', function (Blueprint $table) {
            if (Schema::hasIndex('partner_invitations', 'idx_status_expires')) {
                $table->dropIndex('idx_status_expires');
            }
            if (Schema::hasIndex('partner_invitations', 'idx_email_status')) {
                $table->dropIndex('idx_email_status');
            }
        });

        Schema::table('merchants', function (Blueprint $table) {
            if (Schema::hasIndex('merchants', 'idx_partner_verification')) {
                $table->dropIndex('idx_partner_verification');
            }
        });

        Schema::table('partners', function (Blueprint $table) {
            if (Schema::hasIndex('partners', 'idx_status_created')) {
                $table->dropIndex('idx_status_created');
            }
            if (Schema::hasIndex('partners', 'idx_commission_rate')) {
                $table->dropIndex('idx_commission_rate');
            }
        });
    }
};
