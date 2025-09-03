<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
            if (! Role::where('name', $name)->exists()) {
                Role::create([
                    'name' => $name,
                    'guard_name' => 'web',
                ]);
            }
        }

        // Create comprehensive permissions based on reference project (47+ permissions)
        $permissions = [
            // Overview
            'overview_page',

            // Offers/Services
            'offers_view',
            'offers_create',
            'offers_edit',
            'offers_delete',

            // Ticket checking
            'check_view',
            'check_tickets',

            // Ratings and reviews
            'ratings_view',
            'ratings_reply',

            // Reservations/Bookings
            'reservations_view',
            'reservations_create',
            'reservations_edit',
            'reservations_delete',
            'reservation_detail',

            // POS System
            'pos_page',
            'pos_create',
            'pos_view',
            'pos_delete',

            // Reports and analytics
            'reports_view',

            // Notifications
            'notifications_view',

            // Messages and chat
            'messages_view',
            'messages_send',
            'accept_chats',

            // Wallet and withdrawals
            'wallet_view',
            'wallet_withdraw',

            // Branch management
            'branches_view',
            'branches_create',
            'branches_edit',
            'branches_delete',

            // Role management
            'role_create',
            'role_view',
            'role_edit',
            'role_delete',

            // Team management
            'team_manager_create',
            'team_manager_view',
            'team_manager_edit',
            'team_manager_kick',

            // Support system
            'support_view',
            'support_open',
            'support_delete',

            // Settings
            'settings_view',
            'settings_edit',

            // Policies
            'policies_view',
            'policies_edit',

            // History
            'history_view',

            // User management
            'manage_users',
            'manage_merchants',
            'manage_partners',

            // System management
            'manage_settings',
            'manage_system',
        ];

        foreach ($permissions as $permission) {
            if (! Permission::where('name', $permission)->exists()) {
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
