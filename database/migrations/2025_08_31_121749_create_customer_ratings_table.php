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
        Schema::create('customer_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('offerings')->onDelete('cascade'); // offering_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // customer_id
            $table->integer('rating')->unsigned()->comment('Rating from 1 to 5');
            $table->text('review')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_ratings');
    }
};
