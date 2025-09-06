<?php

namespace App\Console\Commands;

use App\Services\PartnerPerformanceService;
use App\Models\Partner;
use App\Models\PartnerGoal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdatePartnerGoals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partner:update-goals 
                            {--partner= : Update goals for specific partner ID}
                            {--dry-run : Show what would be updated without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update partner goals progress and check for completions';

    /**
     * Execute the console command.
     */
    public function handle(PartnerPerformanceService $performanceService): int
    {
        $this->info('🎯 Starting partner goals update...');
        
        $startTime = microtime(true);
        $isDryRun = $this->option('dry-run');
        $specificPartner = $this->option('partner');
        
        if ($isDryRun) {
            $this->warn('📝 Running in DRY-RUN mode - no goals will be updated');
        }

        try {
            // تحديد الشركاء
            $partners = $specificPartner 
                ? Partner::where('id', $specificPartner)->with('goals')->get()
                : Partner::whereHas('goals', function($q) {
                    $q->where('status', 'active');
                })->with('goals')->get();

            if ($partners->isEmpty()) {
                $this->warn('⚠️  No partners with active goals found');
                return Command::SUCCESS;
            }

            $this->info("🔄 Processing {$partners->count()} partners with active goals");
            $this->newLine();

            $totalGoalsUpdated = 0;
            $totalGoalsCompleted = 0;
            $totalAchievements = 0;
            $totalRewardsAwarded = 0;

            foreach ($partners as $partner) {
                $activeGoals = $partner->goals()->where('status', 'active')->get();
                
                if ($activeGoals->isEmpty()) {
                    continue;
                }

                $this->line("📊 Partner: {$partner->name} ({$activeGoals->count()} active goals)");

                if ($isDryRun) {
                    // وضع المحاكاة
                    foreach ($activeGoals as $goal) {
                        $currentValue = $this->calculateGoalCurrentValue($partner, $goal, $performanceService);
                        $progressBefore = $goal->progress_percentage;
                        $progressAfter = $goal->target_value > 0 ? min(($currentValue / $goal->target_value) * 100, 100) : 0;
                        
                        $this->line("   🎯 {$goal->goal_type_name}: {$progressBefore}% → {$progressAfter}%");
                        
                        if ($currentValue >= $goal->target_value) {
                            $this->line("   ✅ Goal would be completed! Reward: {$goal->reward_amount} SAR");
                        }
                    }
                } else {
                    // التحديث الفعلي
                    $completedGoals = $performanceService->updateGoalsProgress($partner);
                    
                    $goalsUpdated = $activeGoals->count();
                    $goalsCompleted = count($completedGoals);
                    $achievements = $goalsCompleted;
                    $rewardsAwarded = array_sum(array_column($completedGoals, 'goal.reward_amount'));

                    $totalGoalsUpdated += $goalsUpdated;
                    $totalGoalsCompleted += $goalsCompleted;
                    $totalAchievements += $achievements;
                    $totalRewardsAwarded += $rewardsAwarded;

                    if ($goalsCompleted > 0) {
                        $this->line("   ✅ {$goalsCompleted} goals completed!");
                        foreach ($completedGoals as $completed) {
                            $goal = $completed['goal'];
                            $this->line("      🏆 {$goal->goal_type_name} - Reward: {$goal->reward_amount} SAR");
                        }
                    }
                }
            }

            // معالجة الأهداف المنتهية الصلاحية
            if (!$isDryRun) {
                $expiredGoals = PartnerGoal::where('status', 'active')
                    ->where('target_date', '<', now())
                    ->update(['status' => 'expired']);

                if ($expiredGoals > 0) {
                    $this->warn("⏰ {$expiredGoals} goals marked as expired");
                }
            }

            $this->newLine();
            $executionTime = round(microtime(true) - $startTime, 2);
            
            if ($isDryRun) {
                $this->info("📝 Dry run completed in {$executionTime}s");
            } else {
                $this->info("✅ Goals update completed!");
                $this->table([
                    'Metric', 'Count'
                ], [
                    ['Partners Processed', $partners->count()],
                    ['Goals Updated', $totalGoalsUpdated],
                    ['Goals Completed', $totalGoalsCompleted],
                    ['Achievements Created', $totalAchievements],
                    ['Total Rewards Awarded', "{$totalRewardsAwarded} SAR"],
                    ['Execution Time', "{$executionTime}s"],
                ]);

                Log::info('Partner goals updated successfully', [
                    'partners_processed' => $partners->count(),
                    'goals_updated' => $totalGoalsUpdated,
                    'goals_completed' => $totalGoalsCompleted,
                    'achievements_created' => $totalAchievements,
                    'rewards_awarded' => $totalRewardsAwarded,
                    'execution_time' => $executionTime,
                ]);
            }

            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error updating partner goals: ' . $e->getMessage());
            
            Log::error('Failed to update partner goals', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return Command::FAILURE;
        }
    }

    /**
     * Calculate current value for a goal (for dry run)
     */
    private function calculateGoalCurrentValue(Partner $partner, PartnerGoal $goal, PartnerPerformanceService $performanceService): float
    {
        $targetDate = $goal->target_date;
        $startDate = $goal->created_at;

        switch ($goal->goal_type) {
            case 'new_merchants':
                return $partner->merchants()
                    ->whereBetween('created_at', [$startDate, $targetDate])
                    ->count();

            case 'commission_earned':
                return $partner->wallet ? $partner->wallet->transactions()
                    ->where('type', 'commission')
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $targetDate])
                    ->sum('amount') : 0;

            case 'total_revenue':
                return \App\Models\Booking::whereHas('merchant', function($q) use ($partner) {
                    $q->where('partner_id', $partner->id);
                })
                ->whereBetween('created_at', [$startDate, $targetDate])
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            case 'active_merchants':
                return $partner->merchants()
                    ->whereHas('bookings', function($q) use ($startDate, $targetDate) {
                        $q->whereBetween('created_at', [$startDate, $targetDate])
                          ->where('payment_status', 'paid');
                    })
                    ->count();

            default:
                return 0;
        }
    }
}
