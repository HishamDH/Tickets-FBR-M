<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'f_name' => 'Admin',
            'l_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'role' => 'admin',
        ]);

        // Customer User
        User::create([
            'name' => 'Customer User',
            'f_name' => 'Customer',
            'l_name' => 'User',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'customer',
        ]);

        // Merchant User
        User::create([
            'name' => 'Merchant User',
            'f_name' => 'Merchant',
            'l_name' => 'User',
            'email' => 'merchant@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'merchant',
        ]);

        // Partner User
        User::create([
            'name' => 'Partner User',
            'f_name' => 'Partner',
            'l_name' => 'User',
            'email' => 'partner@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'partner',
        ]);
    }
}
