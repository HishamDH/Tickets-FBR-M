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
            // Make service_id nullable to prepare for polymorphic relationship
            $table->unsignedBigInteger('service_id')->nullable()->change();

            // Add polymorphic relationship columns
            $table->morphs('bookable');

            // Add fields from PaidReservation
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('code')->nullable(); // For discount codes, etc.
            $table->string('reservation_status')->nullable()->after('status'); // Can be different from booking status

            // Add POS-specific fields
            $table->unsignedBigInteger('pos_terminal_id')->nullable();
            $table->json('pos_data')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->integer('print_count')->default(0);
            $table->boolean('is_offline_transaction')->default(false);
            $table->string('offline_transaction_id')->nullable();
            $table->timestamp('synced_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop polymorphic relationship columns
            $table->dropMorphs('bookable');

            // Drop fields from PaidReservation
            $table->dropColumn([
                'discount',
                'code',
                'reservation_status',
                'pos_terminal_id',
                'pos_data',
                'printed_at',
                'print_count',
                'is_offline_transaction',
                'offline_transaction_id',
                'synced_at',
            ]);

            // Revert service_id to non-nullable
            $table->unsignedBigInteger('service_id')->nullable(false)->change();
        });
    }
};
