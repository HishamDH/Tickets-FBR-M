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
        Schema::table('carts', function (Blueprint $table) {
            // Make user_id nullable for guest users
            $table->foreignId('user_id')->nullable()->change();
            
            // Add session ID for guest cart persistence
            $table->string('session_id')->nullable()->after('user_id');
            
            // Add polymorphic columns for flexible item types
            $table->unsignedBigInteger('item_id')->after('offering_id');
            $table->string('item_type')->after('item_id');
            
            // Add pricing and discount columns
            $table->decimal('price', 10, 2)->default(0)->after('quantity');
            $table->decimal('discount', 10, 2)->default(0)->after('price');
            
            // Add additional data for branch, time slots, options, etc.
            $table->json('additional_data')->nullable()->after('discount');
            
            // Add indexes for performance
            $table->index(['user_id', 'session_id']);
            $table->index(['item_id', 'item_type']);
            $table->index(['session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Remove new columns
            $table->dropIndex(['user_id', 'session_id']);
            $table->dropIndex(['item_id', 'item_type']);
            $table->dropIndex(['session_id']);
            
            $table->dropColumn([
                'session_id',
                'item_id', 
                'item_type',
                'price',
                'discount',
                'additional_data'
            ]);
            
            // Restore original user_id constraint (this would need manual fixing)
            // $table->foreignId('user_id')->change();
        });
    }
};
