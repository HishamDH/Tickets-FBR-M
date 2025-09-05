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
        Schema::dropIfExists('reviews');
        Schema::rename('customer_ratings', 'reviews');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('reviews', 'customer_ratings');
        // You might want to recreate the old empty 'reviews' table here if needed,
        // but for this refactoring, we assume it's not necessary.
    }
};
