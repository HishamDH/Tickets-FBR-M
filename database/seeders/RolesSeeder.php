<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create core roles based on reference project
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $merchantRole = Role::firstOrCreate(['name' => 'Merchant']);
        $customerRole = Role::firstOrCreate(['name' => 'Customer']);

        // Create basic permissions for each role
        $adminPermissions = [
            'manage users',
            'manage merchants',
            'manage categories',
            'manage offerings',
            'manage branches',
            'manage payments',
            'manage analytics',
            'manage notifications',
            'manage system settings',
            'view admin dashboard',
        ];

        $merchantPermissions = [
            'manage own offerings',
            'manage own reservations',
            'manage own branch',
            'view merchant dashboard',
            'manage own profile',
            'view merchant analytics',
            'manage merchant payments',
        ];

        $customerPermissions = [
            'make reservations',
            'view offerings',
            'manage own profile',
            'view customer dashboard',
            'rate offerings',
            'view reservation history',
        ];

        // Create permissions
        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach ($merchantPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach ($customerPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo($adminPermissions);
        $merchantRole->givePermissionTo($merchantPermissions);
        $customerRole->givePermissionTo($customerPermissions);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Created roles: Admin, Merchant, Customer');
        $this->command->info('Admin permissions: '.count($adminPermissions));
        $this->command->info('Merchant permissions: '.count($merchantPermissions));
        $this->command->info('Customer permissions: '.count($customerPermissions));
    }
}
