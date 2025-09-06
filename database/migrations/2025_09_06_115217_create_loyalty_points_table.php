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
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('loyalty_program_id')->constrained()->onDelete('cascade');
            $table->integer('points');
            $table->datetime('expires_at')->nullable();
            $table->datetime('used_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'loyalty_program_id']);
            $table->index(['expires_at', 'used_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
