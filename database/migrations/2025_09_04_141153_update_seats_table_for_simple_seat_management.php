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
        Schema::table('seats', function (Blueprint $table) {
            // Add simple row/column tracking
            $table->integer('row_number')->after('seat_number')->default(1);
            $table->integer('column_number')->after('row_number')->default(1);
            $table->string('category')->after('column_number')->default('standard'); // standard, vip, etc.
            $table->boolean('is_available')->after('metadata')->default(true);
            
            // Rename section to match our model
            if (Schema::hasColumn('seats', 'section')) {
                $table->renameColumn('section', 'seat_section');
            }
            
            // Make some columns nullable or with defaults
            $table->decimal('width', 8, 2)->default(1.0)->change();
            $table->decimal('height', 8, 2)->default(1.0)->change();
            $table->json('metadata')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->dropColumn(['row_number', 'column_number', 'category', 'is_available']);
            
            if (Schema::hasColumn('seats', 'seat_section')) {
                $table->renameColumn('seat_section', 'section');
            }
        });
    }
};
