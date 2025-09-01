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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم البوابة
            $table->string('code')->unique(); // رمز البوابة (visa, mastercard, mada, apple_pay, stc_pay)
            $table->string('display_name_ar'); // الاسم باللغة العربية
            $table->string('display_name_en'); // الاسم باللغة الإنجليزية
            $table->text('description')->nullable(); // وصف البوابة
            $table->string('icon')->nullable(); // أيقونة البوابة
            $table->string('provider')->nullable(); // مقدم الخدمة (stripe, bank, etc)
            $table->json('settings')->nullable(); // إعدادات إضافية للبوابة
            $table->decimal('transaction_fee', 8, 2)->default(0); // رسوم المعاملة
            $table->enum('fee_type', ['fixed', 'percentage'])->default('percentage'); // نوع الرسوم
            $table->boolean('is_active')->default(true); // حالة التفعيل العامة
            $table->boolean('supports_refund')->default(false); // دعم الاسترداد
            $table->integer('sort_order')->default(0); // ترتيب العرض
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
