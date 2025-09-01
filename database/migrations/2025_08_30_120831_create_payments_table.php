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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique(); // رقم الدفعة الفريد
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('merchant_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_gateway_id')->constrained();
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');

            // تفاصيل المبلغ
            $table->decimal('amount', 10, 2); // المبلغ الأساسي
            $table->decimal('gateway_fee', 8, 2)->default(0); // رسوم البوابة
            $table->decimal('platform_fee', 8, 2)->default(0); // رسوم المنصة
            $table->decimal('total_amount', 10, 2); // المبلغ الإجمالي
            $table->string('currency', 3)->default('SAR');

            // حالة الدفع
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_method', ['card', 'digital_wallet', 'bank_transfer', 'cash'])->nullable();

            // بيانات البوابة الخارجية
            $table->string('gateway_transaction_id')->nullable(); // معرف المعاملة في البوابة
            $table->string('gateway_reference')->nullable(); // مرجع البوابة
            $table->json('gateway_response')->nullable(); // استجابة البوابة
            $table->json('gateway_metadata')->nullable(); // بيانات إضافية من البوابة

            // تواريخ مهمة
            $table->timestamp('initiated_at')->nullable(); // تاريخ بدء الدفع
            $table->timestamp('completed_at')->nullable(); // تاريخ اكتمال الدفع
            $table->timestamp('failed_at')->nullable(); // تاريخ فشل الدفع

            // معلومات إضافية
            $table->string('failure_reason')->nullable(); // سبب الفشل
            $table->string('customer_ip')->nullable(); // IP العميل
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();

            $table->index(['booking_id', 'status']);
            $table->index(['merchant_id', 'status']);
            $table->index(['payment_gateway_id', 'status']);
            $table->index('gateway_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
