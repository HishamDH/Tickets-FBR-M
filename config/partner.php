<?php

return [
    /**
     * إعدادات نظام الشركاء المتقدم
     */

    // معدلات العمولة
    'commission_settings' => [
        'default_rate' => 5.0,              // معدل افتراضي 5%
        'max_rate' => 15.0,                 // أقصى معدل مسموح
        'min_rate' => 2.0,                  // أدنى معدل مسموح
        'performance_bonus_rate' => 2.0,    // معدل مكافأة الأداء الإضافي
    ],

    // إعدادات السحب المتقدمة
    'withdrawal_settings' => [
        'minimum_amount' => 50.0,           // الحد الأدنى للسحب
        'maximum_amount' => 50000.0,        // الحد الأقصى للسحب الواحد
        'daily_limit' => 10000.0,           // الحد اليومي للسحب
        'monthly_limit' => 100000.0,        // الحد الشهري للسحب
        'processing_days' => 3,             // أيام معالجة السحب
        'fee_percentage' => 2.5,            // نسبة رسوم السحب
        'require_verification_above' => 5000.0, // يتطلب تحقق للمبالغ الأكبر
        'auto_approval_limit' => 1000.0,    // حد الموافقة التلقائية
        'hold_period_days' => 7,            // فترة احتجاز العمولات
        
        // السحب التلقائي
        'auto_withdrawal' => [
            'enabled' => true,
            'default_threshold' => 500.0,
            'max_threshold' => 2000.0,
            'schedule' => 'weekly',           // يومي، أسبوعي، شهري
        ],
    ],

    // نظام مستويات الشركاء المحدث
    'partner_tiers' => [
        'bronze' => [
            'name' => 'برونزي',
            'min_merchants' => 0,
            'min_revenue' => 0,
            'commission_rate' => 3.0,
            'bonus_multiplier' => 1.0,
            'withdrawal_fee' => 3.0,
            'benefits' => [
                'basic_reports' => true,
                'email_support' => true,
                'monthly_payout' => true,
            ],
        ],
        'silver' => [
            'name' => 'فضي',
            'min_merchants' => 5,
            'min_revenue' => 25000,
            'commission_rate' => 4.0,
            'bonus_multiplier' => 1.2,
            'withdrawal_fee' => 2.5,
            'benefits' => [
                'advanced_reports' => true,
                'priority_support' => true,
                'weekly_payout' => true,
                'performance_insights' => true,
            ],
        ],
        'gold' => [
            'name' => 'ذهبي',
            'min_merchants' => 15,
            'min_revenue' => 75000,
            'commission_rate' => 5.0,
            'bonus_multiplier' => 1.5,
            'withdrawal_fee' => 2.0,
            'benefits' => [
                'comprehensive_analytics' => true,
                'dedicated_support' => true,
                'daily_payout' => true,
                'custom_commission_rates' => true,
                'api_access' => true,
            ],
        ],
        'platinum' => [
            'name' => 'بلاتيني',
            'min_merchants' => 30,
            'min_revenue' => 150000,
            'commission_rate' => 6.0,
            'bonus_multiplier' => 2.0,
            'withdrawal_fee' => 1.5,
            'benefits' => [
                'real_time_analytics' => true,
                'account_manager' => true,
                'instant_payout' => true,
                'negotiable_rates' => true,
                'white_label_options' => true,
                'priority_api_access' => true,
            ],
        ],
        'diamond' => [
            'name' => 'ماسي',
            'min_merchants' => 50,
            'min_revenue' => 300000,
            'commission_rate' => 7.0,
            'bonus_multiplier' => 2.5,
            'withdrawal_fee' => 1.0,
            'benefits' => [
                'enterprise_features' => true,
                'custom_integrations' => true,
                'revenue_sharing' => true,
                'exclusive_offers' => true,
                'personal_consultant' => true,
            ],
        ],
    ],

    // مقاييس وأهداف الأداء
    'performance_metrics' => [
        // عتبات التقييم
        'rating_thresholds' => [
            'excellent' => 90,
            'good' => 70,
            'average' => 50,
            'poor' => 30,
        ],
        
        // أهداف شهرية
        'monthly_targets' => [
            'new_merchants' => 3,
            'total_revenue' => 25000,
            'bookings_count' => 100,
            'retention_rate' => 80,
            'satisfaction_score' => 4.0,
        ],
        
        // أوزان التقييم
        'evaluation_weights' => [
            'referral_success_rate' => 30,
            'merchant_retention' => 25,
            'revenue_growth' => 20,
            'customer_satisfaction' => 15,
            'activity_consistency' => 10,
        ],
    ],

    // نظام المكافآت والحوافز
    'rewards_system' => [
        'referral_bonuses' => [
            'new_merchant' => 200.0,         // مكافأة تاجر جديد
            'first_sale' => 100.0,           // مكافأة أول بيعة
            'milestone_achievements' => [
                5 => 500.0,                  // 5 تجار
                10 => 1200.0,                // 10 تجار
                25 => 3000.0,                // 25 تاجر
                50 => 7500.0,                // 50 تاجر
            ],
        ],
        
        'performance_bonuses' => [
            'monthly_top_performer' => 2000.0,
            'quarterly_excellence' => 5000.0,
            'annual_champion' => 15000.0,
            'retention_master' => 1000.0,    // للاحتفاظ العالي
        ],
        
        // مكافآت خاصة
        'special_incentives' => [
            'new_year_bonus' => true,
            'ramadan_special' => true,
            'loyalty_rewards' => true,
            'referral_contests' => true,
        ],
    ],

    // إعدادات التقارير والتحليلات
    'reporting' => [
        'enabled' => true,
        'auto_generation' => true,
        'formats' => ['pdf', 'excel', 'csv', 'json'],
        
        'report_types' => [
            'daily_summary' => [
                'enabled' => true,
                'auto_send' => true,
                'recipients' => 'partner',
            ],
            'weekly_analysis' => [
                'enabled' => true,
                'auto_send' => true,
                'recipients' => 'partner',
                'day' => 'monday',
            ],
            'monthly_comprehensive' => [
                'enabled' => true,
                'auto_send' => true,
                'recipients' => 'partner,admin',
                'include_forecasting' => true,
            ],
            'quarterly_review' => [
                'enabled' => true,
                'auto_send' => false,
                'recipients' => 'partner,management',
                'detailed_analysis' => true,
            ],
        ],
        
        'data_retention' => [
            'reports_days' => 365,
            'analytics_days' => 730,
            'archived_years' => 3,
        ],
    ],

    // إعدادات الإشعارات المتقدمة
    'notifications' => [
        'channels' => ['email', 'sms', 'push', 'webhook'],
        
        'email_settings' => [
            'new_commission' => true,
            'withdrawal_processed' => true,
            'goal_achieved' => true,
            'tier_upgraded' => true,
            'monthly_report' => true,
            'performance_alert' => true,
            'merchant_milestone' => true,
            'payment_reminder' => true,
        ],
        
        'sms_settings' => [
            'withdrawal_approved' => true,
            'urgent_alerts' => true,
            'security_notifications' => true,
        ],
        
        'timing' => [
            'digest_frequency' => 'weekly',
            'quiet_hours_start' => '22:00',
            'quiet_hours_end' => '07:00',
            'weekend_notifications' => false,
        ],
    ],

    // إعدادات الأمان المتقدمة
    'security' => [
        'authentication' => [
            'require_2fa' => false,
            'password_strength' => 'medium',
            'session_timeout' => 120,         // دقائق
            'max_login_attempts' => 5,
            'lockout_duration' => 30,         // دقائق
        ],
        
        'withdrawal_security' => [
            'require_2fa_above' => 5000.0,
            'require_email_confirmation' => true,
            'require_sms_confirmation' => true,
            'cooling_period' => 24,           // ساعات
            'suspicious_activity_freeze' => true,
        ],
        
        'audit_logging' => [
            'enabled' => true,
            'events' => [
                'login', 'logout', 'withdrawal_request',
                'commission_update', 'profile_change',
                'api_access', 'report_download',
            ],
            'retention_days' => 365,
        ],
        
        'ip_restrictions' => [
            'enabled' => false,
            'whitelist' => [],
            'country_restrictions' => [],
        ],
    ],

    // إعدادات API المتقدمة
    'api' => [
        'enabled' => true,
        'version' => 'v1',
        'rate_limiting' => [
            'requests_per_minute' => 100,
            'burst_limit' => 200,
            'throttle_strategy' => 'sliding_window',
        ],
        
        'authentication' => [
            'method' => 'sanctum',
            'token_expiry' => 525600,         // دقائق (سنة)
            'refresh_tokens' => true,
        ],
        
        'webhooks' => [
            'enabled' => true,
            'timeout' => 30,                  // ثواني
            'retry_attempts' => 3,
            'retry_delay' => 60,              // ثواني
            'signature_verification' => true,
        ],
        
        'documentation' => [
            'swagger_enabled' => true,
            'public_docs' => false,
            'sandbox_mode' => true,
        ],
    ],

    // إعدادات التكامل
    'integrations' => [
        'payment_gateways' => [
            'mada' => ['enabled' => true, 'fees' => 2.5],
            'visa' => ['enabled' => true, 'fees' => 3.0],
            'mastercard' => ['enabled' => true, 'fees' => 3.0],
            'apple_pay' => ['enabled' => true, 'fees' => 2.9],
            'stc_pay' => ['enabled' => true, 'fees' => 2.0],
        ],
        
        'banks' => [
            'rajhi' => 'مصرف الراجحي',
            'ahli' => 'البنك الأهلي السعودي',
            'sab' => 'البنك السعودي البريطاني',
            'riyadh' => 'بنك الرياض',
            'anb' => 'البنك العربي الوطني',
            'samba' => 'بنك سامبا',
            'alinma' => 'بنك الإنماء',
            'albilad' => 'بنك البلاد',
            'jazira' => 'بنك الجزيرة',
            'investment' => 'البنك السعودي للاستثمار',
        ],
        
        'external_services' => [
            'crm_sync' => false,
            'analytics_platforms' => ['google_analytics'],
            'marketing_automation' => false,
        ],
    ],

    // إعدادات النظام والصيانة
    'system' => [
        'maintenance_mode' => false,
        'cache_duration' => 300,            // ثواني
        'queue_processing' => true,
        'auto_cleanup' => true,
        
        'performance' => [
            'database_optimization' => true,
            'query_caching' => true,
            'response_compression' => true,
            'cdn_enabled' => false,
        ],
        
        'backup' => [
            'enabled' => true,
            'frequency' => 'daily',
            'retention_days' => 30,
            'encrypt_backups' => true,
        ],
    ],

    // إعدادات التخصيص والواجهة
    'ui_customization' => [
        'dashboard_widgets' => [
            'earnings_overview' => true,
            'performance_metrics' => true,
            'recent_referrals' => true,
            'goal_progress' => true,
            'top_merchants' => true,
            'quick_actions' => true,
            'news_updates' => true,
        ],
        
        'theme_settings' => [
            'default_theme' => 'light',
            'dark_mode_available' => true,
            'custom_branding' => false,
            'localization' => 'ar',
        ],
        
        'accessibility' => [
            'high_contrast' => true,
            'font_size_adjustment' => true,
            'keyboard_navigation' => true,
            'screen_reader_support' => true,
        ],
    ],

    // إعدادات الامتثال والقانونية
    'compliance' => [
        'tax_reporting' => [
            'enabled' => true,
            'auto_generate_1099' => false,
            'quarterly_reports' => true,
        ],
        
        'data_protection' => [
            'gdpr_compliance' => true,
            'data_retention_policy' => true,
            'right_to_deletion' => true,
            'data_export' => true,
        ],
        
        'financial_regulations' => [
            'aml_compliance' => true,
            'suspicious_activity_reporting' => true,
            'transaction_limits' => true,
        ],
    ],

    // متطلبات التحقق المحدثة
    'verification_requirements' => [
        'basic_tier' => [
            'national_id' => true,
            'phone_number' => true,
            'email_verification' => true,
        ],
        'advanced_tier' => [
            'bank_account' => true,
            'address_proof' => true,
            'business_license' => false,
        ],
        'premium_tier' => [
            'tax_certificate' => true,
            'business_registration' => true,
            'financial_statements' => false,
        ],
    ],

    // معدلات وحدود مختلفة حسب المنطقة
    'regional_settings' => [
        'saudi_arabia' => [
            'currency' => 'SAR',
            'tax_rate' => 15.0,              // ضريبة القيمة المضافة
            'min_payout' => 100.0,
            'banking_days' => [1, 2, 3, 4, 5], // الأحد إلى الخميس
        ],
    ],

    // الحالات والخصائص المسموحة
    'allowed_statuses' => [
        'pending' => 'قيد المراجعة',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'suspended' => 'موقوف',
        'terminated' => 'منتهي',
    ],

    // أنواع العمولات المتاحة
    'commission_types' => [
        'booking_commission' => 'عمولة حجز',
        'referral_bonus' => 'مكافأة إحالة',
        'performance_bonus' => 'مكافأة أداء',
        'milestone_reward' => 'مكافأة إنجاز',
        'loyalty_bonus' => 'مكافأة ولاء',
        'special_promotion' => 'عرض خاص',
    ],
];
