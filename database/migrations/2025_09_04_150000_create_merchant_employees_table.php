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
        Schema::create('merchant_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->string('role')->default('staff'); // manager, supervisor, staff, cashier
            $table->json('permissions')->nullable(); // Custom permissions array
            $table->string('employee_code')->unique(); // Unique employee ID
            $table->decimal('hourly_rate', 8, 2)->nullable(); // Pay rate
            $table->string('status')->default('active'); // active, inactive, suspended
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('schedule')->nullable(); // Working schedule
            $table->boolean('can_process_payments')->default(false);
            $table->boolean('can_manage_bookings')->default(false);
            $table->boolean('can_view_reports')->default(false);
            $table->boolean('can_manage_services')->default(false);
            $table->boolean('can_manage_inventory')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['merchant_id', 'status']);
            $table->index(['employee_id']);
            $table->index(['role']);
            $table->unique(['merchant_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_employees');
    }
};
