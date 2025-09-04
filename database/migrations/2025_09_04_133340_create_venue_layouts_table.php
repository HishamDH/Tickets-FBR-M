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
        Schema::create('venue_layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('layout_type', ['theater', 'banquet', 'classroom', 'conference', 'u_shape', 'custom'])->default('theater');
            $table->integer('total_capacity');
            $table->json('layout_config'); // Stores the visual layout configuration
            $table->decimal('width', 8, 2); // Layout width in meters or pixels
            $table->decimal('height', 8, 2); // Layout height in meters or pixels
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_layouts');
    }
};
