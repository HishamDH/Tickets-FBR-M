<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'Visa',
                'code' => 'visa',
                'display_name_ar' => 'فيزا',
                'display_name_en' => 'Visa',
                'description' => 'بطاقات فيزا الائتمانية والخصم',
                'icon' => 'visa-icon.svg',
                'provider' => 'stripe',
                'transaction_fee' => 2.9,
                'fee_type' => 'percentage',
                'is_active' => true,
                'supports_refund' => true,
                'sort_order' => 1,
                'settings' => [
                    'min_amount' => 1,
                    'max_amount' => 100000,
                    'supported_currencies' => ['SAR', 'USD'],
                    'requires_3ds' => true,
                ],
            ],
            [
                'name' => 'MasterCard',
                'code' => 'mastercard',
                'display_name_ar' => 'ماستركارد',
                'display_name_en' => 'MasterCard',
                'description' => 'بطاقات ماستركارد الائتمانية والخصم',
                'icon' => 'mastercard-icon.svg',
                'provider' => 'stripe',
                'transaction_fee' => 2.9,
                'fee_type' => 'percentage',
                'is_active' => true,
                'supports_refund' => true,
                'sort_order' => 2,
                'settings' => [
                    'min_amount' => 1,
                    'max_amount' => 100000,
                    'supported_currencies' => ['SAR', 'USD'],
                    'requires_3ds' => true,
                ],
            ],
            [
                'name' => 'Mada',
                'code' => 'mada',
                'display_name_ar' => 'مدى',
                'display_name_en' => 'Mada',
                'description' => 'شبكة مدى للدفع الإلكتروني السعودية',
                'icon' => 'mada-icon.svg',
                'provider' => 'bank_integration',
                'transaction_fee' => 1.5,
                'fee_type' => 'percentage',
                'is_active' => true,
                'supports_refund' => true,
                'sort_order' => 3,
                'settings' => [
                    'min_amount' => 1,
                    'max_amount' => 50000,
                    'supported_currencies' => ['SAR'],
                    'requires_3ds' => false,
                ],
            ],
            [
                'name' => 'Apple Pay',
                'code' => 'apple_pay',
                'display_name_ar' => 'آبل باي',
                'display_name_en' => 'Apple Pay',
                'description' => 'الدفع عبر آبل باي للأجهزة المدعومة',
                'icon' => 'apple-pay-icon.svg',
                'provider' => 'stripe',
                'transaction_fee' => 2.9,
                'fee_type' => 'percentage',
                'is_active' => true,
                'supports_refund' => true,
                'sort_order' => 4,
                'settings' => [
                    'min_amount' => 1,
                    'max_amount' => 100000,
                    'supported_currencies' => ['SAR', 'USD'],
                    'requires_device_support' => true,
                ],
            ],
            [
                'name' => 'STC Pay',
                'code' => 'stc_pay',
                'display_name_ar' => 'إس تي سي باي',
                'display_name_en' => 'STC Pay',
                'description' => 'المحفظة الرقمية من stc pay',
                'icon' => 'stc-pay-icon.svg',
                'provider' => 'stc_integration',
                'transaction_fee' => 2.0,
                'fee_type' => 'percentage',
                'is_active' => true,
                'supports_refund' => true,
                'sort_order' => 5,
                'settings' => [
                    'min_amount' => 1,
                    'max_amount' => 30000,
                    'supported_currencies' => ['SAR'],
                    'requires_phone_verification' => true,
                ],
            ],
            [
                'name' => 'Bank Transfer',
                'code' => 'bank_transfer',
                'display_name_ar' => 'التحويل البنكي',
                'display_name_en' => 'Bank Transfer',
                'description' => 'التحويل المباشر من البنك',
                'icon' => 'bank-transfer-icon.svg',
                'provider' => 'manual',
                'transaction_fee' => 0,
                'fee_type' => 'fixed',
                'is_active' => true,
                'supports_refund' => false,
                'sort_order' => 6,
                'settings' => [
                    'min_amount' => 50,
                    'max_amount' => 500000,
                    'supported_currencies' => ['SAR'],
                    'requires_manual_verification' => true,
                ],
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['code' => $gateway['code']],
                $gateway
            );
        }
    }
}
