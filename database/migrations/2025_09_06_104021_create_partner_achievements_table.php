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
        Schema::create('partner_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->foreignId('goal_id')->nullable()->constrained('partner_goals')->onDelete('set null');
            $table->string('achievement_type', 50); // goal_completion, milestone, streak, special_event, performance_bonus
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('reward_amount', 10, 2)->default(0);
            $table->timestamp('achieved_at');
            $table->string('badge_icon', 100)->nullable();
            $table->string('badge_color', 20)->default('yellow');
            $table->timestamps();

            // Indexes for performance
            $table->index(['partner_id', 'achieved_at']);
            $table->index('achievement_type');
            $table->index('achieved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_achievements');
    }
};
