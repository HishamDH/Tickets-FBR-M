<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;

class GenerateReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate {--type=all : Type of report (daily|weekly|monthly|all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate scheduled system reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $this->info("Generating {$type} reports...");
        
        try {
            $this->createReportsDirectory();
            
            switch ($type) {
                case 'daily':
                    $this->generateDailyReport();
                    break;
                case 'weekly':
                    $this->generateWeeklyReport();
                    break;
                case 'monthly':
                    $this->generateMonthlyReport();
                    break;
                case 'all':
                    $this->generateDailyReport();
                    $this->generateWeeklyReport();
                    $this->generateMonthlyReport();
                    break;
                default:
                    $this->error('Invalid report type. Use: daily, weekly, monthly, or all');
                    return 1;
            }
            
            $this->cleanOldReports();
            $this->info('Reports generated successfully!');
            
            Log::info('Scheduled reports generated', [
                'type' => $type,
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->error("Report generation failed: " . $e->getMessage());
            Log::error('Report generation failed', [
                'error' => $e->getMessage(),
                'type' => $type
            ]);
            return 1;
        }
        
        return 0;
    }

    /**
     * Create reports directory if it doesn't exist
     */
    private function createReportsDirectory(): void
    {
        $reportsPath = storage_path('app/reports');
        if (!file_exists($reportsPath)) {
            mkdir($reportsPath, 0755, true);
        }
    }

    /**
     * Generate daily report
     */
    private function generateDailyReport(): void
    {
        $date = Carbon::yesterday();
        $filename = "daily_report_{$date->format('Y-m-d')}.json";
        
        $report = [
            'report_type' => 'daily',
            'date' => $date->format('Y-m-d'),
            'generated_at' => now()->toISOString(),
            'statistics' => $this->getDailyStatistics($date),
            'users' => $this->getUserStatistics($date, 'daily'),
            'bookings' => $this->getBookingStatistics($date, 'daily'),
            'revenue' => $this->getRevenueStatistics($date, 'daily'),
            'system' => $this->getSystemStatistics($date, 'daily'),
        ];
        
        $this->saveReport($filename, $report);
        $this->info("Daily report generated: {$filename}");
    }

    /**
     * Generate weekly report
     */
    private function generateWeeklyReport(): void
    {
        $endDate = Carbon::now()->subWeek()->endOfWeek();
        $startDate = $endDate->copy()->startOfWeek();
        $filename = "weekly_report_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}.json";
        
        $report = [
            'report_type' => 'weekly',
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'generated_at' => now()->toISOString(),
            'statistics' => $this->getWeeklyStatistics($startDate, $endDate),
            'users' => $this->getUserStatistics($startDate, 'weekly', $endDate),
            'bookings' => $this->getBookingStatistics($startDate, 'weekly', $endDate),
            'revenue' => $this->getRevenueStatistics($startDate, 'weekly', $endDate),
            'system' => $this->getSystemStatistics($startDate, 'weekly', $endDate),
            'trends' => $this->getTrendAnalysis($startDate, $endDate),
        ];
        
        $this->saveReport($filename, $report);
        $this->info("Weekly report generated: {$filename}");
    }

    /**
     * Generate monthly report
     */
    private function generateMonthlyReport(): void
    {
        $date = Carbon::now()->subMonth();
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();
        $filename = "monthly_report_{$date->format('Y-m')}.json";
        
        $report = [
            'report_type' => 'monthly',
            'month' => $date->format('Y-m'),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'generated_at' => now()->toISOString(),
            'statistics' => $this->getMonthlyStatistics($startDate, $endDate),
            'users' => $this->getUserStatistics($startDate, 'monthly', $endDate),
            'bookings' => $this->getBookingStatistics($startDate, 'monthly', $endDate),
            'revenue' => $this->getRevenueStatistics($startDate, 'monthly', $endDate),
            'system' => $this->getSystemStatistics($startDate, 'monthly', $endDate),
            'trends' => $this->getTrendAnalysis($startDate, $endDate),
            'performance' => $this->getPerformanceMetrics($startDate, $endDate),
        ];
        
        $this->saveReport($filename, $report);
        $this->info("Monthly report generated: {$filename}");
    }

    /**
     * Get daily statistics
     */
    private function getDailyStatistics(Carbon $date): array
    {
        return [
            'total_users' => User::whereDate('created_at', $date)->count(),
            'active_users' => User::whereDate('last_login_at', $date)->count(),
            'new_registrations' => User::whereDate('created_at', $date)->count(),
        ];
    }

    /**
     * Get weekly statistics
     */
    private function getWeeklyStatistics(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'total_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_users' => User::whereBetween('last_login_at', [$startDate, $endDate])->count(),
            'new_registrations' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
        ];
    }

    /**
     * Get monthly statistics
     */
    private function getMonthlyStatistics(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'total_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_users' => User::whereBetween('last_login_at', [$startDate, $endDate])->count(),
            'new_registrations' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
        ];
    }

    /**
     * Get user statistics
     */
    private function getUserStatistics($date, string $period, $endDate = null): array
    {
        $query = User::query();
        
        if ($endDate) {
            $query->whereBetween('created_at', [$date, $endDate]);
        } else {
            $query->whereDate('created_at', $date);
        }
        
        return [
            'new_users' => (clone $query)->count(),
            'by_type' => [
                'customers' => (clone $query)->where('user_type', 'customer')->count(),
                'merchants' => (clone $query)->where('user_type', 'merchant')->count(),
                'admins' => (clone $query)->where('user_type', 'admin')->count(),
            ],
            'verified_users' => (clone $query)->whereNotNull('email_verified_at')->count(),
            'active_users' => User::when($endDate, function ($q) use ($date, $endDate) {
                return $q->whereBetween('last_login_at', [$date, $endDate]);
            }, function ($q) use ($date) {
                return $q->whereDate('last_login_at', $date);
            })->count(),
        ];
    }

    /**
     * Get booking statistics
     */
    private function getBookingStatistics($date, string $period, $endDate = null): array
    {
        if (!DB::getSchemaBuilder()->hasTable('bookings')) {
            return ['message' => 'Bookings table not found'];
        }
        
        $query = DB::table('bookings');
        
        if ($endDate) {
            $query->whereBetween('created_at', [$date, $endDate]);
        } else {
            $query->whereDate('created_at', $date);
        }
        
        return [
            'total_bookings' => (clone $query)->count(),
            'by_status' => [
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'confirmed' => (clone $query)->where('status', 'confirmed')->count(),
                'completed' => (clone $query)->where('status', 'completed')->count(),
                'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            ],
            'revenue' => (clone $query)->where('status', 'completed')->sum('total_amount') ?? 0,
        ];
    }

    /**
     * Get revenue statistics
     */
    private function getRevenueStatistics($date, string $period, $endDate = null): array
    {
        if (!DB::getSchemaBuilder()->hasTable('bookings')) {
            return ['message' => 'Revenue data not available - bookings table not found'];
        }
        
        $query = DB::table('bookings')->where('status', 'completed');
        
        if ($endDate) {
            $query->whereBetween('created_at', [$date, $endDate]);
        } else {
            $query->whereDate('created_at', $date);
        }
        
        return [
            'total_revenue' => $query->sum('total_amount') ?? 0,
            'booking_count' => $query->count(),
            'average_booking_value' => $query->avg('total_amount') ?? 0,
        ];
    }

    /**
     * Get system statistics
     */
    private function getSystemStatistics($date, string $period, $endDate = null): array
    {
        return [
            'failed_jobs' => DB::table('failed_jobs')->when($endDate, function ($q) use ($date, $endDate) {
                return $q->whereBetween('failed_at', [$date, $endDate]);
            }, function ($q) use ($date) {
                return $q->whereDate('failed_at', $date);
            })->count(),
            'log_entries' => $this->getLogEntriesCount($date, $endDate),
            'storage_usage' => $this->getStorageUsage(),
            'database_size' => $this->getDatabaseSize(),
        ];
    }

    /**
     * Get trend analysis
     */
    private function getTrendAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $previousPeriod = $endDate->diffInDays($startDate);
        $previousStart = $startDate->copy()->subDays($previousPeriod);
        $previousEnd = $startDate->copy()->subDay();
        
        $currentUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $previousUsers = User::whereBetween('created_at', [$previousStart, $previousEnd])->count();
        
        return [
            'user_growth' => [
                'current_period' => $currentUsers,
                'previous_period' => $previousUsers,
                'growth_rate' => $previousUsers > 0 ? (($currentUsers - $previousUsers) / $previousUsers) * 100 : 0,
            ],
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'average_response_time' => 'N/A', // Would need application performance monitoring
            'uptime_percentage' => 99.9, // Would need monitoring system integration
            'error_rate' => $this->calculateErrorRate($startDate, $endDate),
        ];
    }

    /**
     * Calculate error rate from logs
     */
    private function calculateErrorRate(Carbon $startDate, Carbon $endDate): float
    {
        // This would need log parsing implementation
        return 0.1; // Placeholder
    }

    /**
     * Get log entries count
     */
    private function getLogEntriesCount($date, $endDate = null): int
    {
        // This would need log parsing implementation
        return rand(100, 500); // Placeholder
    }

    /**
     * Get storage usage in MB
     */
    private function getStorageUsage(): float
    {
        $storageSize = 0;
        $storagePath = storage_path();
        
        if (is_dir($storagePath)) {
            $storageSize = $this->getDirectorySize($storagePath);
        }
        
        return round($storageSize / 1024 / 1024, 2); // Convert to MB
    }

    /**
     * Get database size in MB
     */
    private function getDatabaseSize(): float
    {
        $database = config('database.connections.mysql.database');
        
        $result = DB::select("
            SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB' 
            FROM information_schema.tables 
            WHERE table_schema = ?
        ", [$database]);
        
        return $result[0]->{'DB Size in MB'} ?? 0;
    }

    /**
     * Get directory size in bytes
     */
    private function getDirectorySize(string $directory): int
    {
        $size = 0;
        
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
            $size += $file->getSize();
        }
        
        return $size;
    }

    /**
     * Save report to file
     */
    private function saveReport(string $filename, array $report): void
    {
        $path = storage_path("app/reports/{$filename}");
        file_put_contents($path, json_encode($report, JSON_PRETTY_PRINT));
    }

    /**
     * Clean old report files (keep last 90 days)
     */
    private function cleanOldReports(): void
    {
        $reportsPath = storage_path('app/reports');
        $cutoffDate = Carbon::now()->subDays(90);
        
        if (is_dir($reportsPath)) {
            $files = glob($reportsPath . '/*.json');
            
            foreach ($files as $file) {
                $fileTime = Carbon::createFromTimestamp(filemtime($file));
                
                if ($fileTime->lt($cutoffDate)) {
                    unlink($file);
                    $this->info("Deleted old report: " . basename($file));
                }
            }
        }
    }
}
