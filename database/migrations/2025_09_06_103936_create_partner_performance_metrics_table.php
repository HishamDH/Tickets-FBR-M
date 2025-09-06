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
        Schema::create('partner_performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->year('year');
            $table->tinyInteger('month')->unsigned();
            
            // Financial metrics
            $table->decimal('commission_earned', 10, 2)->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->decimal('avg_revenue_per_merchant', 10, 2)->default(0);
            
            // Merchant metrics
            $table->integer('new_merchants_count')->default(0);
            $table->integer('active_merchants_count')->default(0);
            $table->integer('total_bookings')->default(0);
            
            // Performance metrics
            $table->decimal('conversion_rate', 5, 2)->default(0); // Percentage
            $table->decimal('retention_rate', 5, 2)->default(0); // Percentage
            $table->decimal('quality_score', 5, 2)->default(0); // 0-100 scale
            $table->decimal('performance_score', 5, 2)->default(0); // Overall 0-100 scale
            
            $table->timestamps();

            // Indexes for performance
            $table->unique(['partner_id', 'year', 'month']);
            $table->index(['year', 'month']);
            $table->index('performance_score');
            $table->index('commission_earned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_performance_metrics');
    }
};
