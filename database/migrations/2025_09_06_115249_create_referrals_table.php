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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referee_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('referral_program_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->boolean('is_successful')->default(false);
            $table->datetime('completed_at')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->decimal('order_amount', 10, 2)->nullable();
            $table->datetime('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['code', 'is_successful']);
            $table->index(['referrer_id', 'referral_program_id']);
            $table->index(['expires_at', 'is_successful']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
