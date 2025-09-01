<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Offering;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Creating sample data for testing...');

        // Create Categories
        $this->createCategories();

        // Create Test Users with Roles
        $this->createTestUsers();

        // Create Sample Merchants
        $merchants = $this->createMerchants();

        // Create Sample Offerings
        $offerings = $this->createOfferings($merchants);

        // Create Sample Bookings
        $this->createBookings($offerings);

        $this->command->info('✅ Sample data created successfully!');
        $this->command->info('🌐 Visit http://tickets-fbr-m.test/ to test your platform');
    }

    private function createCategories()
    {
        $categories = [
            ['name' => 'Events & Entertainment', 'description' => 'Weddings, parties, concerts, and celebrations'],
            ['name' => 'Food & Catering', 'description' => 'Restaurant reservations and catering services'],
            ['name' => 'Health & Wellness', 'description' => 'Spa, massage, fitness, and wellness services'],
            ['name' => 'Professional Services', 'description' => 'Consulting, legal, accounting, and business services'],
            ['name' => 'Beauty & Personal Care', 'description' => 'Hair, makeup, nail, and beauty services'],
            ['name' => 'Travel & Tourism', 'description' => 'Tours, guides, and travel experiences'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                array_merge($category, ['slug' => Str::slug($category['name'])])
            );
        }

        $this->command->info('📂 Categories created');
    }

    private function createTestUsers()
    {
        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@tickets-fbr-m.test'],
            [
                'name' => 'Platform Administrator',
                'f_name' => 'Admin',
                'l_name' => 'User',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('Admin');

        // Customer Users
        $customers = [
            [
                'name' => 'John Customer',
                'email' => 'customer@tickets-fbr-m.test',
                'f_name' => 'John',
                'l_name' => 'Customer',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'f_name' => 'Sarah',
                'l_name' => 'Johnson',
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike@example.com',
                'f_name' => 'Mike',
                'l_name' => 'Wilson',
            ],
        ];

        foreach ($customers as $customerData) {
            $customer = User::firstOrCreate(
                ['email' => $customerData['email']],
                array_merge($customerData, [
                    'password' => Hash::make('password'),
                    'user_type' => 'customer',
                    'email_verified_at' => now(),
                ])
            );
            $customer->assignRole('Customer');
        }

        $this->command->info('👥 Test users created (admin@tickets-fbr-m.test, customer@tickets-fbr-m.test)');
    }

    private function createMerchants()
    {
        $merchantsData = [
            [
                'name' => 'Grand Events Co',
                'email' => 'merchant1@tickets-fbr-m.test',
                'business_name' => 'Grand Events Company',
                'description' => 'Professional event planning and management services for weddings, corporate events, and special occasions. We handle everything from venue selection to catering coordination.',
                'phone' => '+1-555-0101',
                'city' => 'New York',
                'country' => 'USA',
                'website' => 'https://grandevents.example.com',
                'status' => 'active',
            ],
            [
                'name' => 'Bella Vista Restaurant',
                'email' => 'merchant2@tickets-fbr-m.test',
                'business_name' => 'Bella Vista Fine Dining',
                'description' => 'Upscale Italian restaurant offering authentic cuisine, private dining rooms, and catering services. Perfect for romantic dinners and business meetings.',
                'phone' => '+1-555-0102',
                'city' => 'Los Angeles',
                'country' => 'USA',
                'website' => 'https://bellavista.example.com',
                'status' => 'active',
            ],
            [
                'name' => 'Zen Wellness Spa',
                'email' => 'merchant3@tickets-fbr-m.test',
                'business_name' => 'Zen Wellness & Spa Center',
                'description' => 'Full-service spa offering massage therapy, facial treatments, yoga classes, and wellness packages. Your sanctuary for relaxation and rejuvenation.',
                'phone' => '+1-555-0103',
                'city' => 'Miami',
                'country' => 'USA',
                'website' => 'https://zenwellness.example.com',
                'status' => 'active',
            ],
            [
                'name' => 'ProConsult Solutions',
                'email' => 'merchant4@tickets-fbr-m.test',
                'business_name' => 'ProConsult Business Solutions',
                'description' => 'Professional business consulting firm specializing in strategy development, process optimization, and digital transformation for small and medium enterprises.',
                'phone' => '+1-555-0104',
                'city' => 'Chicago',
                'country' => 'USA',
                'website' => 'https://proconsult.example.com',
                'status' => 'active',
            ],
            [
                'name' => 'Glam Beauty Studio',
                'email' => 'merchant5@tickets-fbr-m.test',
                'business_name' => 'Glam Beauty Studio',
                'description' => 'Premier beauty salon offering hair styling, makeup artistry, nail services, and bridal packages. Making you look and feel your absolute best.',
                'phone' => '+1-555-0105',
                'city' => 'Las Vegas',
                'country' => 'USA',
                'website' => 'https://glambeauty.example.com',
                'status' => 'active',
            ],
            [
                'name' => 'Adventure Tours LLC',
                'email' => 'merchant6@tickets-fbr-m.test',
                'business_name' => 'Adventure Tours & Experiences',
                'description' => 'Exciting guided tours and adventure experiences including hiking, city tours, food tours, and cultural experiences. Discover the world with us.',
                'phone' => '+1-555-0106',
                'city' => 'San Francisco',
                'country' => 'USA',
                'website' => 'https://adventuretours.example.com',
                'status' => 'active',
            ],
        ];

        $merchants = [];
        foreach ($merchantsData as $merchantData) {
            // Create user first
            $user = User::firstOrCreate(
                ['email' => $merchantData['email']],
                [
                    'name' => $merchantData['name'],
                    'f_name' => explode(' ', $merchantData['name'])[0] ?? $merchantData['name'],
                    'l_name' => explode(' ', $merchantData['name'])[1] ?? 'Business',
                    'password' => Hash::make('password'),
                    'user_type' => 'merchant',
                    'email_verified_at' => now(),
                    'phone' => $merchantData['phone'],
                ]
            );
            $user->assignRole('Merchant');

            // Create merchant
            $merchant = Merchant::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'business_name' => $merchantData['business_name'],
                    'business_type' => 'service', // Default business type
                    'cr_number' => 'CR'.rand(1000000, 9999999), // Random CR number
                    'business_address' => $merchantData['city'].', '.$merchantData['country'],
                    'city' => $merchantData['city'],
                    'verification_status' => 'approved',
                    'commission_rate' => 0.10, // 10% default commission
                ]
            );

            $merchants[] = $merchant;
        }

        $this->command->info('🏪 '.count($merchants).' merchants created');

        return $merchants;
    }

    private function createOfferings($merchants)
    {
        $categories = Category::all();
        $offerings = [];

        $offeringsData = [
            // Grand Events Co
            [
                'merchant_index' => 0,
                'offerings' => [
                    ['title' => 'Wedding Planning Package', 'price' => 2500, 'category' => 'Events & Entertainment', 'description' => 'Complete wedding planning service including venue coordination, vendor management, and day-of coordination.'],
                    ['title' => 'Corporate Event Management', 'price' => 1800, 'category' => 'Events & Entertainment', 'description' => 'Professional corporate event planning for conferences, seminars, and company celebrations.'],
                    ['title' => 'Birthday Party Organization', 'price' => 650, 'category' => 'Events & Entertainment', 'description' => 'Fun and memorable birthday party planning for all ages with themes and entertainment.'],
                ],
            ],
            // Bella Vista Restaurant
            [
                'merchant_index' => 1,
                'offerings' => [
                    ['title' => 'Fine Dining Experience', 'price' => 85, 'category' => 'Food & Catering', 'description' => 'Exquisite 7-course tasting menu with wine pairings in our elegant dining room.'],
                    ['title' => 'Private Dining Room', 'price' => 450, 'category' => 'Food & Catering', 'description' => 'Exclusive private dining experience for up to 12 guests with personalized menu.'],
                    ['title' => 'Event Catering Service', 'price' => 35, 'category' => 'Food & Catering', 'description' => 'Professional catering services for corporate events and special occasions.'],
                ],
            ],
            // Zen Wellness Spa
            [
                'merchant_index' => 2,
                'offerings' => [
                    ['title' => 'Full Body Massage Therapy', 'price' => 120, 'category' => 'Health & Wellness', 'description' => '90-minute therapeutic massage using premium oils and techniques for ultimate relaxation.'],
                    ['title' => 'Facial Treatment Package', 'price' => 85, 'category' => 'Health & Wellness', 'description' => 'Rejuvenating facial treatment with organic products tailored to your skin type.'],
                    ['title' => 'Couples Spa Day', 'price' => 280, 'category' => 'Health & Wellness', 'description' => 'Romantic spa experience for two including massage, facial, and champagne.'],
                    ['title' => 'Yoga Class Session', 'price' => 25, 'category' => 'Health & Wellness', 'description' => 'Group yoga class suitable for all levels in our peaceful studio environment.'],
                ],
            ],
            // ProConsult Solutions
            [
                'merchant_index' => 3,
                'offerings' => [
                    ['title' => 'Business Strategy Consultation', 'price' => 350, 'category' => 'Professional Services', 'description' => '3-hour strategic planning session to define goals and develop actionable business strategies.'],
                    ['title' => 'Digital Transformation Audit', 'price' => 1200, 'category' => 'Professional Services', 'description' => 'Comprehensive analysis of your digital processes with recommendations for improvement.'],
                    ['title' => 'Process Optimization Review', 'price' => 800, 'category' => 'Professional Services', 'description' => 'Detailed review of business processes to identify inefficiencies and improvement opportunities.'],
                ],
            ],
            // Glam Beauty Studio
            [
                'merchant_index' => 4,
                'offerings' => [
                    ['title' => 'Bridal Makeup & Hair', 'price' => 250, 'category' => 'Beauty & Personal Care', 'description' => 'Complete bridal beauty package including trial run, wedding day makeup and hairstyling.'],
                    ['title' => 'Hair Cut & Styling', 'price' => 65, 'category' => 'Beauty & Personal Care', 'description' => 'Professional haircut and styling service with our expert stylists.'],
                    ['title' => 'Manicure & Pedicure', 'price' => 55, 'category' => 'Beauty & Personal Care', 'description' => 'Relaxing nail care service with premium products and attention to detail.'],
                    ['title' => 'Special Event Makeup', 'price' => 85, 'category' => 'Beauty & Personal Care', 'description' => 'Professional makeup application for special events, photoshoots, or nights out.'],
                ],
            ],
            // Adventure Tours LLC
            [
                'merchant_index' => 5,
                'offerings' => [
                    ['title' => 'City Walking Tour', 'price' => 45, 'category' => 'Travel & Tourism', 'description' => '3-hour guided walking tour covering historical landmarks and hidden gems of the city.'],
                    ['title' => 'Food & Wine Tour', 'price' => 95, 'category' => 'Travel & Tourism', 'description' => 'Culinary adventure visiting local restaurants, markets, and wine bars with tastings.'],
                    ['title' => 'Hiking Adventure Day', 'price' => 125, 'category' => 'Travel & Tourism', 'description' => 'Full-day guided hiking experience including transportation, equipment, and lunch.'],
                    ['title' => 'Photography Tour', 'price' => 85, 'category' => 'Travel & Tourism', 'description' => 'Professional photography tour with tips and techniques at scenic locations.'],
                ],
            ],
        ];

        foreach ($offeringsData as $merchantOfferings) {
            $merchant = $merchants[$merchantOfferings['merchant_index']];

            foreach ($merchantOfferings['offerings'] as $offeringData) {
                $category = $categories->where('name', $offeringData['category'])->first();

                $offering = Offering::firstOrCreate(
                    [
                        'name' => $offeringData['title'],
                        'user_id' => $merchant->user_id,
                    ],
                    [
                        'description' => $offeringData['description'],
                        'price' => $offeringData['price'],
                        'status' => 'active',
                        'category' => $offeringData['category'],
                        'features' => $this->generateFeatures($offeringData['category']),
                        'location' => $merchant->city,
                        'type' => $this->mapCategoryToType($offeringData['category']),
                    ]
                );

                $offerings[] = $offering;
            }
        }

        $this->command->info('🎯 '.count($offerings).' offerings created');

        return $offerings;
    }

    private function createBookings($offerings)
    {
        $customers = User::whereHas('roles', function ($q) {
            $q->where('name', 'Customer');
        })->get();

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found, skipping sample bookings');

            return;
        }

        // For now, just log that we would create bookings
        // The actual Booking creation needs to be adjusted to match your specific Booking model structure
        $this->command->info('📅 Sample bookings skipped (model structure needs adjustment)');
    }

    private function generateFeatures($category)
    {
        $featuresByCategory = [
            'Events & Entertainment' => ['Professional Planning', 'Vendor Coordination', 'Day-of Support', 'Custom Themes'],
            'Food & Catering' => ['Fresh Ingredients', 'Dietary Options', 'Professional Service', 'Setup & Cleanup'],
            'Health & Wellness' => ['Licensed Therapists', 'Premium Products', 'Relaxing Environment', 'Customizable'],
            'Professional Services' => ['Expert Consultation', 'Detailed Reports', 'Follow-up Support', 'Proven Methods'],
            'Beauty & Personal Care' => ['Skilled Professionals', 'Premium Products', 'Latest Techniques', 'Personalized Service'],
            'Travel & Tourism' => ['Expert Guides', 'Small Groups', 'Local Insights', 'Photo Opportunities'],
        ];

        $features = $featuresByCategory[$category] ?? ['High Quality', 'Professional Service', 'Customer Satisfaction'];

        return array_slice($features, 0, rand(2, 4));
    }

    private function mapCategoryToType($category)
    {
        $typeMapping = [
            'Events & Entertainment' => 'events',
            'Food & Catering' => 'restaurant',
            'Health & Wellness' => 'experiences',
            'Professional Services' => 'conference',
            'Beauty & Personal Care' => 'experiences',
            'Travel & Tourism' => 'experiences',
        ];

        return $typeMapping[$category] ?? 'events';
    }
}
