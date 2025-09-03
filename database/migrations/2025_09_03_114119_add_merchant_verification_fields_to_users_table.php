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
        Schema::table('users', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('users', 'commercial_registration_number')) {
                $table->string('commercial_registration_number')->nullable()->after('business_name');
            }
            if (!Schema::hasColumn('users', 'tax_number')) {
                $table->string('tax_number')->nullable()->after('commercial_registration_number');
            }
            if (!Schema::hasColumn('users', 'business_city')) {
                $table->string('business_city')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'merchant_status')) {
                $table->enum('merchant_status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending')->after('business_city');
            }
            if (!Schema::hasColumn('users', 'verification_notes')) {
                $table->text('verification_notes')->nullable()->after('merchant_status');
            }
            if (!Schema::hasColumn('users', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verification_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $existingColumns = [];
            if (Schema::hasColumn('users', 'commercial_registration_number')) {
                $existingColumns[] = 'commercial_registration_number';
            }
            if (Schema::hasColumn('users', 'tax_number')) {
                $existingColumns[] = 'tax_number';
            }
            if (Schema::hasColumn('users', 'business_city')) {
                $existingColumns[] = 'business_city';
            }
            if (Schema::hasColumn('users', 'merchant_status')) {
                $existingColumns[] = 'merchant_status';
            }
            if (Schema::hasColumn('users', 'verification_notes')) {
                $existingColumns[] = 'verification_notes';
            }
            if (Schema::hasColumn('users', 'verified_at')) {
                $existingColumns[] = 'verified_at';
            }
            
            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
