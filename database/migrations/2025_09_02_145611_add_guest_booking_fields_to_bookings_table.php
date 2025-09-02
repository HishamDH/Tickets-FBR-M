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
            // Add guest booking fields for non-registered customers
            $table->string('customer_name')->nullable()->after('qr_code');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->string('customer_email')->nullable()->after('customer_phone');
            
            // Add additional booking details
            $table->integer('number_of_people')->nullable()->after('customer_email');
            $table->integer('number_of_tables')->nullable()->after('number_of_people');
            $table->integer('duration_hours')->nullable()->after('number_of_tables');
            $table->text('notes')->nullable()->after('duration_hours');
        });
        
        // Make customer_id nullable for guest bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreignId('customer_id')->nullable()->change();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name',
                'customer_phone', 
                'customer_email',
                'number_of_people',
                'number_of_tables',
                'duration_hours',
                'notes'
            ]);
        });
    }
};
