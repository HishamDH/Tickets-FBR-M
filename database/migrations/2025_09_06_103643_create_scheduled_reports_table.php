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
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->string('report_type', 50)->default('comprehensive'); // comprehensive, financial, performance, merchants, commission
            $table->string('frequency', 20)->default('monthly'); // daily, weekly, monthly, quarterly, yearly
            $table->json('options')->nullable(); // Report configuration options
            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->integer('run_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for performance
            $table->index(['is_active', 'next_run_at']);
            $table->index(['partner_id', 'frequency']);
            $table->index(['report_type', 'frequency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_reports');
    }
};
