<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for payment processing.
    |
    */

    // رسوم المنصة الافتراضية (نسبة مئوية)
    'platform_fee_rate' => env('PLATFORM_FEE_RATE', 1.0),

    // العملة الافتراضية
    'default_currency' => env('PAYMENT_DEFAULT_CURRENCY', 'SAR'),

    // الحد الأدنى والأقصى للمبالغ
    'min_amount' => env('PAYMENT_MIN_AMOUNT', 1),
    'max_amount' => env('PAYMENT_MAX_AMOUNT', 100000),

    // إعدادات Stripe
    'stripe' => [
        'public_key' => env('STRIPE_KEY'),
        'secret_key' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'enabled' => env('STRIPE_ENABLED', false),
    ],

    // إعدادات STC Pay
    'stc_pay' => [
        'merchant_id' => env('STC_PAY_MERCHANT_ID'),
        'api_key' => env('STC_PAY_API_KEY'),
        'webhook_secret' => env('STC_PAY_WEBHOOK_SECRET'),
        'enabled' => env('STC_PAY_ENABLED', false),
    ],

    // إعدادات التكامل البنكي
    'bank_integration' => [
        'enabled' => env('BANK_INTEGRATION_ENABLED', false),
        'api_endpoint' => env('BANK_API_ENDPOINT'),
        'api_key' => env('BANK_API_KEY'),
        'merchant_id' => env('BANK_MERCHANT_ID'),
    ],

    // إعدادات الاسترداد
    'refund' => [
        'allowed_days' => env('REFUND_ALLOWED_DAYS', 30),
        'auto_refund_enabled' => env('AUTO_REFUND_ENABLED', false),
    ],

    // إعدادات الأمان
    'security' => [
        'encrypt_credentials' => true,
        'verify_ssl' => true,
        'timeout' => 30, // ثانية
    ],

    // إعدادات الإشعارات
    'notifications' => [
        'send_email' => true,
        'send_sms' => true,
        'admin_notifications' => true,
    ],

    // بيئة الاختبار
    'test_mode' => env('PAYMENT_TEST_MODE', true),
    
    // بيانات اختبار للمحاكاة
    'test_cards' => [
        'visa_success' => '4242424242424242',
        'visa_decline' => '4000000000000002',
        'mastercard_success' => '5555555555554444',
        'mada_success' => '4000682300000000',
    ],
];
