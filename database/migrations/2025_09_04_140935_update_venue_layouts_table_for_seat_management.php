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
        Schema::table('venue_layouts', function (Blueprint $table) {
            // Add new columns
            $table->foreignId('merchant_id')->after('id')->constrained('users')->onDelete('cascade');
            $table->foreignId('offering_id')->after('merchant_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('rows')->after('layout_type')->default(10);
            $table->integer('columns')->after('rows')->default(10);
            $table->integer('total_seats')->after('columns')->default(0);
            $table->json('layout_data')->after('layout_config')->nullable();
            
            // Drop old columns that we don't need
            $table->dropColumn(['total_capacity', 'width', 'height']);
            
            // Rename service_id to offering_id if needed
            if (Schema::hasColumn('venue_layouts', 'service_id')) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            }
            
            // Make layout_config nullable
            $table->json('layout_config')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venue_layouts', function (Blueprint $table) {
            // Restore old structure
            $table->dropForeign(['merchant_id']);
            $table->dropForeign(['offering_id']);
            $table->dropColumn(['merchant_id', 'offering_id', 'rows', 'columns', 'total_seats', 'layout_data']);
            
            // Add back old columns
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->integer('total_capacity');
            $table->decimal('width', 8, 2);
            $table->decimal('height', 8, 2);
        });
    }
};
