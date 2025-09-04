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
        Schema::create('employee_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_employee_id')->constrained()->onDelete('cascade');
            $table->date('shift_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('actual_start_time')->nullable();
            $table->time('actual_end_time')->nullable();
            $table->decimal('break_duration', 4, 2)->default(0); // Hours
            $table->text('notes')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, in_progress, completed, cancelled
            $table->decimal('total_hours', 4, 2)->nullable();
            $table->decimal('overtime_hours', 4, 2)->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['merchant_employee_id', 'shift_date']);
            $table->index(['shift_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_shifts');
    }
};
