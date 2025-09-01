<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'System Administrator',
            'f_name' => 'System',
            'l_name' => 'Administrator',
            'email' => 'admin@shubak.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'user_type' => 'admin',
            'phone' => '+966500000000',
            'status' => 'active',
        ]);

        // Assign admin role
        $admin->assignRole('admin');

        $this->command->info('Admin user created: admin@shubak.com / password');
    }
}
