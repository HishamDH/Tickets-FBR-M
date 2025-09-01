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
        Schema::create('withdraw_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_withdraw_id')->constrained('merchant_withdraws')->onDelete('cascade');
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade');
            $table->enum('action', ['requested', 'approved', 'rejected', 'processing', 'completed', 'cancelled']);
            $table->decimal('amount', 10, 2);
            $table->string('status');
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamp('action_date');
            $table->timestamps();

            $table->index(['merchant_withdraw_id']);
            $table->index(['merchant_id', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_logs');
    }
};
