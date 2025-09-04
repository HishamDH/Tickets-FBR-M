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
        Schema::table('paid_reservations', function (Blueprint $table) {
            // POS-specific fields
            $table->string('pos_terminal_id')->nullable()->after('qr_code');
            $table->json('pos_data')->nullable()->after('pos_terminal_id');
            $table->timestamp('printed_at')->nullable()->after('pos_data');
            $table->integer('print_count')->default(0)->after('printed_at');
            $table->boolean('is_offline_transaction')->default(false)->after('print_count');
            $table->string('offline_transaction_id')->nullable()->after('is_offline_transaction');
            $table->timestamp('synced_at')->nullable()->after('offline_transaction_id');
            
            // Indexes for better performance
            $table->index(['pos_terminal_id', 'created_at']);
            $table->index(['is_offline_transaction', 'synced_at']);
            $table->index('offline_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paid_reservations', function (Blueprint $table) {
            $table->dropIndex(['pos_terminal_id', 'created_at']);
            $table->dropIndex(['is_offline_transaction', 'synced_at']);
            $table->dropIndex('offline_transaction_id');
            
            $table->dropColumn([
                'pos_terminal_id',
                'pos_data',
                'printed_at',
                'print_count',
                'is_offline_transaction',
                'offline_transaction_id',
                'synced_at',
            ]);
        });
    }
};
