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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // notification type (booking_confirmed, payment_received, etc.)
            $table->string('notifiable_type'); // Model type (User, Merchant, Partner)
            $table->unsignedBigInteger('notifiable_id'); // Model ID
            $table->string('title'); // Notification title
            $table->text('message'); // Notification message
            $table->json('data')->nullable(); // Additional data
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('channel')->default('database'); // database, email, sms, push
            $table->timestamp('read_at')->nullable(); // When notification was read
            $table->timestamp('sent_at')->nullable(); // When notification was sent
            $table->boolean('is_sent')->default(false); // Whether notification was sent
            $table->timestamps();

            // Indexes for performance
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index(['type', 'created_at']);
            $table->index(['read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
