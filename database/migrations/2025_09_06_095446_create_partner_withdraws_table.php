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
        Schema::create('partner_withdraws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_wallet_id')->constrained('partner_wallets')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'processing', 'completed', 'rejected', 'cancelled'])->default('pending');
            $table->json('bank_details')->nullable(); // تفاصيل البنك
            $table->string('transaction_id')->nullable(); // رقم المعاملة من البنك
            $table->text('notes')->nullable(); // ملاحظات الشريك
            $table->text('admin_notes')->nullable(); // ملاحظات الإدارة
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            // فهارس
            $table->index('partner_wallet_id');
            $table->index('status');
            $table->index('requested_at');
            $table->index(['partner_wallet_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_withdraws');
    }
};
