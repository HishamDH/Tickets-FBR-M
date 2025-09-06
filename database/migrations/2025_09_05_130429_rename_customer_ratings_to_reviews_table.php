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
        // حذف foreign key constraints أولاً
        Schema::table('review_images', function (Blueprint $table) {
            $table->dropForeign(['review_id']);
        });

        // حذف جدول reviews القديم
        Schema::dropIfExists('reviews');
        
        // إعادة تسمية customer_ratings إلى reviews
        Schema::rename('customer_ratings', 'reviews');
        
        // إعادة إضافة foreign key constraint
        Schema::table('review_images', function (Blueprint $table) {
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
        });
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
