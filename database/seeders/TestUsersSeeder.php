<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Partner;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==============================================
        // 🔧 CREATE ROLES AND PERMISSIONS FIRST
        // ==============================================
        
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $merchantRole = Role::firstOrCreate(['name' => 'Merchant', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'web']);
        $partnerRole = Role::firstOrCreate(['name' => 'Partner', 'guard_name' => 'web']);

        // ==============================================
        // 🔧 ADMIN ACCOUNTS
        // ==============================================
        
        $admin1 = User::create([
            'name' => 'مدير النظام',
            'f_name' => 'مدير',
            'l_name' => 'النظام',
            'email' => 'admin@tickets-fbr.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        $admin1->assignRole($adminRole);

        $admin2 = User::create([
            'name' => 'أحمد المدير',
            'f_name' => 'أحمد',
            'l_name' => 'المدير',
            'email' => 'ahmed.admin@tickets-fbr.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        $admin2->assignRole($adminRole);

        // ==============================================
        // 👥 CUSTOMER ACCOUNTS
        // ==============================================
        
        $customer1 = User::create([
            'name' => 'محمد العميل',
            'f_name' => 'محمد',
            'l_name' => 'العميل',
            'email' => 'customer1@test.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
            'user_type' => 'customer',
            'phone' => '+966501234567',
            'email_verified_at' => now(),
        ]);
        $customer1->assignRole($customerRole);

        $customer2 = User::create([
            'name' => 'فاطمة أحمد',
            'f_name' => 'فاطمة',
            'l_name' => 'أحمد',
            'email' => 'customer2@test.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
            'user_type' => 'customer',
            'phone' => '+966507654321',
            'email_verified_at' => now(),
        ]);
        $customer2->assignRole($customerRole);

        // ==============================================
        // 🏪 MERCHANT ACCOUNTS
        // ==============================================

        // ✅ APPROVED MERCHANT
        $approvedMerchant = User::create([
            'name' => 'تاجر مفعل',
            'f_name' => 'تاجر',
            'l_name' => 'مفعل',
            'email' => 'merchant.approved@test.com',
            'password' => Hash::make('merchant123'),
            'role' => 'merchant',
            'user_type' => 'merchant',
            'business_name' => 'شركة التجارة المفعلة',
            'phone' => '+966511111111',
            'commercial_registration_number' => 'CR123456789',
            'tax_number' => 'TAX987654321',
            'business_city' => 'الرياض',
            'merchant_status' => 'approved',
            'email_verified_at' => now(),
        ]);
        $approvedMerchant->assignRole($merchantRole);

        Merchant::create([
            'user_id' => $approvedMerchant->id,
            'business_name' => 'شركة التجارة المفعلة',
            'business_type' => 'services',
            'cr_number' => 'CR123456789',
            'city' => 'الرياض',
            'verification_status' => 'approved',
        ]);

        // ⏳ PENDING MERCHANT
        $pendingMerchant = User::create([
            'name' => 'تاجر قيد المراجعة',
            'f_name' => 'تاجر',
            'l_name' => 'قيد المراجعة',
            'email' => 'merchant.pending@test.com',
            'password' => Hash::make('merchant123'),
            'role' => 'merchant',
            'user_type' => 'merchant',
            'business_name' => 'شركة قيد المراجعة',
            'phone' => '+966522222222',
            'commercial_registration_number' => 'CR111111111',
            'tax_number' => 'TAX111111111',
            'business_city' => 'جدة',
            'merchant_status' => 'pending',
            'email_verified_at' => now(),
        ]);
        $pendingMerchant->assignRole($merchantRole);

        Merchant::create([
            'user_id' => $pendingMerchant->id,
            'business_name' => 'شركة قيد المراجعة',
            'business_type' => 'retail',
            'cr_number' => 'CR111111111',
            'city' => 'جدة',
            'verification_status' => 'pending',
        ]);

        // ❌ REJECTED MERCHANT
        $rejectedMerchant = User::create([
            'name' => 'تاجر مرفوض',
            'f_name' => 'تاجر',
            'l_name' => 'مرفوض',
            'email' => 'merchant.rejected@test.com',
            'password' => Hash::make('merchant123'),
            'role' => 'merchant',
            'user_type' => 'merchant',
            'business_name' => 'شركة مرفوضة',
            'phone' => '+966533333333',
            'commercial_registration_number' => 'CR222222222',
            'tax_number' => 'TAX222222222',
            'business_city' => 'الدمام',
            'merchant_status' => 'rejected',
            'verification_notes' => 'البيانات التجارية غير صحيحة، يرجى إعادة التقديم ببيانات صحيحة',
            'email_verified_at' => now(),
        ]);
        $rejectedMerchant->assignRole($merchantRole);

        Merchant::create([
            'user_id' => $rejectedMerchant->id,
            'business_name' => 'شركة مرفوضة',
            'business_type' => 'other',
            'cr_number' => 'CR222222222',
            'city' => 'الدمام',
            'verification_status' => 'rejected',
        ]);

        // ⚠️ SUSPENDED MERCHANT
        $suspendedMerchant = User::create([
            'name' => 'تاجر معلق',
            'f_name' => 'تاجر',
            'l_name' => 'معلق',
            'email' => 'merchant.suspended@test.com',
            'password' => Hash::make('merchant123'),
            'role' => 'merchant',
            'user_type' => 'merchant',
            'business_name' => 'شركة معلقة',
            'phone' => '+966544444444',
            'commercial_registration_number' => 'CR333333333',
            'tax_number' => 'TAX333333333',
            'business_city' => 'المدينة المنورة',
            'merchant_status' => 'suspended',
            'verification_notes' => 'تم تعليق الحساب بسبب مخالفة الشروط والأحكام',
            'email_verified_at' => now(),
        ]);
        $suspendedMerchant->assignRole($merchantRole);

        Merchant::create([
            'user_id' => $suspendedMerchant->id,
            'business_name' => 'شركة معلقة',
            'business_type' => 'entertainment',
            'cr_number' => 'CR333333333',
            'city' => 'المدينة المنورة',
            'verification_status' => 'rejected', // Using rejected instead of suspended for merchant table
        ]);

        // ==============================================
        // 🤝 PARTNER ACCOUNTS
        // ==============================================

        // ✅ ACTIVE PARTNER
        $activePartner = User::create([
            'name' => 'شريك نشط',
            'f_name' => 'شريك',
            'l_name' => 'نشط',
            'email' => 'partner.active@test.com',
            'password' => Hash::make('partner123'),
            'role' => 'partner',
            'user_type' => 'partner',
            'phone' => '+966555555555',
            'email_verified_at' => now(),
        ]);
        $activePartner->assignRole($partnerRole);

        Partner::create([
            'user_id' => $activePartner->id,
            'partner_code' => 'PART001',
            'commission_rate' => 15.00,
            'status' => 'active',
            'business_name' => 'شركة الشراكة النشطة',
            'contact_person' => 'شريك نشط',
        ]);

        // ⏳ INACTIVE PARTNER
        $inactivePartner = User::create([
            'name' => 'شريك غير نشط',
            'f_name' => 'شريك',
            'l_name' => 'غير نشط',
            'email' => 'partner.inactive@test.com',
            'password' => Hash::make('partner123'),
            'role' => 'partner',
            'user_type' => 'partner',
            'phone' => '+966566666666',
            'email_verified_at' => now(),
        ]);
        $inactivePartner->assignRole($partnerRole);

        Partner::create([
            'user_id' => $inactivePartner->id,
            'partner_code' => 'PART002',
            'commission_rate' => 10.00,
            'status' => 'inactive',
            'business_name' => 'شركة الشراكة غير النشطة',
            'contact_person' => 'شريك غير نشط',
        ]);

        // ⚠️ SUSPENDED PARTNER
        $suspendedPartner = User::create([
            'name' => 'شريك معلق',
            'f_name' => 'شريك',
            'l_name' => 'معلق',
            'email' => 'partner.suspended@test.com',
            'password' => Hash::make('partner123'),
            'role' => 'partner',
            'user_type' => 'partner',
            'phone' => '+966577777777',
            'email_verified_at' => now(),
        ]);
        $suspendedPartner->assignRole($partnerRole);

        Partner::create([
            'user_id' => $suspendedPartner->id,
            'partner_code' => 'PART003',
            'commission_rate' => 12.00,
            'status' => 'suspended',
            'business_name' => 'شركة الشراكة المعلقة',
            'contact_person' => 'شريك معلق',
        ]);

        // 🆕 PARTNER WITHOUT PROFILE
        $partnerNoProfile = User::create([
            'name' => 'شريك بدون ملف',
            'f_name' => 'شريك',
            'l_name' => 'بدون ملف',
            'email' => 'partner.noprofile@test.com',
            'password' => Hash::make('partner123'),
            'role' => 'partner',
            'user_type' => 'partner',
            'phone' => '+966588888888',
            'email_verified_at' => now(),
        ]);
        $partnerNoProfile->assignRole($partnerRole);
        // Note: No Partner record created for this user - will show "incomplete profile" status
    }
}
