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
            // Add merchant relationship
            $table->foreignId('merchant_id')->nullable()->after('id')->constrained()->onDelete('cascade');

            // Enhanced service details
            $table->enum('service_type', ['package', 'individual', 'addon'])->default('individual')->after('category');
            $table->enum('pricing_model', ['fixed', 'per_person', 'per_hour'])->default('fixed')->after('price_type');
            $table->decimal('base_price', 10, 2)->nullable()->after('price');
            $table->string('currency', 3)->default('SAR')->after('base_price');
            $table->integer('duration_hours')->nullable()->after('currency');
            $table->integer('capacity')->nullable()->after('duration_hours');
            $table->json('features')->nullable()->after('capacity');
            $table->json('images')->nullable()->after('image');
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft')->after('is_active');
            $table->boolean('online_booking_enabled')->default(false)->after('status');
            $table->boolean('is_available')->default(true)->after('is_featured');

            // Add indexes for performance
            $table->index(['category', 'status']);
            $table->index(['merchant_id', 'online_booking_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['category', 'status']);
            $table->dropIndex(['merchant_id', 'online_booking_enabled']);
            $table->dropForeign(['merchant_id']);
            $table->dropColumn([
                'merchant_id', 'service_type', 'pricing_model', 'base_price',
                'currency', 'duration_hours', 'capacity', 'features', 'images',
                'status', 'online_booking_enabled', 'is_available',
            ]);
        });
    }
};
