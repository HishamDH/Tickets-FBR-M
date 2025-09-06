<?php

namespace App\Console\Commands;

use App\Services\PartnerReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partner:process-scheduled-reports {--dry-run : Run without actually sending reports}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and send scheduled partner reports';

    /**
     * Execute the console command.
     */
    public function handle(PartnerReportService $reportService): int
    {
        $this->info('🔄 Starting scheduled reports processing...');
        
        $startTime = microtime(true);
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn('📝 Running in DRY-RUN mode - no reports will be sent');
        }

        try {
            if ($isDryRun) {
                $processed = $this->performDryRun();
            } else {
                $processed = $reportService->runScheduledReports();
            }
            
            $executionTime = round(microtime(true) - $startTime, 2);
            
            $this->info("✅ Successfully processed {$processed} scheduled reports");
            $this->info("⏱️  Total execution time: {$executionTime} seconds");
            
            if ($processed > 0) {
                Log::info('Scheduled reports processed successfully', [
                    'reports_processed' => $processed,
                    'execution_time' => $executionTime,
                    'dry_run' => $isDryRun,
                ]);
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error processing scheduled reports: ' . $e->getMessage());
            
            Log::error('Failed to process scheduled reports', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return Command::FAILURE;
        }
    }

    /**
     * Perform dry run to show what would be processed
     */
    private function performDryRun(): int
    {
        $reports = \App\Models\ScheduledReport::with('partner')
            ->where('is_active', true)
            ->where('next_run_at', '<=', now())
            ->get();

        if ($reports->isEmpty()) {
            $this->info('📭 No scheduled reports due for processing');
            return 0;
        }

        $this->info("📊 Found {$reports->count()} scheduled reports due for processing:");
        $this->newLine();

        $headers = ['ID', 'Partner', 'Type', 'Frequency', 'Next Run', 'Run Count'];
        $rows = [];

        foreach ($reports as $report) {
            $rows[] = [
                $report->id,
                $report->partner->name,
                $report->report_type_name,
                $report->frequency_name,
                $report->next_run_at->format('Y-m-d H:i:s'),
                $report->run_count,
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();
        $this->info('💡 Run without --dry-run to actually process these reports');

        return $reports->count();
    }
}
