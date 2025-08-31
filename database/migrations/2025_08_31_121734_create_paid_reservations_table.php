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
        Schema::create('paid_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('offerings')->onDelete('cascade'); // offering_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // customer_id
            $table->date('booking_date');
            $table->time('booking_time')->nullable();
            $table->integer('guest_count')->default(1);
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('reservation_status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paid_reservations');
    }
};
