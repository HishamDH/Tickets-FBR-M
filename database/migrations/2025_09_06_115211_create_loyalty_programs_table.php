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
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('points');
            $table->decimal('points_per_amount', 8, 2)->default(1.00);
            $table->decimal('redemption_rate', 8, 2)->default(0.01);
            $table->integer('minimum_points')->default(0);
            $table->integer('maximum_points_per_transaction')->nullable();
            $table->integer('expiry_months')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('merchant_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('settings')->nullable();
            $table->json('tier_benefits')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['merchant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_programs');
    }
};
