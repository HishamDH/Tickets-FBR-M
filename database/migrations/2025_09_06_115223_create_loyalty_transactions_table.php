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
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('loyalty_program_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['earn', 'redeem']);
            $table->integer('points');
            $table->text('description')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'loyalty_program_id']);
            $table->index(['type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};
