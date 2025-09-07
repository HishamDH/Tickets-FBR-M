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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique();
            $table->uuid('qr_code')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->morphs('bookable');
            $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
            $table->date('booking_date');
            $table->time('booking_time');
            $table->integer('guest_count');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->string('payment_status')->default('pending');
            $table->string('status')->default('pending');
            $table->string('booking_source')->nullable();
            $table->text('special_requests')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->integer('number_of_people')->nullable();
            $table->integer('number_of_tables')->nullable();
            $table->integer('duration_hours')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
