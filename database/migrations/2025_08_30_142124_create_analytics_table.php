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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_name');
            $table->string('metric_type'); // 'kpi', 'chart_data', 'insight', 'alert'
            $table->json('metric_data');
            $table->string('period'); // 'hourly', 'daily', 'weekly', 'monthly'
            $table->date('metric_date');
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            $table->index(['metric_name', 'metric_date']);
            $table->index(['metric_type', 'period']);
        });

        // Create analytics_cache table for performance
        Schema::create('analytics_cache', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key')->unique();
            $table->longText('cache_data');
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index('expires_at');
        });

        // Create analytics_alerts table
        Schema::create('analytics_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('alert_type'); // 'performance', 'revenue', 'system'
            $table->string('severity'); // 'low', 'medium', 'high', 'critical'
            $table->string('title');
            $table->text('message');
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_dismissed')->default(false);
            $table->timestamp('triggered_at');
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamps();
            
            $table->index(['alert_type', 'severity']);
            $table->index('is_active');
        });

        // Create analytics_exports table
        Schema::create('analytics_exports', function (Blueprint $table) {
            $table->id();
            $table->string('export_type'); // 'pdf', 'excel', 'csv'
            $table->string('report_type'); // 'dashboard', 'revenue', 'customers', 'merchants', 'operations'
            $table->json('filters')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status'); // 'pending', 'processing', 'completed', 'failed'
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('requested_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_exports');
        Schema::dropIfExists('analytics_alerts');
        Schema::dropIfExists('analytics_cache');
        Schema::dropIfExists('analytics');
    }
};
