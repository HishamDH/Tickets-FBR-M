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
        Schema::create('merchant_withdraws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'approved', 'processing', 'completed', 'rejected', 'cancelled'])
                ->default('pending');
            $table->json('bank_details'); // Store bank account info
            $table->string('transaction_id')->nullable(); // Bank transaction reference
            $table->text('notes')->nullable(); // Merchant notes
            $table->text('admin_notes')->nullable(); // Admin notes for rejection/approval
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['merchant_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_withdraws');
    }
};
