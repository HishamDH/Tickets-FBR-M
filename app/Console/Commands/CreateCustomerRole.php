<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CreateCustomerRole extends Command
{
    protected $signature = 'app:create-customer-role';
    protected $description = 'Create the Customer role required for tests';

    public function handle()
    {
        $this->info('Creating Customer role...');

        if (!Role::where('name', 'Customer')->exists()) {
            Role::create(['name' => 'Customer', 'guard_name' => 'web']);
            $this->info('Customer role created successfully.');
        } else {
            $this->info('Customer role already exists.');
        }

        // Check if other required roles exist, create if not
        $requiredRoles = ['Admin', 'Merchant', 'Partner'];
        
        foreach ($requiredRoles as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
                $this->info("$roleName role created successfully.");
            } else {
                $this->info("$roleName role already exists.");
            }
        }

        return Command::SUCCESS;
    }
}
