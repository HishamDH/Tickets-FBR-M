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
        Schema::create('seat_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('seat_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['reserved', 'confirmed', 'cancelled', 'no_show'])->default('reserved');
            $table->decimal('amount_paid', 10, 2); // Amount paid for this specific seat
            $table->text('special_requirements')->nullable(); // Dietary, accessibility requirements
            $table->timestamp('reserved_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Temporary reservation expiry
            $table->timestamps();

            $table->unique(['booking_id', 'seat_id']); // One seat per booking
            $table->index(['seat_id', 'status']);
            $table->index(['booking_id', 'status']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_reservations');
    }
};
