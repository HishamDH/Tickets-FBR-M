<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SubdomainTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test merchant user
        $merchantUser = User::create([
            'name' => 'Test Merchant',
            'f_name' => 'Test',
            'l_name' => 'Merchant',
            'email' => 'testmerchant@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'merchant',
            'phone' => '1234567890',
            'city' => 'الرياض',
            'is_accepted' => true,
        ]);

        // Assign merchant role
        $merchantUser->assignRole('Merchant');

        // Create merchant profile with branding
        $merchant = Merchant::create([
            'user_id' => $merchantUser->id,
            'business_name' => 'مطعم الأصالة',
            'business_type' => 'مطعم',
            'cr_number' => '1234567890',
            'business_address' => 'حي الصحافة، الرياض',
            'city' => 'الرياض',
            'verification_status' => 'approved',
            'commission_rate' => 5.00,
            
            // Branding fields
            'subdomain' => 'asalah-restaurant',
            'primary_color' => '#2D5A27',
            'secondary_color' => '#1A4D16',
            'accent_color' => '#F59E0B',
            'subdomain_enabled' => true,
        ]);

        // Create some services for the merchant
        $services = [
            [
                'name' => 'وجبة العشاء الفاخرة',
                'description' => 'تجربة طعام استثنائية مع أفضل الأطباق التراثية والعصرية. تتضمن الوجبة مجموعة متنوعة من المقبلات والأطباق الرئيسية والحلويات.',
                'location' => 'القاعة الرئيسية',
                'price' => 150,
                'base_price' => 150,
                'currency' => 'SAR',
                'duration_hours' => 2,
                'capacity' => 100,
                'pricing_model' => 'per_person',
                'category' => 'catering',
                'service_type' => 'restaurant',
                'is_featured' => true,
                'is_active' => true,
                'is_available' => true,
                'online_booking_enabled' => true,
                'features' => ['بوفيه مفتوح', 'خدمة طاولة', 'موسيقى هادئة'],
            ],
            [
                'name' => 'وجبة الغداء السريعة',
                'description' => 'وجبة غداء سريعة ولذيذة مناسبة لفترة الاستراحة. تشمل الوجبة طبق رئيسي ومشروب وحلوى.',
                'location' => 'منطقة الوجبات السريعة',
                'price' => 45,
                'base_price' => 45,
                'currency' => 'SAR',
                'duration_hours' => 1,
                'capacity' => 200,
                'pricing_model' => 'per_person',
                'category' => 'catering',
                'service_type' => 'restaurant',
                'is_featured' => false,
                'is_active' => true,
                'is_available' => true,
                'online_booking_enabled' => true,
                'features' => ['وجبة سريعة', 'خيارات متنوعة', 'أسعار اقتصادية'],
            ],
            [
                'name' => 'حجز قاعة خاصة',
                'description' => 'قاعة خاصة مجهزة بالكامل للمناسبات والاجتماعات العائلية. تتسع لحتى 50 شخص مع إمكانية ترتيب الديكور حسب المناسبة.',
                'location' => 'القاعة الخاصة رقم 1',
                'price' => 500,
                'base_price' => 500,
                'currency' => 'SAR',
                'duration_hours' => 4,
                'capacity' => 50,
                'pricing_model' => 'fixed',
                'category' => 'venues',
                'service_type' => 'event',
                'is_featured' => true,
                'is_active' => true,
                'is_available' => true,
                'online_booking_enabled' => true,
                'features' => ['تكييف ممتاز', 'نظام صوتي', 'ديكور قابل للتخصيص', 'خدمة ضيافة'],
            ],
        ];

        foreach ($services as $serviceData) {
            $serviceData['merchant_id'] = $merchantUser->id;
            Service::create($serviceData);
        }

        $this->command->info('✅ Test merchant "asalah-restaurant" created successfully!');
        $this->command->info('🌐 You can test the subdomain at: http://asalah-restaurant.localhost:8000');
        $this->command->info('📧 Login credentials: testmerchant@example.com / password');
    }
}