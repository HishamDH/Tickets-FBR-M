<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Merchant;
use App\Models\Offering;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuickSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories first
        $categories = [
            ['name' => 'Events', 'slug' => 'events', 'icon' => '🎉'],
            ['name' => 'Conferences', 'slug' => 'conferences', 'icon' => '🏢'],
            ['name' => 'Restaurants', 'slug' => 'restaurants', 'icon' => '🍽️'],
            ['name' => 'Experiences', 'slug' => 'experiences', 'icon' => '🎯'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Create a merchant user
        $merchantUser = User::firstOrCreate(
            ['email' => 'merchant@test.com'],
            [
                'name' => 'Golden Events',
                'f_name' => 'Golden',
                'l_name' => 'Events',
                'password' => bcrypt('password'),
                'user_type' => 'merchant',
                'status' => 'active',
                'phone' => '+966501234567',
                'email_verified_at' => now(),
            ]
        );

        // Create merchant profile
        $merchant = Merchant::firstOrCreate(
            ['user_id' => $merchantUser->id],
            [
                'business_name' => 'Golden Events',
                'business_type' => 'events',
                'cr_number' => '1010123456',
                'verification_status' => 'approved',
                'commission_rate' => 5.0,
                'city' => 'Riyadh',
            ]
        );

        // Create another merchant user
        $merchantUser2 = User::firstOrCreate(
            ['email' => 'restaurant@test.com'],
            [
                'name' => 'Royal Restaurant',
                'f_name' => 'Royal',
                'l_name' => 'Restaurant',
                'password' => bcrypt('password'),
                'user_type' => 'merchant',
                'status' => 'active',
                'phone' => '+966501234568',
                'email_verified_at' => now(),
            ]
        );

        // Create second merchant profile
        $merchant2 = Merchant::firstOrCreate(
            ['user_id' => $merchantUser2->id],
            [
                'business_name' => 'Royal Restaurant',
                'business_type' => 'restaurant',
                'cr_number' => '1010123457',
                'verification_status' => 'approved',
                'commission_rate' => 3.0,
                'city' => 'Jeddah',
            ]
        );

        // Create offerings for first merchant
        Offering::firstOrCreate(
            ['name' => 'Wedding Reception Hall', 'user_id' => $merchantUser->id],
            [
                'description' => 'Beautiful wedding reception hall with elegant decorations, can accommodate up to 200 guests',
                'price' => 2500.00,
                'status' => 'active',
                'type' => 'events',
                'category' => 'weddings',
                'location' => 'Riyadh, Saudi Arabia',
            ]
        );

        Offering::firstOrCreate(
            ['name' => 'Corporate Conference Room', 'user_id' => $merchantUser->id],
            [
                'description' => 'Professional conference room for business meetings with modern AV equipment',
                'price' => 800.00,
                'status' => 'active',
                'type' => 'conference',
                'category' => 'business',
                'location' => 'Riyadh, Saudi Arabia',
            ]
        );

        Offering::firstOrCreate(
            ['name' => 'Birthday Party Package', 'user_id' => $merchantUser->id],
            [
                'description' => 'Complete birthday party package with decorations, cake, and entertainment',
                'price' => 1200.00,
                'status' => 'active',
                'type' => 'events',
                'category' => 'parties',
                'location' => 'Riyadh, Saudi Arabia',
            ]
        );

        // Create offerings for second merchant
        Offering::firstOrCreate(
            ['name' => 'Fine Dining Experience', 'user_id' => $merchantUser2->id],
            [
                'description' => 'Exquisite dining experience with international cuisine',
                'price' => 150.00,
                'status' => 'active',
                'type' => 'restaurant',
                'category' => 'dining',
                'location' => 'Jeddah, Saudi Arabia',
            ]
        );

        Offering::firstOrCreate(
            ['name' => 'Private Dining Room', 'user_id' => $merchantUser2->id],
            [
                'description' => 'Private dining room for intimate gatherings and business dinners',
                'price' => 500.00,
                'status' => 'active',
                'type' => 'restaurant',
                'category' => 'private_dining',
                'location' => 'Jeddah, Saudi Arabia',
            ]
        );

        // Create a test customer
        $customer = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Ahmed Al-Saudi',
                'f_name' => 'Ahmed',
                'l_name' => 'Al-Saudi',
                'password' => bcrypt('password'),
                'user_type' => 'customer',
                'status' => 'active',
                'phone' => '+966501234569',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Sample data created successfully!');
        $this->command->info('📊 Created:');
        $this->command->info('   - 2 Merchants');
        $this->command->info('   - 5 Offerings');
        $this->command->info('   - 1 Customer');
        $this->command->info('   - 4 Categories');
    }
}
