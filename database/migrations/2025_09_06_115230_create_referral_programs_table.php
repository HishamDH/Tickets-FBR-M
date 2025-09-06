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
        Schema::create('referral_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('referrer_reward_type', ['fixed', 'percentage', 'points']);
            $table->decimal('referrer_reward_value', 10, 2);
            $table->enum('referee_reward_type', ['fixed', 'percentage', 'points']);
            $table->decimal('referee_reward_value', 10, 2);
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->integer('maximum_uses_per_referrer')->nullable();
            $table->integer('maximum_total_uses')->nullable();
            $table->integer('validity_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('merchant_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['merchant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_programs');
    }
};
