<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Merchant;
use App\Models\Service;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "🚀 بدء إنشاء البيانات التجريبية...\n";

        // Create merchants with users
        $merchants = collect();
        
        echo "📝 إنشاء التجار...\n";
        for ($i = 0; $i < 15; $i++) {
            $merchantUser = User::factory()->merchant()->create();
            $merchant = Merchant::factory()->forUser($merchantUser)->create();
            $merchants->push($merchant);
        }
        echo "✅ تم إنشاء {$merchants->count()} تاجر\n";

        // Create services for merchants
        echo "🎯 إنشاء الخدمات...\n";
        $totalServices = 0;
        $merchants->each(function ($merchant) use (&$totalServices) {
            $serviceCount = rand(3, 8);
            for ($i = 0; $i < $serviceCount; $i++) {
                Service::factory()->forMerchant($merchant)->create();
                $totalServices++;
            }
        });
        echo "✅ تم إنشاء {$totalServices} خدمة\n";

        // Create customers
        echo "👥 إنشاء العملاء...\n";
        $customers = User::factory()->customer()->count(50)->create();
        echo "✅ تم إنشاء {$customers->count()} عميل\n";

        // Create partners
        echo "🤝 إنشاء الشركاء...\n";
        $partners = User::factory()->partner()->count(8)->create();
        echo "✅ تم إنشاء {$partners->count()} شريك\n";

        echo "🎉 تم إكمال إنشاء البيانات التجريبية بنجاح!\n";
        echo "📊 الإحصائيات:\n";
        echo "   - المديرين: " . User::where('role', 'admin')->count() . "\n";
        echo "   - التجار: " . User::where('role', 'merchant')->count() . "\n";
        echo "   - العملاء: " . User::where('role', 'customer')->count() . "\n";
        echo "   - الشركاء: " . User::where('role', 'partner')->count() . "\n";
        echo "   - الخدمات: " . Service::count() . "\n";
    }
}
