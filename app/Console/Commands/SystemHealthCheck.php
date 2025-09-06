<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SystemHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:health-check {--detailed : Show detailed health information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a comprehensive system health check';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDetailed = $this->option('detailed');
        
        $this->info('Starting system health check...');
        
        $healthResults = [
            'database' => $this->checkDatabase(),
            'storage' => $this->checkStorage(),
            'cache' => $this->checkCache(),
            'memory' => $this->checkMemory(),
            'disk_space' => $this->checkDiskSpace(),
            'permissions' => $this->checkPermissions(),
            'security' => $this->checkSecurity(),
            'performance' => $this->checkPerformance(),
        ];
        
        $overallHealth = $this->calculateOverallHealth($healthResults);
        
        $this->displayResults($healthResults, $overallHealth, $isDetailed);
        
        // Log health check results
        Log::info('System health check completed', [
            'overall_health' => $overallHealth,
            'results' => $healthResults,
            'timestamp' => now()
        ]);
        
        return $overallHealth >= 80 ? 0 : 1;
    }

    /**
     * Check database connectivity and performance
     */
    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $connectionTime = (microtime(true) - $start) * 1000;
            
            $start = microtime(true);
            $userCount = DB::table('users')->count();
            $queryTime = (microtime(true) - $start) * 1000;
            
            $health = 100;
            $issues = [];
            
            if ($connectionTime > 1000) {
                $health -= 20;
                $issues[] = 'Slow database connection';
            }
            
            if ($queryTime > 500) {
                $health -= 15;
                $issues[] = 'Slow query performance';
            }
            
            return [
                'status' => 'healthy',
                'health_score' => max(0, $health),
                'connection_time_ms' => round($connectionTime, 2),
                'query_time_ms' => round($queryTime, 2),
                'total_users' => $userCount,
                'issues' => $issues
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'health_score' => 0,
                'error' => $e->getMessage(),
                'issues' => ['Database connection failed']
            ];
        }
    }

    /**
     * Check storage systems
     */
    private function checkStorage(): array
    {
        $health = 100;
        $issues = [];
        
        try {
            // Check if storage directories are writable
            $storageChecks = [
                'logs' => storage_path('logs'),
                'cache' => storage_path('framework/cache'),
                'sessions' => storage_path('framework/sessions'),
                'views' => storage_path('framework/views'),
                'public' => storage_path('app/public'),
            ];
            
            foreach ($storageChecks as $name => $path) {
                if (!is_dir($path)) {
                    $health -= 15;
                    $issues[] = "Missing {$name} directory";
                } elseif (!is_writable($path)) {
                    $health -= 20;
                    $issues[] = "{$name} directory not writable";
                }
            }
            
            // Check storage usage
            $totalSpace = disk_total_space(storage_path());
            $freeSpace = disk_free_space(storage_path());
            $usagePercentage = (($totalSpace - $freeSpace) / $totalSpace) * 100;
            
            if ($usagePercentage > 90) {
                $health -= 30;
                $issues[] = 'Storage space critically low';
            } elseif ($usagePercentage > 80) {
                $health -= 15;
                $issues[] = 'Storage space running low';
            }
            
            return [
                'status' => $health > 70 ? 'healthy' : 'degraded',
                'health_score' => max(0, $health),
                'usage_percentage' => round($usagePercentage, 2),
                'free_space_gb' => round($freeSpace / 1024 / 1024 / 1024, 2),
                'total_space_gb' => round($totalSpace / 1024 / 1024 / 1024, 2),
                'issues' => $issues
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'health_score' => 0,
                'error' => $e->getMessage(),
                'issues' => ['Storage check failed']
            ];
        }
    }

    /**
     * Check cache system
     */
    private function checkCache(): array
    {
        try {
            $testKey = 'health_check_' . time();
            $testValue = 'test_value';
            
            $start = microtime(true);
            Cache::put($testKey, $testValue, 60);
            $writeTime = (microtime(true) - $start) * 1000;
            
            $start = microtime(true);
            $retrieved = Cache::get($testKey);
            $readTime = (microtime(true) - $start) * 1000;
            
            Cache::forget($testKey);
            
            $health = 100;
            $issues = [];
            
            if ($retrieved !== $testValue) {
                $health = 0;
                $issues[] = 'Cache read/write test failed';
            }
            
            if ($writeTime > 100) {
                $health -= 20;
                $issues[] = 'Slow cache write performance';
            }
            
            if ($readTime > 50) {
                $health -= 15;
                $issues[] = 'Slow cache read performance';
            }
            
            return [
                'status' => $health > 70 ? 'healthy' : 'degraded',
                'health_score' => max(0, $health),
                'write_time_ms' => round($writeTime, 2),
                'read_time_ms' => round($readTime, 2),
                'driver' => config('cache.default'),
                'issues' => $issues
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'health_score' => 0,
                'error' => $e->getMessage(),
                'issues' => ['Cache system failed']
            ];
        }
    }

    /**
     * Check memory usage
     */
    private function checkMemory(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $memoryPeak = memory_get_peak_usage(true);
        
        $usagePercentage = ($memoryUsage / $memoryLimit) * 100;
        $peakPercentage = ($memoryPeak / $memoryLimit) * 100;
        
        $health = 100;
        $issues = [];
        
        if ($usagePercentage > 90) {
            $health -= 40;
            $issues[] = 'Memory usage critically high';
        } elseif ($usagePercentage > 80) {
            $health -= 25;
            $issues[] = 'Memory usage high';
        }
        
        if ($peakPercentage > 95) {
            $health -= 20;
            $issues[] = 'Memory peak usage too high';
        }
        
        return [
            'status' => $health > 70 ? 'healthy' : 'degraded',
            'health_score' => max(0, $health),
            'current_usage_mb' => round($memoryUsage / 1024 / 1024, 2),
            'peak_usage_mb' => round($memoryPeak / 1024 / 1024, 2),
            'limit_mb' => round($memoryLimit / 1024 / 1024, 2),
            'usage_percentage' => round($usagePercentage, 2),
            'peak_percentage' => round($peakPercentage, 2),
            'issues' => $issues
        ];
    }

    /**
     * Check disk space
     */
    private function checkDiskSpace(): array
    {
        $rootPath = '/';
        if (PHP_OS_FAMILY === 'Windows') {
            $rootPath = 'C:';
        }
        
        $totalSpace = disk_total_space($rootPath);
        $freeSpace = disk_free_space($rootPath);
        $usagePercentage = (($totalSpace - $freeSpace) / $totalSpace) * 100;
        
        $health = 100;
        $issues = [];
        
        if ($usagePercentage > 95) {
            $health -= 50;
            $issues[] = 'Disk space critically low';
        } elseif ($usagePercentage > 90) {
            $health -= 30;
            $issues[] = 'Disk space running low';
        } elseif ($usagePercentage > 85) {
            $health -= 15;
            $issues[] = 'Disk space getting low';
        }
        
        return [
            'status' => $health > 70 ? 'healthy' : 'degraded',
            'health_score' => max(0, $health),
            'usage_percentage' => round($usagePercentage, 2),
            'free_space_gb' => round($freeSpace / 1024 / 1024 / 1024, 2),
            'total_space_gb' => round($totalSpace / 1024 / 1024 / 1024, 2),
            'issues' => $issues
        ];
    }

    /**
     * Check file permissions
     */
    private function checkPermissions(): array
    {
        $health = 100;
        $issues = [];
        
        $criticalPaths = [
            storage_path(),
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            base_path('bootstrap/cache'),
        ];
        
        foreach ($criticalPaths as $path) {
            if (!is_writable($path)) {
                $health -= 20;
                $issues[] = "Directory not writable: " . basename($path);
            }
        }
        
        return [
            'status' => $health > 70 ? 'healthy' : 'degraded',
            'health_score' => max(0, $health),
            'checked_paths' => count($criticalPaths),
            'issues' => $issues
        ];
    }

    /**
     * Check security status
     */
    private function checkSecurity(): array
    {
        $health = 100;
        $issues = [];
        
        // Check if debug mode is off in production
        if (config('app.debug') && config('app.env') === 'production') {
            $health -= 30;
            $issues[] = 'Debug mode enabled in production';
        }
        
        // Check if APP_KEY is set
        if (empty(config('app.key'))) {
            $health -= 40;
            $issues[] = 'Application key not set';
        }
        
        // Check for HTTPS in production
        if (config('app.env') === 'production' && !request()->secure()) {
            $health -= 25;
            $issues[] = 'HTTPS not enabled in production';
        }
        
        // Check password policy compliance
        $weakPasswords = DB::table('users')
            ->where('password_changed_at', '<', now()->subDays(90))
            ->orWhereNull('password_changed_at')
            ->count();
            
        if ($weakPasswords > 0) {
            $health -= 15;
            $issues[] = "{$weakPasswords} users with expired passwords";
        }
        
        return [
            'status' => $health > 70 ? 'healthy' : 'degraded',
            'health_score' => max(0, $health),
            'expired_passwords' => $weakPasswords,
            'https_enabled' => request()->secure(),
            'debug_mode' => config('app.debug'),
            'issues' => $issues
        ];
    }

    /**
     * Check system performance
     */
    private function checkPerformance(): array
    {
        $health = 100;
        $issues = [];
        
        // Check failed jobs
        $failedJobs = DB::table('failed_jobs')->count();
        if ($failedJobs > 100) {
            $health -= 25;
            $issues[] = "High number of failed jobs: {$failedJobs}";
        } elseif ($failedJobs > 50) {
            $health -= 15;
            $issues[] = "Moderate failed jobs: {$failedJobs}";
        }
        
        // Check log file sizes
        $logPath = storage_path('logs');
        $logSize = 0;
        
        if (is_dir($logPath)) {
            foreach (glob($logPath . '/*.log') as $file) {
                $logSize += filesize($file);
            }
        }
        
        $logSizeMB = $logSize / 1024 / 1024;
        if ($logSizeMB > 1000) {
            $health -= 20;
            $issues[] = "Large log files: {$logSizeMB}MB";
        }
        
        return [
            'status' => $health > 70 ? 'healthy' : 'degraded',
            'health_score' => max(0, $health),
            'failed_jobs' => $failedJobs,
            'log_size_mb' => round($logSizeMB, 2),
            'issues' => $issues
        ];
    }

    /**
     * Calculate overall health score
     */
    private function calculateOverallHealth(array $results): int
    {
        $totalScore = 0;
        $count = 0;
        
        foreach ($results as $result) {
            if (isset($result['health_score'])) {
                $totalScore += $result['health_score'];
                $count++;
            }
        }
        
        return $count > 0 ? round($totalScore / $count) : 0;
    }

    /**
     * Display health check results
     */
    private function displayResults(array $results, int $overallHealth, bool $detailed): void
    {
        $this->info("\n=== System Health Check Results ===");
        $this->info("Overall Health Score: {$overallHealth}%");
        
        if ($overallHealth >= 90) {
            $this->info("Status: Excellent ✅");
        } elseif ($overallHealth >= 80) {
            $this->warn("Status: Good ⚠️");
        } elseif ($overallHealth >= 60) {
            $this->warn("Status: Degraded ⚠️");
        } else {
            $this->error("Status: Critical ❌");
        }
        
        $this->info("\n=== Component Health ===");
        
        foreach ($results as $component => $result) {
            $status = $result['status'] ?? 'unknown';
            $score = $result['health_score'] ?? 0;
            $emoji = $this->getStatusEmoji($status, $score);
            
            $this->info("{$emoji} " . ucfirst($component) . ": {$score}% ({$status})");
            
            if (!empty($result['issues'])) {
                foreach ($result['issues'] as $issue) {
                    $this->warn("  - {$issue}");
                }
            }
            
            if ($detailed && isset($result['health_score'])) {
                unset($result['status'], $result['health_score'], $result['issues']);
                foreach ($result as $key => $value) {
                    if (is_array($value)) {
                        $value = json_encode($value);
                    }
                    $this->line("    {$key}: {$value}");
                }
            }
        }
    }

    /**
     * Get status emoji
     */
    private function getStatusEmoji(string $status, int $score): string
    {
        if ($status === 'unhealthy' || $score < 60) {
            return '❌';
        } elseif ($status === 'degraded' || $score < 80) {
            return '⚠️';
        } else {
            return '✅';
        }
    }

    /**
     * Parse memory limit from PHP ini
     */
    private function parseMemoryLimit(string $limit): int
    {
        $limit = trim($limit);
        $multiplier = 1;
        
        if (preg_match('/(\d+)\s*([kmg])/i', $limit, $matches)) {
            $value = intval($matches[1]);
            $unit = strtolower($matches[2]);
            
            switch ($unit) {
                case 'g':
                    $multiplier = 1024 * 1024 * 1024;
                    break;
                case 'm':
                    $multiplier = 1024 * 1024;
                    break;
                case 'k':
                    $multiplier = 1024;
                    break;
            }
            
            return $value * $multiplier;
        }
        
        return intval($limit);
    }
}
