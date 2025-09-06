<?php

namespace App\Console\Commands;

use App\Services\PartnerPerformanceService;
use App\Models\Partner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CalculatePartnerMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partner:calculate-metrics 
                            {--partner= : Calculate for specific partner ID}
                            {--month= : Calculate for specific month (YYYY-MM)}
                            {--force : Force recalculation even if metrics exist}
                            {--dry-run : Show what would be calculated without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and store partner performance metrics';

    /**
     * Execute the console command.
     */
    public function handle(PartnerPerformanceService $performanceService): int
    {
        $this->info('🔄 Starting partner metrics calculation...');
        
        $startTime = microtime(true);
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');
        $specificPartner = $this->option('partner');
        $specificMonth = $this->option('month');
        
        if ($isDryRun) {
            $this->warn('📝 Running in DRY-RUN mode - no metrics will be saved');
        }

        try {
            // تحديد التاريخ
            $date = $specificMonth ? Carbon::createFromFormat('Y-m', $specificMonth) : now();
            
            // تحديد الشركاء
            $partners = $specificPartner 
                ? Partner::where('id', $specificPartner)->get()
                : Partner::where('status', 'active')->get();

            if ($partners->isEmpty()) {
                $this->warn('⚠️  No partners found to process');
                return Command::SUCCESS;
            }

            $this->info("📊 Processing {$partners->count()} partners for {$date->format('Y-m')}");
            $this->newLine();

            $processed = 0;
            $skipped = 0;
            $errors = 0;

            $progressBar = $this->output->createProgressBar($partners->count());
            $progressBar->start();

            foreach ($partners as $partner) {
                try {
                    // التحقق من وجود المقاييس مسبقاً
                    $existingMetrics = $partner->metrics()
                        ->where('year', $date->year)
                        ->where('month', $date->month)
                        ->first();

                    if ($existingMetrics && !$force) {
                        $skipped++;
                        $progressBar->advance();
                        continue;
                    }

                    if ($isDryRun) {
                        // في وضع المحاكاة، نحسب فقط دون حفظ
                        $metrics = $performanceService->calculatePerformanceMetrics(
                            $partner,
                            $date->copy()->startOfMonth(),
                            $date->copy()->endOfMonth()
                        );
                        
                        $this->line("\n🔍 Partner: {$partner->name}");
                        $this->line("   Commission: {$metrics['commission_earned']} SAR");
                        $this->line("   New Merchants: {$metrics['new_merchants_count']}");
                        $this->line("   Performance Score: {$metrics['performance_score']}");
                    } else {
                        // حساب وحفظ المقاييس
                        $performanceService->calculateAndStoreMetrics($partner, $date);
                    }

                    $processed++;
                } catch (\Exception $e) {
                    $errors++;
                    Log::error('Failed to calculate metrics for partner', [
                        'partner_id' => $partner->id,
                        'partner_name' => $partner->name,
                        'error' => $e->getMessage(),
                    ]);
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // النتائج
            $executionTime = round(microtime(true) - $startTime, 2);
            
            $this->info("✅ Metrics calculation completed!");
            $this->table([
                'Metric', 'Count'
            ], [
                ['Processed', $processed],
                ['Skipped', $skipped],
                ['Errors', $errors],
                ['Total Partners', $partners->count()],
                ['Execution Time', "{$executionTime}s"],
            ]);

            if ($errors > 0) {
                $this->warn("⚠️  {$errors} errors occurred. Check logs for details.");
            }

            if (!$isDryRun && $processed > 0) {
                Log::info('Partner metrics calculated successfully', [
                    'partners_processed' => $processed,
                    'partners_skipped' => $skipped,
                    'errors' => $errors,
                    'execution_time' => $executionTime,
                    'month' => $date->format('Y-m'),
                ]);
            }

            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error calculating partner metrics: ' . $e->getMessage());
            
            Log::error('Failed to calculate partner metrics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return Command::FAILURE;
        }
    }
}
