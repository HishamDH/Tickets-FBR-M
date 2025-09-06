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
        Schema::create('partner_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->string('goal_type', 50); // new_merchants, commission_earned, total_revenue, active_merchants, etc.
            $table->decimal('target_value', 12, 2);
            $table->decimal('current_value', 12, 2)->default(0);
            $table->timestamp('target_date');
            $table->text('description')->nullable();
            $table->decimal('reward_amount', 10, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'expired', 'cancelled'])->default('active');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['partner_id', 'status']);
            $table->index(['status', 'target_date']);
            $table->index('goal_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_goals');
    }
};
