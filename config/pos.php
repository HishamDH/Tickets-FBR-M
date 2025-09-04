<?php

return [
    /*
    |--------------------------------------------------------------------------
    | POS System Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the Point of Sale system including
    | offline mode, payment methods, receipt settings, and hardware integration.
    |
    */

    'offline_mode' => [
        'enabled' => env('POS_OFFLINE_MODE', true),
        'max_transactions' => env('POS_OFFLINE_MAX_TRANSACTIONS', 1000),
        'auto_sync_interval' => env('POS_AUTO_SYNC_INTERVAL', 300), // seconds
        'max_storage_size' => env('POS_MAX_STORAGE_SIZE', 50 * 1024 * 1024), // 50MB
        'cleanup_after_days' => env('POS_CLEANUP_AFTER_DAYS', 30),
    ],

    'payment_methods' => [
        'cash' => [
            'enabled' => true,
            'name' => 'Cash',
            'icon' => 'money-bill',
            'color' => 'green',
            'requires_change' => true,
        ],
        'card' => [
            'enabled' => true,
            'name' => 'Credit/Debit Card',
            'icon' => 'credit-card',
            'color' => 'blue',
            'requires_change' => false,
        ],
        'digital' => [
            'enabled' => true,
            'name' => 'Digital Wallet',
            'icon' => 'mobile-alt',
            'color' => 'purple',
            'requires_change' => false,
        ],
        'bank_transfer' => [
            'enabled' => false,
            'name' => 'Bank Transfer',
            'icon' => 'university',
            'color' => 'orange',
            'requires_change' => false,
        ],
    ],

    'receipt' => [
        'auto_print' => env('POS_AUTO_PRINT_RECEIPT', true),
        'print_customer_copy' => env('POS_PRINT_CUSTOMER_COPY', true),
        'print_merchant_copy' => env('POS_PRINT_MERCHANT_COPY', false),
        'include_qr_code' => env('POS_INCLUDE_QR_CODE', true),
        'include_logo' => env('POS_INCLUDE_LOGO', true),
        'footer_message' => env('POS_RECEIPT_FOOTER', 'Thank you for your business!'),
    ],

    'ticket' => [
        'auto_print' => env('POS_AUTO_PRINT_TICKET', true),
        'include_barcode' => env('POS_INCLUDE_BARCODE', true),
        'include_event_details' => env('POS_INCLUDE_EVENT_DETAILS', true),
        'include_seat_info' => env('POS_INCLUDE_SEAT_INFO', true),
        'ticket_template' => env('POS_TICKET_TEMPLATE', 'default'),
    ],

    'hardware' => [
        'cash_drawer' => [
            'enabled' => env('POS_CASH_DRAWER_ENABLED', false),
            'auto_open' => env('POS_AUTO_OPEN_DRAWER', true),
            'open_on_cash_payment' => env('POS_OPEN_ON_CASH_PAYMENT', true),
        ],
        'barcode_scanner' => [
            'enabled' => env('POS_BARCODE_SCANNER_ENABLED', false),
            'auto_focus' => env('POS_SCANNER_AUTO_FOCUS', true),
            'sound_enabled' => env('POS_SCANNER_SOUND', true),
        ],
        'customer_display' => [
            'enabled' => env('POS_CUSTOMER_DISPLAY_ENABLED', false),
            'port' => env('POS_CUSTOMER_DISPLAY_PORT', 'COM2'),
            'baud_rate' => env('POS_CUSTOMER_DISPLAY_BAUD', 9600),
        ],
    ],

    'interface' => [
        'theme' => env('POS_THEME', 'light'), // light, dark
        'language' => env('POS_LANGUAGE', 'ar'),
        'currency_symbol' => env('POS_CURRENCY_SYMBOL', 'ر.س'),
        'currency_position' => env('POS_CURRENCY_POSITION', 'after'), // before, after
        'decimal_places' => env('POS_DECIMAL_PLACES', 2),
        'enable_sound' => env('POS_ENABLE_SOUND', true),
        'enable_animations' => env('POS_ENABLE_ANIMATIONS', true),
        'keyboard_shortcuts' => env('POS_KEYBOARD_SHORTCUTS', true),
    ],

    'security' => [
        'require_manager_approval' => env('POS_REQUIRE_MANAGER_APPROVAL', false),
        'manager_approval_amount' => env('POS_MANAGER_APPROVAL_AMOUNT', 1000),
        'session_timeout' => env('POS_SESSION_TIMEOUT', 3600), // seconds
        'auto_logout' => env('POS_AUTO_LOGOUT', true),
        'log_all_transactions' => env('POS_LOG_ALL_TRANSACTIONS', true),
    ],

    'performance' => [
        'cache_products' => env('POS_CACHE_PRODUCTS', true),
        'cache_duration' => env('POS_CACHE_DURATION', 300), // seconds
        'max_recent_transactions' => env('POS_MAX_RECENT_TRANSACTIONS', 100),
        'pagination_size' => env('POS_PAGINATION_SIZE', 20),
    ],

    'reporting' => [
        'daily_report_time' => env('POS_DAILY_REPORT_TIME', '23:59'),
        'auto_email_reports' => env('POS_AUTO_EMAIL_REPORTS', false),
        'report_email' => env('POS_REPORT_EMAIL', null),
        'include_charts' => env('POS_INCLUDE_CHARTS', true),
        'export_formats' => ['pdf', 'excel', 'csv'],
    ],

    'notifications' => [
        'low_stock_alert' => env('POS_LOW_STOCK_ALERT', true),
        'low_stock_threshold' => env('POS_LOW_STOCK_THRESHOLD', 10),
        'payment_success_sound' => env('POS_PAYMENT_SUCCESS_SOUND', true),
        'error_sound' => env('POS_ERROR_SOUND', true),
        'print_success_notification' => env('POS_PRINT_SUCCESS_NOTIFICATION', true),
    ],

    'integration' => [
        'accounting_system' => env('POS_ACCOUNTING_SYSTEM', null),
        'inventory_system' => env('POS_INVENTORY_SYSTEM', null),
        'crm_system' => env('POS_CRM_SYSTEM', null),
        'webhook_url' => env('POS_WEBHOOK_URL', null),
        'api_rate_limit' => env('POS_API_RATE_LIMIT', 100), // requests per minute
    ],

    'backup' => [
        'auto_backup' => env('POS_AUTO_BACKUP', true),
        'backup_interval' => env('POS_BACKUP_INTERVAL', 86400), // seconds (daily)
        'max_backup_files' => env('POS_MAX_BACKUP_FILES', 7),
        'backup_location' => env('POS_BACKUP_LOCATION', 'local'),
        'cloud_backup' => env('POS_CLOUD_BACKUP', false),
    ],

    'features' => [
        'customer_management' => env('POS_CUSTOMER_MANAGEMENT', true),
        'loyalty_program' => env('POS_LOYALTY_PROGRAM', false),
        'discounts' => env('POS_DISCOUNTS', true),
        'tax_calculation' => env('POS_TAX_CALCULATION', true),
        'split_payment' => env('POS_SPLIT_PAYMENT', false),
        'refunds' => env('POS_REFUNDS', true),
        'exchanges' => env('POS_EXCHANGES', false),
        'gift_cards' => env('POS_GIFT_CARDS', false),
    ],

    'defaults' => [
        'tax_rate' => env('POS_DEFAULT_TAX_RATE', 15), // percentage
        'service_charge' => env('POS_DEFAULT_SERVICE_CHARGE', 0), // percentage
        'payment_method' => env('POS_DEFAULT_PAYMENT_METHOD', 'cash'),
        'receipt_email' => env('POS_DEFAULT_RECEIPT_EMAIL', false),
        'customer_required' => env('POS_CUSTOMER_REQUIRED', false),
    ],

    'limits' => [
        'max_transaction_amount' => env('POS_MAX_TRANSACTION_AMOUNT', 10000),
        'max_line_items' => env('POS_MAX_LINE_ITEMS', 50),
        'max_daily_transactions' => env('POS_MAX_DAILY_TRANSACTIONS', 1000),
        'max_cash_amount' => env('POS_MAX_CASH_AMOUNT', 5000),
    ],

    'validation' => [
        'require_email' => env('POS_REQUIRE_EMAIL', false),
        'require_phone' => env('POS_REQUIRE_PHONE', true),
        'validate_phone_format' => env('POS_VALIDATE_PHONE_FORMAT', true),
        'validate_email_format' => env('POS_VALIDATE_EMAIL_FORMAT', true),
        'phone_regex' => env('POS_PHONE_REGEX', '/^(\+966|966|0)?[5][0-9]{8}$/'),
    ],
];
