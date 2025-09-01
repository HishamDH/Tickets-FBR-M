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
        Schema::table('paid_reservations', function (Blueprint $table) {
            if (! Schema::hasColumn('paid_reservations', 'item_type')) {
                $table->string('item_type')->after('item_id');
            }
            if (! Schema::hasColumn('paid_reservations', 'quantity')) {
                $table->double('quantity')->default(1)->after('item_type');
            }
            if (! Schema::hasColumn('paid_reservations', 'discount')) {
                $table->double('discount')->default(0.0)->after('quantity');
            }
            if (! Schema::hasColumn('paid_reservations', 'code')) {
                $table->string('code')->unique()->after('discount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paid_reservations', function (Blueprint $table) {
            $table->dropColumn(['item_type', 'quantity', 'discount', 'code']);
        });
    }
};
