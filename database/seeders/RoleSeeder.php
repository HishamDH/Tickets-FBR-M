<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            'admin' => 'System Administrator',
            'customer' => 'Customer User', 
            'merchant' => 'Merchant User',
            'partner' => 'Partner User',
        ];

        foreach ($roles as $name => $description) {
            if (!Role::where('name', $name)->exists()) {
                Role::create([
                    'name' => $name,
                    'guard_name' => 'web',
                ]);
            }
        }

        // Create basic permissions (can be expanded later)
        $permissions = [
            'manage_users',
            'manage_merchants', 
            'manage_services',
            'manage_bookings',
            'manage_partners',
            'view_reports',
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);
            }
        }

        // Assign all permissions to admin role
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(Permission::all());

        $this->command->info('Roles and permissions created successfully');
    }
}
