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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_layout_id')->constrained()->onDelete('cascade');
            $table->string('seat_number'); // e.g., "A1", "B5", "Table 3"
            $table->string('section')->nullable(); // e.g., "VIP", "General", "Balcony"
            $table->enum('seat_type', ['individual', 'table', 'sofa', 'vip', 'wheelchair'])->default('individual');
            $table->integer('capacity')->default(1); // For tables: number of people per table
            $table->decimal('price', 10, 2); // Individual seat pricing
            $table->decimal('x_position', 8, 2); // X coordinate in layout
            $table->decimal('y_position', 8, 2); // Y coordinate in layout
            $table->decimal('width', 8, 2)->default(1.0); // Seat/table width
            $table->decimal('height', 8, 2)->default(1.0); // Seat/table height
            $table->integer('rotation')->default(0); // Rotation angle for tables
            $table->enum('status', ['available', 'reserved', 'occupied', 'maintenance', 'blocked'])->default('available');
            $table->boolean('is_accessible')->default(false); // Wheelchair accessible
            $table->json('metadata')->nullable(); // Additional seat properties (color, special features, etc.)
            $table->timestamps();

            $table->unique(['venue_layout_id', 'seat_number']);
            $table->index(['venue_layout_id', 'status']);
            $table->index(['venue_layout_id', 'section', 'seat_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
