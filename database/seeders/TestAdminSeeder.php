<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TestAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@shubak.com')->first();
        
        if ($admin) {
            $this->command->info('Admin user found');
            $this->command->info('User type: ' . $admin->user_type);
            $this->command->info('Role attribute: ' . $admin->role);
            $this->command->info('Has admin role: ' . ($admin->hasRole('admin') ? 'Yes' : 'No'));
            $this->command->info('All roles: ' . implode(', ', $admin->getRoleNames()->toArray()));
            
            // Make sure the admin has the role
            if (!$admin->hasRole('admin')) {
                $admin->assignRole('admin');
                $this->command->info('Assigned admin role to user');
            }
        } else {
            $this->command->info('Admin user not found');
        }
    }
}
