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
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['referrer', 'referee']);
            $table->enum('reward_type', ['fixed', 'percentage', 'points']);
            $table->decimal('reward_value', 10, 2);
            $table->enum('status', ['pending', 'processed', 'cancelled'])->default('pending');
            $table->datetime('processed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['referral_id', 'type']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_rewards');
    }
};
