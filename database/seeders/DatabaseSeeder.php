<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Merchant;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Offering;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        if (!DB::table('users')->where('email', 'admin@shubak.sa')->exists()) {
            DB::table('users')->insert([
                'name' => 'مدير النظام',
                'f_name' => 'مدير',
                'l_name' => 'النظام',
                'email' => 'admin@shubak.sa',
                'phone' => '0501234567',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "✅ تم إنشاء مدير النظام\n";

        $this->call(CategorySeeder::class);
        echo "✅ تم إنشاء الفئات\n";

        // Create payment gateways
        $gateways = [
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'display_name_ar' => 'Stripe (بطاقات ائتمانية)',
                'display_name_en' => 'Stripe (Credit Cards)',
                'description' => 'بوابة دفع عالمية تدعم جميع البطاقات الائتمانية والمحافظ الرقمية',
                'provider' => 'stripe',
                'transaction_fee' => 2.9,
                'fee_type' => 'percentage',
                'is_active' => true,
                'supports_refund' => true,
                'settings' => json_encode([
                    'public_key' => 'pk_test_example',
                    'secret_key' => 'sk_test_example',
                    'webhook_secret' => 'whsec_example',
                    'api_version' => '2022-11-15',
                ]),
                'sort_order' => 1,
            ],
            [
                'name' => 'HyperPay',
                'code' => 'hyperpay',
                'display_name_ar' => 'HyperPay (MADA & فيزا)',
                'display_name_en' => 'HyperPay (MADA & VISA)',
                'description' => 'بوابة دفع سعودية متخصصة في MADA والبطاقات المحلية',
                'provider' => 'hyperpay',
                'transaction_fee' => 2.75,
                'fee_type' => 'percentage',
                'is_active' => true,
                'supports_refund' => true,
                'settings' => json_encode([
                    'entity_id' => 'test_entity',
                    'access_token' => 'test_token',
                    'test_mode' => true,
                ]),
                'sort_order' => 2,
            ],
            [
                'name' => 'MADA',
                'code' => 'mada',
                'display_name_ar' => 'مدى',
                'display_name_en' => 'MADA',
                'description' => 'شبكة المدفوعات السعودية للبطاقات المصرفية المحلية',
                'provider' => 'hyperpay',
                'transaction_fee' => 1.5,
                'fee_type' => 'percentage',
                'is_active' => true,
                'supports_refund' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Apple Pay',
                'code' => 'apple_pay',
                'display_name_ar' => 'آبل باي',
                'display_name_en' => 'Apple Pay',
                'description' => 'محفظة رقمية من آبل للدفع السريع والآمن',
                'provider' => 'stripe',
                'transaction_fee' => 2.9,
                'fee_type' => 'percentage',
                'is_active' => true,
                'supports_refund' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'STC Pay',
                'code' => 'stc_pay',
                'display_name_ar' => 'STC Pay',
                'display_name_en' => 'STC Pay',
                'description' => 'محفظة STC الرقمية للعملاء في المملكة العربية السعودية',
                'provider' => 'stc',
                'transaction_fee' => 0.0,
                'fee_type' => 'fixed',
                'is_active' => false, // معطل حتى يتم التكامل
                'supports_refund' => false,
                'settings' => json_encode([
                    'merchant_id' => 'test_merchant',
                    'api_key' => 'test_api_key',
                    'environment' => 'sandbox'
                ]),
                'sort_order' => 5,
            ]
        ];

        foreach ($gateways as $gatewayData) {
            PaymentGateway::firstOrCreate(
                ['code' => $gatewayData['code']],
                $gatewayData
            );
        }

        echo "✅ تم إنشاء بوابات الدفع\n";

        // Create merchants with users
        $merchants = collect();
        
        for ($i = 0; $i < 15; $i++) {
            $merchantUser = User::factory()->merchant()->create();
            $merchant = Merchant::factory()->forUser($merchantUser)->create();
            $merchants->push($merchant);
        }

        echo "✅ تم إنشاء 15 تاجر\n";

        // Create services for merchants
        $services = collect();
        
        $merchants->each(function ($merchant) use (&$services) {
            $serviceCount = rand(3, 8);
            for ($i = 0; $i < $serviceCount; $i++) {
                $service = Service::factory()->forMerchant($merchant)->create();
                $services->push($service);
            }
        });

        echo "✅ تم إنشاء " . $services->count() . " خدمة\n";

        // Create offerings for merchants
        $merchantUsers = User::where('user_type', 'merchant')->get();
        $merchantUsers->each(function ($merchantUser) {
            Offering::factory()->count(rand(2, 5))->create([
                'user_id' => $merchantUser->id,
            ]);
        });

        echo "✅ تم إنشاء العروض\n";

        // Create customers
        $customers = User::factory()->customer()->count(50)->create();

        echo "✅ تم إنشاء 50 عميل\n";

        // Create partners
        $partners = User::factory()->partner()->count(8)->create();

        echo "✅ تم إنشاء 8 شريك\n";

        // Create bookings with different statuses
        $bookings = collect();

        // Recent confirmed bookings (last 30 days)
        for ($i = 0; $i < 25; $i++) {
            $customer = $customers->random();
            $service = $services->random();
            $booking = Booking::factory()
                ->forCustomer($customer)
                ->forService($service)
                ->confirmed()
                ->create();
            $bookings->push($booking);
        }

        // Upcoming pending bookings
        for ($i = 0; $i < 15; $i++) {
            $customer = $customers->random();
            $service = $services->random();
            $booking = Booking::factory()
                ->forCustomer($customer)
                ->forService($service)
                ->upcoming()
                ->pending()
                ->create();
            $bookings->push($booking);
        }

        // Completed bookings (past events)
        for ($i = 0; $i < 30; $i++) {
            $customer = $customers->random();
            $service = $services->random();
            $booking = Booking::factory()
                ->forCustomer($customer)
                ->forService($service)
                ->completed()
                ->create();
            $bookings->push($booking);
        }

        // Some cancelled bookings
        for ($i = 0; $i < 8; $i++) {
            $customer = $customers->random();
            $service = $services->random();
            $booking = Booking::factory()
                ->forCustomer($customer)
                ->forService($service)
                ->cancelled()
                ->create();
            $bookings->push($booking);
        }

        // Guest bookings (without registered customer)
        for ($i = 0; $i < 12; $i++) {
            $service = $services->random();
            $booking = Booking::factory()
                ->guest()
                ->forService($service)
                ->create();
            $bookings->push($booking);
        }

        echo "✅ تم إنشاء " . $bookings->count() . " حجز\n";

        // Create payments for confirmed and completed bookings
        $paidBookings = $bookings->filter(function ($booking) {
            return in_array($booking->booking_status, ['confirmed', 'completed']);
        });

        $paymentGateways = PaymentGateway::all();

        $paidBookings->each(function ($booking) use ($paymentGateways) {
            $gateway = $paymentGateways->random();
            Payment::factory()
                ->forBooking($booking)
                ->withGateway($gateway)
                ->completed()
                ->create();
        });

        // Create some failed payments
        for ($i = 0; $i < 5; $i++) {
            $booking = $bookings->where('payment_status', 'pending')->random();
            $gateway = $paymentGateways->random();
            Payment::factory()
                ->forBooking($booking)
                ->withGateway($gateway)
                ->failed()
                ->create();
        }

        echo "✅ تم إنشاء المدفوعات\n";

        echo "\n🎉 تم إكمال إعداد قاعدة البيانات بنجاح!\n";
        echo "📧 بيانات تسجيل الدخول للمدير:\n";
        echo "   البريد الإلكتروني: admin@shubak.sa\n";
        echo "   كلمة المرور: password\n";
    }
}
