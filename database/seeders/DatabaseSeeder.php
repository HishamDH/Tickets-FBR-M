<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Merchant;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@shubak.sa',
            'phone' => '0501234567',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        echo "✅ تم إنشاء مدير النظام\n";

        // Create payment gateways
        PaymentGateway::factory()->stripe()->active()->create();
        PaymentGateway::factory()->hyperpay()->active()->create();
        PaymentGateway::factory()->tap()->active()->create();
        PaymentGateway::factory()->stcPay()->active()->create();

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
