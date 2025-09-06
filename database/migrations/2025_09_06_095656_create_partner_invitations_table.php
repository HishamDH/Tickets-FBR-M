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
        Schema::create('partner_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->string('email');
            $table->string('business_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('invitation_token')->unique();
            $table->enum('status', ['pending', 'accepted', 'expired', 'cancelled'])->default('pending');
            $table->text('message')->nullable(); // رسالة شخصية من الشريك
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('merchant_id')->nullable()->constrained('merchants')->onDelete('set null'); // إذا تم قبول الدعوة
            $table->timestamps();
            
            // فهارس
            $table->index('partner_id');
            $table->index('email');
            $table->index('status');
            $table->index('invitation_token');
            $table->index(['partner_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_invitations');
    }
};
