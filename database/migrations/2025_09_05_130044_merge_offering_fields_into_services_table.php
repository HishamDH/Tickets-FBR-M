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
        Schema::table('services', function (Blueprint $table) {
            // Add fields from Offering model
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->json('additional_data')->nullable();
            $table->json('translations')->nullable();
            $table->boolean('has_chairs')->default(false);
            $table->integer('chairs_count')->nullable();
            $table->integer('max_capacity')->nullable();
            $table->integer('min_capacity')->nullable();
            $table->boolean('allow_overbooking')->default(false);
            $table->decimal('overbooking_percentage', 5, 2)->default(0);
            $table->string('capacity_type')->default('fixed');
            $table->integer('buffer_capacity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'start_time',
                'end_time',
                'additional_data',
                'translations',
                'has_chairs',
                'chairs_count',
                'max_capacity',
                'min_capacity',
                'allow_overbooking',
                'overbooking_percentage',
                'capacity_type',
                'buffer_capacity',
            ]);
        });
    }
};
