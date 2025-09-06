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
        Schema::create('partner_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_wallet_id')->constrained('partner_wallets')->onDelete('cascade');
            $table->string('transaction_reference')->unique();
            $table->enum('type', ['commission', 'withdrawal', 'adjustment', 'referral_bonus', 'deduction']);
            $table->enum('category', ['merchant_referral', 'booking_commission', 'bonus', 'penalty', 'withdrawal', 'adjustment']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // معلومات إضافية مثل merchant_id, booking_id, etc.
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            // فهارس
            $table->index('partner_wallet_id');
            $table->index('type');
            $table->index('status');
            $table->index('created_at');
            $table->index(['partner_wallet_id', 'type']);
            $table->index(['partner_wallet_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_wallet_transactions');
    }
};
