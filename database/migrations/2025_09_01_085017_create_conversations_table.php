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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->enum('type', ['customer_support', 'merchant_customer', 'internal'])->default('customer_support');
            $table->enum('status', ['active', 'archived', 'closed'])->default('active');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('merchant_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('support_agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('last_message_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
            $table->index(['merchant_id', 'status']);
            $table->index(['support_agent_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
