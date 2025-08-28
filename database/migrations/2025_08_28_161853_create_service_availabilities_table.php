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
        Schema::create('service_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->date('availability_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('available_slots')->nullable();
            $table->integer('booked_slots')->default(0);
            $table->boolean('is_available')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Add indexes for performance with shorter names
            $table->index(['service_id', 'availability_date'], 'svc_avail_date_idx');
            $table->index(['availability_date', 'is_available'], 'date_available_idx');
            $table->unique(['service_id', 'availability_date', 'start_time'], 'svc_date_time_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_availabilities');
    }
};
