<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Database Backup Tasks
        $schedule->command('db:backup --type=full')
                 ->dailyAt('01:00')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->emailOutputOnFailure('admin@example.com');

        $schedule->command('db:backup --type=structure')
                 ->weekly()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Data Cleanup Tasks
        $schedule->command('app:data-cleanup')
                 ->dailyAt('03:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Report Generation Tasks
        $schedule->command('reports:generate --type=daily')
                 ->dailyAt('04:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        $schedule->command('reports:generate --type=weekly')
                 ->weekly()
                 ->mondays()
                 ->at('05:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        $schedule->command('reports:generate --type=monthly')
                 ->monthlyOn(1, '06:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Password Security Tasks
        $schedule->call(function () {
            // Reset locked accounts after lockout duration
            \App\Models\User::where('locked_until', '<', now())
                ->whereNotNull('locked_until')
                ->update(['locked_until' => null, 'failed_login_attempts' => 0]);
        })->hourly();

        $schedule->call(function () {
            // Notify users about password expiry (7 days before)
            $users = \App\Models\User::whereDate('password_changed_at', '<=', now()->subDays(83))
                ->where('force_password_reset', false)
                ->get();
            
            foreach ($users as $user) {
                // Send password expiry notification
                // You can implement notification logic here
            }
        })->daily();

        // System Maintenance Tasks
        $schedule->call(function () {
            // Clear expired sessions
            \Illuminate\Support\Facades\DB::table('sessions')
                ->where('last_activity', '<', now()->subDays(30)->timestamp)
                ->delete();
        })->daily();

        $schedule->call(function () {
            // Clear old failed jobs
            \Illuminate\Support\Facades\DB::table('failed_jobs')
                ->where('failed_at', '<', now()->subDays(7))
                ->delete();
        })->daily();

        // معالجة السحوبات التلقائية يومياً في الساعة 2:00 صباحاً
        $schedule->command('partners:process-auto-withdrawals')
                 ->dailyAt('02:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // تحديث حالة الدعوات المنتهية الصلاحية كل ساعة
        $schedule->call(function () {
            \App\Models\PartnerInvitation::where('status', 'pending')
                ->where('expires_at', '<', now())
                ->update(['status' => 'expired']);
        })->hourly();

        // Monitor system health
        $schedule->command('system:health-check')
                 ->hourly()
                 ->withoutOverlapping()
                 ->runInBackground();

        $schedule->call(function () {
            // Check disk space, memory usage, etc.
            $diskUsage = disk_free_space('/') / disk_total_space('/');
            if ($diskUsage < 0.1) { // Less than 10% free space
                \Illuminate\Support\Facades\Log::warning('Low disk space warning', [
                    'free_space_percentage' => $diskUsage * 100
                ]);
            }
        })->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
