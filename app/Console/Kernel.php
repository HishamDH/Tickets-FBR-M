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
