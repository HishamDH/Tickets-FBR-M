<?php

namespace App\Services;

use App\Models\PaidReservation;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ThermalPrinterService
{
    protected $printerConfig;
    protected $paperWidth = 58; // 58mm thermal paper
    protected $charPerLine = 32; // Characters per line for 58mm paper
    
    public function __construct()
    {
        $this->printerConfig = config('printing.thermal', [
            'enabled' => true,
            'driver' => 'escpos', // ESC/POS commands
            'connection' => 'usb', // usb, network, bluetooth
            'device_path' => '/dev/usb/lp0', // Linux USB path
            'ip_address' => null, // For network printers
            'port' => 9100, // Network printer port
        ]);
    }

    /**
     * Print ticket receipt
     */
    public function printTicket(PaidReservation $reservation): bool
    {
        try {
            $receiptContent = $this->generateTicketReceipt($reservation);
            return $this->sendToPrinter($receiptContent);
        } catch (\Exception $e) {
            Log::error('Thermal printer error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Print sales receipt
     */
    public function printSalesReceipt(PaidReservation $reservation): bool
    {
        try {
            $receiptContent = $this->generateSalesReceipt($reservation);
            return $this->sendToPrinter($receiptContent);
        } catch (\Exception $e) {
            Log::error('Thermal printer error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Print end of day report
     */
    public function printDailyReport(User $merchant, array $salesData): bool
    {
        try {
            $reportContent = $this->generateDailyReport($merchant, $salesData);
            return $this->sendToPrinter($reportContent);
        } catch (\Exception $e) {
            Log::error('Thermal printer error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Batch print multiple tickets
     */
    public function batchPrintTickets(array $reservations): array
    {
        $results = [];
        
        foreach ($reservations as $reservation) {
            $results[] = [
                'reservation_id' => $reservation->id,
                'success' => $this->printTicket($reservation),
            ];
        }
        
        return $results;
    }

    /**
     * Generate ticket receipt content
     */
    protected function generateTicketReceipt(PaidReservation $reservation): string
    {
        $content = '';
        $line = str_repeat('-', $this->charPerLine);
        
        // Header
        $content .= $this->centerText('شباك التذاكر') . "\n";
        $content .= $this->centerText('TICKET WINDOW') . "\n";
        $content .= $line . "\n";
        
        // Merchant info
        if ($reservation->offering && $reservation->offering->user) {
            $merchant = $reservation->offering->user;
            $content .= $this->centerText($merchant->business_name ?? $merchant->name) . "\n";
        }
        
        $content .= $line . "\n";
        
        // Ticket details
        $content .= "رقم التذكرة: " . $reservation->id . "\n";
        $content .= "Ticket #: " . $reservation->id . "\n";
        
        if ($reservation->offering) {
            $content .= "الخدمة: " . $reservation->offering->title . "\n";
            $content .= "Service: " . $reservation->offering->title . "\n";
        }
        
        $content .= "التاريخ: " . now()->format('Y-m-d H:i') . "\n";
        $content .= "Date: " . now()->format('Y-m-d H:i') . "\n";
        
        $content .= $line . "\n";
        
        // Price details
        $content .= sprintf("السعر: %s ريال\n", number_format($reservation->price, 2));
        $content .= sprintf("Price: %s SAR\n", number_format($reservation->price, 2));
        
        // Payment method
        $additionalData = $reservation->additional_data ?? [];
        if (isset($additionalData['payment']['method'])) {
            $paymentMethod = $additionalData['payment']['method'];
            $content .= "طريقة الدفع: " . $this->getPaymentMethodArabic($paymentMethod) . "\n";
            $content .= "Payment: " . ucfirst($paymentMethod) . "\n";
        }
        
        $content .= $line . "\n";
        
        // QR Code placeholder (in real implementation, you'd generate actual QR)
        $content .= $this->centerText('QR CODE') . "\n";
        $content .= $this->centerText('█████████') . "\n";
        $content .= $this->centerText('█ ███ █ █') . "\n";
        $content .= $this->centerText('█ ███ █ █') . "\n";
        $content .= $this->centerText('█████████') . "\n";
        $content .= $this->centerText($reservation->verification_code ?? $reservation->id) . "\n";
        
        $content .= $line . "\n";
        
        // Footer
        $content .= $this->centerText('شكراً لاختياركم خدماتنا') . "\n";
        $content .= $this->centerText('Thank you for choosing us') . "\n";
        $content .= $this->centerText('www.shobaktickets.com') . "\n";
        
        // Cut paper command
        $content .= "\x1B\x69"; // ESC i - Cut paper
        
        return $content;
    }

    /**
     * Generate sales receipt content
     */
    protected function generateSalesReceipt(PaidReservation $reservation): string
    {
        $content = '';
        $line = str_repeat('-', $this->charPerLine);
        
        // Header
        $content .= $this->centerText('إيصال بيع') . "\n";
        $content .= $this->centerText('SALES RECEIPT') . "\n";
        $content .= $line . "\n";
        
        // Receipt details
        $additionalData = $reservation->additional_data ?? [];
        if (isset($additionalData['receipt_number'])) {
            $content .= "رقم الإيصال: " . $additionalData['receipt_number'] . "\n";
        }
        
        $content .= "التاريخ: " . now()->format('Y-m-d H:i') . "\n";
        $content .= "الكاشير: " . auth()->user()->name . "\n";
        
        $content .= $line . "\n";
        
        // Items
        if ($reservation->offering) {
            $content .= sprintf("%-20s %8s\n", 
                $this->truncateText($reservation->offering->title, 20),
                number_format($reservation->price, 2)
            );
        }
        
        $content .= $line . "\n";
        
        // Totals
        $content .= sprintf("%-20s %8s\n", "المجموع الفرعي:", number_format($reservation->price, 2));
        
        if (isset($additionalData['payment']['discount']) && $additionalData['payment']['discount'] > 0) {
            $discount = $additionalData['payment']['discount'];
            $content .= sprintf("%-20s -%7s\n", "خصم:", number_format($discount, 2));
        }
        
        $content .= sprintf("%-20s %8s\n", "الإجمالي:", number_format($reservation->price, 2));
        
        // Payment details
        if (isset($additionalData['payment'])) {
            $payment = $additionalData['payment'];
            $content .= $line . "\n";
            
            if ($payment['method'] === 'cash') {
                $content .= sprintf("%-20s %8s\n", "المبلغ المدفوع:", number_format($payment['cash_received'] ?? 0, 2));
                $content .= sprintf("%-20s %8s\n", "الباقي:", number_format($payment['change'] ?? 0, 2));
            } else {
                $content .= sprintf("%-20s %8s\n", "طريقة الدفع:", $this->getPaymentMethodArabic($payment['method']));
            }
        }
        
        $content .= $line . "\n";
        $content .= $this->centerText('شكراً لزيارتكم') . "\n";
        
        // Cut paper
        $content .= "\x1B\x69";
        
        return $content;
    }

    /**
     * Generate daily report content
     */
    protected function generateDailyReport(User $merchant, array $salesData): string
    {
        $content = '';
        $line = str_repeat('-', $this->charPerLine);
        
        // Header
        $content .= $this->centerText('تقرير يومي') . "\n";
        $content .= $this->centerText('DAILY REPORT') . "\n";
        $content .= $line . "\n";
        
        // Merchant info
        $content .= "التاجر: " . $merchant->name . "\n";
        $content .= "التاريخ: " . now()->format('Y-m-d') . "\n";
        $content .= "الوقت: " . now()->format('H:i') . "\n";
        
        $content .= $line . "\n";
        
        // Sales summary
        $content .= "ملخص المبيعات:\n";
        $content .= sprintf("%-20s %8s\n", "عدد المعاملات:", $salesData['total_transactions'] ?? 0);
        $content .= sprintf("%-20s %8s\n", "إجمالي المبيعات:", number_format($salesData['total_sales'] ?? 0, 2));
        $content .= sprintf("%-20s %8s\n", "المبيعات النقدية:", number_format($salesData['cash_sales'] ?? 0, 2));
        $content .= sprintf("%-20s %8s\n", "مبيعات البطاقات:", number_format($salesData['card_sales'] ?? 0, 2));
        
        $content .= $line . "\n";
        
        // Payment breakdown
        if (isset($salesData['payment_methods'])) {
            $content .= "طرق الدفع:\n";
            foreach ($salesData['payment_methods'] as $method => $amount) {
                $content .= sprintf("%-20s %8s\n", 
                    $this->getPaymentMethodArabic($method),
                    number_format($amount, 2)
                );
            }
            $content .= $line . "\n";
        }
        
        // Footer
        $content .= $this->centerText('نهاية التقرير') . "\n";
        $content .= $this->centerText('-- تم طباعته تلقائياً --') . "\n";
        
        // Cut paper
        $content .= "\x1B\x69";
        
        return $content;
    }

    /**
     * Send content to thermal printer
     */
    protected function sendToPrinter(string $content): bool
    {
        if (!$this->printerConfig['enabled']) {
            Log::info('Thermal printer disabled, saving to file instead');
            return $this->saveToFile($content);
        }

        try {
            switch ($this->printerConfig['connection']) {
                case 'usb':
                    return $this->sendViaUSB($content);
                case 'network':
                    return $this->sendViaNetwork($content);
                default:
                    Log::warning('Unknown printer connection type: ' . $this->printerConfig['connection']);
                    return $this->saveToFile($content);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send to printer: ' . $e->getMessage());
            return $this->saveToFile($content);
        }
    }

    /**
     * Send via USB connection
     */
    protected function sendViaUSB(string $content): bool
    {
        $devicePath = $this->printerConfig['device_path'];
        
        if (!file_exists($devicePath)) {
            Log::warning('Printer device not found: ' . $devicePath);
            return false;
        }

        $handle = fopen($devicePath, 'w');
        if (!$handle) {
            Log::error('Cannot open printer device: ' . $devicePath);
            return false;
        }

        // Initialize printer
        fwrite($handle, "\x1B\x40"); // ESC @ - Initialize printer
        
        // Send content
        fwrite($handle, $content);
        
        fclose($handle);
        return true;
    }

    /**
     * Send via network connection
     */
    protected function sendViaNetwork(string $content): bool
    {
        $ip = $this->printerConfig['ip_address'];
        $port = $this->printerConfig['port'];
        
        if (!$ip) {
            Log::error('Network printer IP not configured');
            return false;
        }

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$socket) {
            Log::error('Cannot create socket for network printer');
            return false;
        }

        if (!socket_connect($socket, $ip, $port)) {
            Log::error('Cannot connect to network printer: ' . $ip . ':' . $port);
            socket_close($socket);
            return false;
        }

        // Initialize printer
        socket_write($socket, "\x1B\x40"); // ESC @ - Initialize printer
        
        // Send content
        socket_write($socket, $content);
        
        socket_close($socket);
        return true;
    }

    /**
     * Save to file as fallback
     */
    protected function saveToFile(string $content): bool
    {
        $filename = 'prints/receipt_' . now()->format('Y-m-d_H-i-s') . '_' . uniqid() . '.txt';
        
        try {
            Storage::disk('local')->put($filename, $content);
            Log::info('Receipt saved to file: ' . $filename);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to save receipt to file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Center text for thermal printer
     */
    protected function centerText(string $text): string
    {
        $textLength = mb_strlen($text);
        if ($textLength >= $this->charPerLine) {
            return $text;
        }
        
        $spaces = floor(($this->charPerLine - $textLength) / 2);
        return str_repeat(' ', $spaces) . $text;
    }

    /**
     * Truncate text to fit printer width
     */
    protected function truncateText(string $text, int $maxLength): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }
        
        return mb_substr($text, 0, $maxLength - 3) . '...';
    }

    /**
     * Get Arabic payment method name
     */
    protected function getPaymentMethodArabic(string $method): string
    {
        $methods = [
            'cash' => 'نقدي',
            'card' => 'بطاقة',
            'mixed' => 'مختلط',
            'credit' => 'ائتمان',
            'debit' => 'خصم',
            'digital' => 'محفظة رقمية',
            'transfer' => 'تحويل بنكي',
        ];

        return $methods[$method] ?? $method;
    }

    /**
     * Test printer connection
     */
    public function testPrinter(): bool
    {
        try {
            $testContent = $this->escp->initialize() .
                $this->escp->textBold(true) .
                $this->centerText('اختبار الطابعة') . "\n" .
                $this->escp->textBold(false) .
                str_repeat('=', $this->charPerLine) . "\n" .
                "تاريخ الاختبار: " . now()->format('Y-m-d H:i:s') . "\n" .
                "حالة الطابعة: نشطة\n" .
                str_repeat('=', $this->charPerLine) . "\n" .
                $this->escp->cut();

            return $this->sendToPrinter($testContent);

        } catch (\Exception $e) {
            Log::error('Test printer error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Print raw data directly to printer
     */
    public function printRaw($data)
    {
        try {
            $config = config('printing.thermal_printer');
            
            if ($config['type'] === 'usb') {
                return $this->printToUSB($data);
            } elseif ($config['type'] === 'network') {
                return $this->printToNetwork($data);
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Print raw error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Print to USB printer
     */
    private function printToUSB($data)
    {
        try {
            $config = config('printing.thermal_printer');
            $device = $config['usb']['device'];
            
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows - use LPT port or USB device
                $handle = fopen($device, 'w');
                if ($handle) {
                    fwrite($handle, $data);
                    fclose($handle);
                    return true;
                }
            } else {
                // Linux/Unix - use device file
                file_put_contents($device, $data);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('USB print error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Print to network printer
     */
    private function printToNetwork($data)
    {
        try {
            $config = config('printing.thermal_printer.network');
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            
            if ($socket && socket_connect($socket, $config['ip'], $config['port'])) {
                socket_write($socket, $data);
                socket_close($socket);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Network print error: ' . $e->getMessage());
            return false;
        }
    }
}
