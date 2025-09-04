<?php

namespace App\Console\Commands;

use App\Models\Merchant;
use App\Models\Service;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestSubdomainCommand extends Command
{
    protected $signature = 'test:subdomain';
    protected $description = 'Test subdomain functionality';

    public function handle()
    {
        $this->info('🧪 Testing Subdomain Functionality');
        $this->newLine();

        // Check if test merchant exists
        $user = User::where('email', 'testmerchant@example.com')->first();
        
        if (!$user) {
            $this->warn('Test merchant not found. Creating...');
            
            // Create test merchant
            $user = User::create([
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

            // Assign role
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Merchant']);
                $user->assignRole($role);
            }
        }

        // Check/create merchant profile
        $merchant = $user->merchant;
        if (!$merchant) {
            $this->warn('Merchant profile not found. Creating...');
            
            $merchant = Merchant::create([
                'user_id' => $user->id,
                'business_name' => 'مطعم الأصالة',
                'business_type' => 'مطعم',
                'cr_number' => '1234567890',
                'business_address' => 'حي الصحافة، الرياض',
                'city' => 'الرياض',
                'verification_status' => 'approved',
                'commission_rate' => 5.00,
                'subdomain' => 'asalah-restaurant',
                'primary_color' => '#2D5A27',
                'secondary_color' => '#1A4D16',
                'accent_color' => '#F59E0B',
                'subdomain_enabled' => true,
            ]);
        }

        // Update subdomain if needed
        if (!$merchant->subdomain) {
            $merchant->update([
                'subdomain' => 'asalah-restaurant',
                'primary_color' => '#2D5A27',
                'secondary_color' => '#1A4D16', 
                'accent_color' => '#F59E0B',
                'subdomain_enabled' => true,
            ]);
        }

        // Create test service if none exist
        $serviceCount = Service::where('merchant_id', $merchant->id)->count();
        if ($serviceCount === 0) {
            $this->warn('No services found. Creating test services...');
            
            Service::create([
                'merchant_id' => $merchant->id,
                'name' => 'وجبة العشاء الفاخرة',
                'description' => 'تجربة طعام استثنائية مع أفضل الأطباق التراثية والعصرية',
                'location' => 'القاعة الرئيسية',
                'price' => 150,
                'base_price' => 150,
                'currency' => 'SAR',
                'duration_hours' => 2,
                'capacity' => 100,
                'pricing_model' => 'per_person',
                'category' => 'catering',
                'service_type' => 'individual',
                'is_featured' => true,
                'is_active' => true,
                'is_available' => true,
                'online_booking_enabled' => true,
                'features' => ['بوفيه مفتوح', 'خدمة طاولة', 'موسيقى هادئة'],
            ]);
        }

        // Display results
        $this->newLine();
        $this->info('✅ Subdomain Test Results:');
        $this->table(
            ['Property', 'Value'],
            [
                ['Business Name', $merchant->business_name],
                ['Subdomain', $merchant->subdomain],
                ['Subdomain URL', $merchant->subdomain_url ?: 'Not configured'],
                ['Subdomain Enabled', $merchant->subdomain_enabled ? '✅ Yes' : '❌ No'],
                ['Primary Color', $merchant->primary_color],
                ['Logo URL', $merchant->logo_url],
                ['Services Count', $serviceCount + ($serviceCount === 0 ? 1 : 0)],
                ['Verification Status', $merchant->verification_status],
            ]
        );

        $this->newLine();
        $this->info('🌐 Test URLs:');
        $this->line('• Main site: http://localhost:8000');
        $this->line('• Subdomain: http://asalah-restaurant.localhost:8000');
        $this->line('• Login: http://localhost:8000/login (testmerchant@example.com / password)');
        
        $this->newLine();
        $this->comment('💡 To test locally, you may need to:');
        $this->comment('1. Add "127.0.0.1 asalah-restaurant.localhost" to your hosts file');
        $this->comment('2. Or use a tool like Laravel Valet for automatic subdomain handling');

        return Command::SUCCESS;
    }
}