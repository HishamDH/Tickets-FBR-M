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
        Schema::create('merchant_payment_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_gateway_id')->constrained()->onDelete('cascade');
            $table->boolean('is_enabled')->default(false); // التاجر فعّل هذه البوابة
            $table->json('gateway_credentials')->nullable(); // بيانات الاتصال (مشفرة)
            $table->decimal('custom_fee', 8, 2)->nullable(); // رسوم مخصصة للتاجر
            $table->enum('custom_fee_type', ['fixed', 'percentage'])->nullable();
            $table->integer('display_order')->default(0); // ترتيب العرض لدى التاجر
            $table->json('additional_settings')->nullable(); // إعدادات إضافية
            $table->timestamp('last_tested_at')->nullable(); // آخر اختبار للبوابة
            $table->boolean('test_passed')->default(false); // نتيجة آخر اختبار
            $table->timestamps();
            
            $table->unique(['merchant_id', 'payment_gateway_id']);
            $table->index(['merchant_id', 'is_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_payment_settings');
    }
};
