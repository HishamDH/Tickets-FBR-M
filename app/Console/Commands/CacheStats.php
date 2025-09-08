<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SiteStatsService;
use Illuminate\Support\Facades\Cache;

class CacheStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:cache {--clear : Clear the stats cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache site statistics for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clear')) {
            Cache::forget('site_stats');
            $this->info('Statistics cache cleared.');
            return 0;
        }

        $this->info('Caching site statistics...');
        
        $statsService = new SiteStatsService();
        $stats = $statsService->getSiteStats();
        
        $this->table(['Metric', 'Value'], [
            ['Total Services', number_format($stats['total_services'])],
            ['Total Bookings', number_format($stats['total_bookings'])],
            ['Total Customers', number_format($stats['total_customers'])],
            ['Total Merchants', number_format($stats['total_merchants'])],
            ['Completion Rate', $stats['completion_rate'] . '%'],
            ['Today\'s Bookings', number_format($stats['active_bookings_today'])],
            ['Total Revenue', number_format($stats['total_revenue']) . ' SAR'],
        ]);

        $this->info('✅ Statistics cached successfully!');
        
        return 0;
    }
}