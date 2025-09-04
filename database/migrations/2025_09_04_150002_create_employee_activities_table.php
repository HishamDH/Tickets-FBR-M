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
        Schema::create('employee_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_employee_id')->constrained()->onDelete('cascade');
            $table->string('activity_type'); // login, logout, sale, booking, refund, etc.
            $table->text('description');
            $table->json('metadata')->nullable(); // Additional data about the activity
            $table->decimal('amount', 10, 2)->nullable(); // For monetary activities
            $table->timestamp('activity_time');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['merchant_employee_id', 'activity_time']);
            $table->index(['activity_type']);
            $table->index(['activity_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_activities');
    }
};
