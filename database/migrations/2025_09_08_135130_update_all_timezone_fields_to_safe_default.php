<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Force update ALL users to have a safe timezone
        DB::statement("UPDATE users SET timezone = 'Asia/Riyadh'");
        
        // Also ensure app timezone is set correctly in config if needed
        if (config('app.timezone') !== 'Asia/Riyadh') {
            // We don't modify the config file directly, but make note
            // Admin should update config/app.php: 'timezone' => 'Asia/Riyadh'
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this fix
    }
};
