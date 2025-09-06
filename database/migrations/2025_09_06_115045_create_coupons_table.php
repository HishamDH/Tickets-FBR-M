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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('discount_type', ['fixed', 'percentage']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('minimum_amount', 10, 2)->nullable();
            $table->decimal('maximum_discount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->integer('usage_limit_per_user')->nullable();
            $table->datetime('starts_at')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('merchant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['code', 'is_active']);
            $table->index(['merchant_id', 'is_active']);
            $table->index(['expires_at', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
