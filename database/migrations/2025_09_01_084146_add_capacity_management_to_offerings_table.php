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
        Schema::table('offerings', function (Blueprint $table) {
            $table->integer('max_capacity')->default(50)->after('chairs_count');
            $table->integer('min_capacity')->default(1)->after('max_capacity');
            $table->boolean('allow_overbooking')->default(false)->after('min_capacity');
            $table->decimal('overbooking_percentage', 5, 2)->default(10)->after('allow_overbooking');
            $table->enum('capacity_type', ['fixed', 'flexible', 'unlimited'])->default('fixed')->after('overbooking_percentage');
            $table->integer('buffer_capacity')->default(0)->after('capacity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offerings', function (Blueprint $table) {
            $table->dropColumn([
                'max_capacity',
                'min_capacity',
                'allow_overbooking',
                'overbooking_percentage',
                'capacity_type',
                'buffer_capacity'
            ]);
        });
    }
};
