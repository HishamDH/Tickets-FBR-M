<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Thermal Printer Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for thermal receipt printers used in POS systems
    |
    */

    'thermal' => [
        'enabled' => env('THERMAL_PRINTER_ENABLED', true),
        'driver' => env('THERMAL_PRINTER_DRIVER', 'escpos'), // escpos, star, custom
        'connection' => env('THERMAL_PRINTER_CONNECTION', 'usb'), // usb, network, bluetooth, serial
        
        // USB Configuration
        'device_path' => env('THERMAL_PRINTER_USB_PATH', '/dev/usb/lp0'), // Linux: /dev/usb/lp0, Windows: LPT1
        
        // Network Configuration
        'ip_address' => env('THERMAL_PRINTER_IP', null),
        'port' => env('THERMAL_PRINTER_PORT', 9100),
        'timeout' => env('THERMAL_PRINTER_TIMEOUT', 5), // seconds
        
        // Serial Configuration
        'serial_port' => env('THERMAL_PRINTER_SERIAL_PORT', 'COM1'), // Windows: COM1, Linux: /dev/ttyUSB0
        'baud_rate' => env('THERMAL_PRINTER_BAUD_RATE', 9600),
        
        // Paper Configuration
        'paper_width' => env('THERMAL_PRINTER_PAPER_WIDTH', 58), // 58mm or 80mm
        'char_per_line' => env('THERMAL_PRINTER_CHAR_PER_LINE', 32), // Characters per line
        
        // Features
        'auto_cut' => env('THERMAL_PRINTER_AUTO_CUT', true),
        'cash_drawer' => env('THERMAL_PRINTER_CASH_DRAWER', true),
        'buzzer' => env('THERMAL_PRINTER_BUZZER', false),
        
        // Retry Configuration
        'retry_attempts' => env('THERMAL_PRINTER_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('THERMAL_PRINTER_RETRY_DELAY', 1), // seconds
        
        // Fallback Options
        'save_failed_prints' => env('THERMAL_PRINTER_SAVE_FAILED', true),
        'fallback_to_file' => env('THERMAL_PRINTER_FALLBACK_FILE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cash Drawer Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for electronic cash drawers
    |
    */

    'cash_drawer' => [
        'enabled' => env('CASH_DRAWER_ENABLED', true),
        'auto_open_on_cash_sale' => env('CASH_DRAWER_AUTO_OPEN', true),
        'pulse_duration' => env('CASH_DRAWER_PULSE_DURATION', 200), // milliseconds
        
        // Commands (ESC/POS standard)
        'open_command' => "\x1B\x70\x00\x32\xC8", // ESC p 0 50 200 - Standard cash drawer command
        'status_command' => "\x1B\x75", // ESC u - Get cash drawer status
    ],

    /*
    |--------------------------------------------------------------------------
    | Label Printer Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for label printers (tickets, badges, etc.)
    |
    */

    'label' => [
        'enabled' => env('LABEL_PRINTER_ENABLED', false),
        'driver' => env('LABEL_PRINTER_DRIVER', 'zpl'), // zpl, epl, cpcl
        'connection' => env('LABEL_PRINTER_CONNECTION', 'usb'),
        'device_path' => env('LABEL_PRINTER_USB_PATH', '/dev/usb/lp1'),
        'ip_address' => env('LABEL_PRINTER_IP', null),
        'port' => env('LABEL_PRINTER_PORT', 9100),
        
        // Label Configuration
        'label_width' => env('LABEL_PRINTER_WIDTH', 4), // inches
        'label_height' => env('LABEL_PRINTER_HEIGHT', 3), // inches
        'dpi' => env('LABEL_PRINTER_DPI', 203), // dots per inch
    ],

    /*
    |--------------------------------------------------------------------------
    | Print Job Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for queuing print jobs
    |
    */

    'queue' => [
        'enabled' => env('PRINT_QUEUE_ENABLED', true),
        'connection' => env('PRINT_QUEUE_CONNECTION', 'database'),
        'queue_name' => env('PRINT_QUEUE_NAME', 'printing'),
        'max_attempts' => env('PRINT_QUEUE_MAX_ATTEMPTS', 3),
        'batch_size' => env('PRINT_QUEUE_BATCH_SIZE', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Print Templates
    |--------------------------------------------------------------------------
    |
    | Predefined templates for different types of receipts
    |
    */

    'templates' => [
        'ticket' => [
            'header_lines' => 3,
            'footer_lines' => 3,
            'include_qr' => true,
            'include_barcode' => false,
            'logo_height' => 0, // lines
        ],
        
        'receipt' => [
            'header_lines' => 2,
            'footer_lines' => 2,
            'include_totals' => true,
            'include_payment_details' => true,
            'tax_display' => false,
        ],
        
        'report' => [
            'header_lines' => 4,
            'footer_lines' => 2,
            'include_summary' => true,
            'include_details' => true,
            'page_break' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Printer Specific Settings
    |--------------------------------------------------------------------------
    |
    | Model-specific configurations for different printer brands
    |
    */

    'printers' => [
        'epson' => [
            'init_command' => "\x1B\x40", // ESC @
            'cut_command' => "\x1B\x69", // ESC i
            'drawer_command' => "\x1B\x70\x00\x32\xC8",
            'buzzer_command' => "\x1B\x42\x05", // ESC B 5
        ],
        
        'star' => [
            'init_command' => "\x1B\x40",
            'cut_command' => "\x1B\x64\x03",
            'drawer_command' => "\x1B\x07",
            'buzzer_command' => "\x1B\x42\x05",
        ],
        
        'citizen' => [
            'init_command' => "\x1B\x40",
            'cut_command' => "\x1B\x69",
            'drawer_command' => "\x1B\x70\x00\x32\xC8",
            'buzzer_command' => "\x1B\x42\x05",
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Printer operation logging settings
    |
    */

    'logging' => [
        'enabled' => env('PRINT_LOGGING_ENABLED', true),
        'level' => env('PRINT_LOGGING_LEVEL', 'info'), // debug, info, warning, error
        'log_successful_prints' => env('PRINT_LOG_SUCCESS', true),
        'log_failed_prints' => env('PRINT_LOG_FAILURES', true),
        'log_printer_status' => env('PRINT_LOG_STATUS', false),
    ],

];
