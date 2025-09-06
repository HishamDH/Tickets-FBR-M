<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\User;

class DataCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:data-cleanup {--dry-run : Show what would be cleaned without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old and unnecessary data from the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('Starting data cleanup...');
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No data will actually be deleted');
        }
        
        $cleanupStats = [
            'expired_sessions' => $this->cleanExpiredSessions($isDryRun),
            'old_logs' => $this->cleanOldLogs($isDryRun),
            'temp_files' => $this->cleanTempFiles($isDryRun),
            'failed_jobs' => $this->cleanFailedJobs($isDryRun),
            'password_resets' => $this->cleanPasswordResets($isDryRun),
            'expired_bookings' => $this->cleanExpiredBookings($isDryRun),
            'orphaned_files' => $this->cleanOrphanedFiles($isDryRun),
            'inactive_users' => $this->cleanInactiveUsers($isDryRun),
        ];
        
        $totalCleaned = array_sum($cleanupStats);
        
        $this->info("\nCleanup Summary:");
        $this->table(
            ['Category', 'Items Cleaned'],
            collect($cleanupStats)->map(function ($count, $category) {
                return [ucfirst(str_replace('_', ' ', $category)), $count];
            })->toArray()
        );
        
        $this->info("Total items cleaned: {$totalCleaned}");
        
        // Log cleanup results
        Log::info('Data cleanup completed', [
            'stats' => $cleanupStats,
            'total_cleaned' => $totalCleaned,
            'dry_run' => $isDryRun
        ]);
        
        return 0;
    }

    /**
     * Clean expired sessions
     */
    private function cleanExpiredSessions(bool $isDryRun): int
    {
        if (!DB::getSchemaBuilder()->hasTable('sessions')) {
            $this->info("Sessions table not found - skipping");
            return 0;
        }
        
        $expiredSessions = DB::table('sessions')
            ->where('last_activity', '<', now()->subDays(30)->timestamp)
            ->count();
            
        if (!$isDryRun && $expiredSessions > 0) {
            DB::table('sessions')
                ->where('last_activity', '<', now()->subDays(30)->timestamp)
                ->delete();
        }
        
        $this->info("Expired sessions: {$expiredSessions}");
        return $expiredSessions;
    }

    /**
     * Clean old log files
     */
    private function cleanOldLogs(bool $isDryRun): int
    {
        $logPath = storage_path('logs');
        $cutoffDate = Carbon::now()->subDays(60);
        $cleanedCount = 0;
        
        if (is_dir($logPath)) {
            $logFiles = glob($logPath . '/*.log');
            
            foreach ($logFiles as $file) {
                $fileTime = Carbon::createFromTimestamp(filemtime($file));
                
                if ($fileTime->lt($cutoffDate)) {
                    if (!$isDryRun) {
                        unlink($file);
                    }
                    $cleanedCount++;
                    $this->info("Old log file: " . basename($file));
                }
            }
        }
        
        return $cleanedCount;
    }

    /**
     * Clean temporary files
     */
    private function cleanTempFiles(bool $isDryRun): int
    {
        $tempPath = storage_path('app/temp');
        $cleanedCount = 0;
        
        if (is_dir($tempPath)) {
            $tempFiles = glob($tempPath . '/*');
            $cutoffDate = Carbon::now()->subHours(24);
            
            foreach ($tempFiles as $file) {
                $fileTime = Carbon::createFromTimestamp(filemtime($file));
                
                if ($fileTime->lt($cutoffDate)) {
                    if (!$isDryRun) {
                        if (is_dir($file)) {
                            $this->deleteDirectory($file);
                        } else {
                            unlink($file);
                        }
                    }
                    $cleanedCount++;
                    $this->info("Temp file/folder: " . basename($file));
                }
            }
        }
        
        return $cleanedCount;
    }

    /**
     * Clean failed jobs older than 7 days
     */
    private function cleanFailedJobs(bool $isDryRun): int
    {
        if (!DB::getSchemaBuilder()->hasTable('failed_jobs')) {
            $this->info("Failed jobs table not found - skipping");
            return 0;
        }
        
        $failedJobs = DB::table('failed_jobs')
            ->where('failed_at', '<', now()->subDays(7))
            ->count();
            
        if (!$isDryRun && $failedJobs > 0) {
            DB::table('failed_jobs')
                ->where('failed_at', '<', now()->subDays(7))
                ->delete();
        }
        
        $this->info("Failed jobs: {$failedJobs}");
        return $failedJobs;
    }

    /**
     * Clean expired password reset tokens
     */
    private function cleanPasswordResets(bool $isDryRun): int
    {
        if (!DB::getSchemaBuilder()->hasTable('password_reset_tokens')) {
            $this->info("Password reset tokens table not found - skipping");
            return 0;
        }
        
        $expiredResets = DB::table('password_reset_tokens')
            ->where('created_at', '<', now()->subHours(1))
            ->count();
            
        if (!$isDryRun && $expiredResets > 0) {
            DB::table('password_reset_tokens')
                ->where('created_at', '<', now()->subHours(1))
                ->delete();
        }
        
        $this->info("Expired password resets: {$expiredResets}");
        return $expiredResets;
    }

    /**
     * Clean expired unconfirmed bookings
     */
    private function cleanExpiredBookings(bool $isDryRun): int
    {
        $expiredBookings = 0;
        
        if (DB::getSchemaBuilder()->hasTable('bookings')) {
            $expiredBookings = DB::table('bookings')
                ->where('status', 'pending')
                ->where('created_at', '<', now()->subHours(2))
                ->count();
                
            if (!$isDryRun && $expiredBookings > 0) {
                DB::table('bookings')
                    ->where('status', 'pending')
                    ->where('created_at', '<', now()->subHours(2))
                    ->update(['status' => 'expired']);
            }
        }
        
        $this->info("Expired bookings: {$expiredBookings}");
        return $expiredBookings;
    }

    /**
     * Clean orphaned files from storage
     */
    private function cleanOrphanedFiles(bool $isDryRun): int
    {
        $cleanedCount = 0;
        $publicPath = storage_path('app/public');
        
        if (is_dir($publicPath)) {
            $files = Storage::disk('public')->allFiles();
            
            foreach ($files as $file) {
                // Check if file is referenced in database
                $isReferenced = $this->isFileReferenced($file);
                
                if (!$isReferenced) {
                    if (!$isDryRun) {
                        Storage::disk('public')->delete($file);
                    }
                    $cleanedCount++;
                    $this->info("Orphaned file: {$file}");
                }
            }
        }
        
        return $cleanedCount;
    }

    /**
     * Clean inactive users (no login for 2 years and no activity)
     */
    private function cleanInactiveUsers(bool $isDryRun): int
    {
        $inactiveUsers = User::where('last_login_at', '<', now()->subYears(2))
            ->orWhereNull('last_login_at')
            ->where('created_at', '<', now()->subYears(2))
            ->whereNotIn('user_type', ['admin']) // Don't delete admin users
            ->count();
            
        if (!$isDryRun && $inactiveUsers > 0) {
            // Instead of deleting, we'll just mark them as inactive
            User::where('last_login_at', '<', now()->subYears(2))
                ->orWhereNull('last_login_at')
                ->where('created_at', '<', now()->subYears(2))
                ->whereNotIn('user_type', ['admin'])
                ->update(['status' => 'inactive']);
        }
        
        $this->info("Inactive users marked: {$inactiveUsers}");
        return $inactiveUsers;
    }

    /**
     * Check if a file is referenced in the database
     */
    private function isFileReferenced(string $file): bool
    {
        // Check in users table for avatars
        if (DB::table('users')->where('avatar', 'like', "%{$file}%")->exists()) {
            return true;
        }
        
        // Check in services table for images (if exists)
        if (DB::getSchemaBuilder()->hasTable('services')) {
            if (DB::table('services')->where('image', 'like', "%{$file}%")->exists()) {
                return true;
            }
        }
        
        // Add more checks for other tables as needed
        
        return false;
    }

    /**
     * Recursively delete directory
     */
    private function deleteDirectory(string $dir): bool
    {
        if (!file_exists($dir)) {
            return true;
        }
        
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        
        return rmdir($dir);
    }
}
