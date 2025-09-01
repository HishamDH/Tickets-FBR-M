<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add booking system fields (only new columns)
            $table->string('booking_number', 20)->unique()->after('id');
            $table->foreignId('customer_id')->after('booking_number')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->after('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('merchant_id')->after('service_id')->constrained()->onDelete('cascade');
            $table->time('booking_time')->nullable()->after('booking_date');
            $table->integer('guest_count')->nullable()->after('booking_time');
            $table->decimal('total_amount', 10, 2)->after('guest_count');
            $table->decimal('commission_amount', 10, 2)->after('total_amount');
            $table->decimal('commission_rate', 5, 2)->after('commission_amount');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('commission_rate');
            $table->enum('booking_source', ['online', 'manual', 'pos'])->default('online')->after('status');
            $table->text('special_requests')->nullable()->after('booking_source');
            $table->text('cancellation_reason')->nullable()->after('special_requests');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null')->after('cancelled_at');
            $table->string('qr_code')->unique()->nullable()->after('cancelled_by');

            // Modify existing status column to use proper enum
            $table->dropColumn('service_name'); // We'll use service_id instead
        });

        // Add the enum constraint for status in a separate statement
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'pending'");

        Schema::table('bookings', function (Blueprint $table) {
            // Add indexes for performance
            $table->index(['booking_date', 'status']);
            $table->index(['merchant_id', 'payment_status']);
            $table->index('booking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['booking_date', 'status']);
            $table->dropIndex(['merchant_id', 'payment_status']);
            $table->dropIndex(['booking_number']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['merchant_id']);
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn([
                'booking_number', 'customer_id', 'service_id', 'merchant_id',
                'booking_time', 'guest_count', 'total_amount',
                'commission_amount', 'commission_rate', 'payment_status',
                'booking_source', 'special_requests',
                'cancellation_reason', 'cancelled_at', 'cancelled_by', 'qr_code',
            ]);
            $table->string('service_name')->after('user_id');
        });
    }
};
