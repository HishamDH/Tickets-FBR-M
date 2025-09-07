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
        Schema::table('bookings', function (Blueprint $table) {
            // Add service_id column for backward compatibility
            $table->foreignId('service_id')->nullable()->after('merchant_id')->constrained('services')->onDelete('cascade');
            
            // Add index for better performance
            $table->index(['service_id', 'payment_status']);
            $table->index(['service_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropIndex(['service_id', 'payment_status']);
            $table->dropIndex(['service_id', 'status']);
            $table->dropColumn('service_id');
        });
    }
};
