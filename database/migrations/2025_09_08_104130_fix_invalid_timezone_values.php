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
        // Fix invalid timezone values
        DB::table('users')
            ->whereIn('timezone', ['flexible', 'auto', 'dynamic', 'variable', 'null', ''])
            ->orWhereNull('timezone')
            ->update(['timezone' => 'Asia/Riyadh']);

        // Also fix any other invalid timezone values that are not recognized by PHP
        $validTimezones = timezone_identifiers_list();
        $users = DB::table('users')->whereNotNull('timezone')->where('timezone', '!=', '')->get();
        
        foreach ($users as $user) {
            if (!in_array($user->timezone, $validTimezones)) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['timezone' => 'Asia/Riyadh']);
            }
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
