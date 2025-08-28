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
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->string('business_type', 100);
            $table->string('cr_number', 50)->unique();
            $table->text('business_address')->nullable();
            $table->string('city', 100);
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->decimal('commission_rate', 5, 2)->default(3.00);
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->foreignId('account_manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('settings')->nullable();
            $table->timestamps();
            
            $table->index(['verification_status', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
