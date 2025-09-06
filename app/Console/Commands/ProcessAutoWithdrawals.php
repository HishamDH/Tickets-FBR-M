<?php

namespace App\Console\Commands;

use App\Models\PartnerWallet;
use App\Services\PartnerCommissionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessAutoWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partners:process-auto-withdrawals
                          {--dry-run : عرض النتائج دون تنفيذ العملية}
                          {--partner= : معالجة شريك محدد فقط}
                          {--force : تجاهل الشروط وتنفيذ العملية}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'معالجة طلبات السحب التلقائي للشركاء المؤهلين';

    protected $commissionService;

    public function __construct(PartnerCommissionService $commissionService)
    {
        parent::__construct();
        $this->commissionService = $commissionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $partnerId = $this->option('partner');
        $force = $this->option('force');

        $this->info('🚀 بدء معالجة طلبات السحب التلقائي...');
        
        if ($isDryRun) {
            $this->warn('⚠️  وضع المحاكاة - لن يتم تنفيذ أي عمليات فعلية');
        }

        // الحصول على المحافظ المؤهلة
        $query = PartnerWallet::where('auto_withdraw', true)
            ->whereNotNull('auto_withdraw_threshold');

        if ($partnerId) {
            $query->where('partner_id', $partnerId);
        }

        if (!$force) {
            $query->whereColumn('balance', '>=', 'auto_withdraw_threshold');
        }

        $eligibleWallets = $query->with('partner')->get();

        if ($eligibleWallets->isEmpty()) {
            $this->info('✅ لا توجد محافظ مؤهلة للسحب التلقائي');
            return 0;
        }

        $this->info("📊 تم العثور على {$eligibleWallets->count()} محفظة مؤهلة");

        // عرض تفاصيل المحافظ
        $headers = ['الشريك', 'الرصيد الحالي', 'حد السحب', 'المبلغ المراد سحبه'];
        $rows = [];

        foreach ($eligibleWallets as $wallet) {
            $rows[] = [
                $wallet->partner->name,
                number_format($wallet->balance, 2) . ' ريال',
                number_format($wallet->auto_withdraw_threshold, 2) . ' ريال',
                number_format($wallet->available_balance, 2) . ' ريال',
            ];
        }

        $this->table($headers, $rows);

        // تأكيد العملية
        if (!$isDryRun && !$this->confirm('هل تريد المتابعة مع معالجة طلبات السحب؟')) {
            $this->info('تم إلغاء العملية');
            return 0;
        }

        // معالجة طلبات السحب
        $processed = 0;
        $failed = 0;
        $totalAmount = 0;

        foreach ($eligibleWallets as $wallet) {
            try {
                $this->info("⏳ معالجة محفظة الشريك: {$wallet->partner->name}");

                if (!$isDryRun) {
                    $success = $this->commissionService->processAutoWithdrawal($wallet);
                    
                    if ($success) {
                        $processed++;
                        $totalAmount += $wallet->available_balance;
                        $this->info("✅ تم إنشاء طلب سحب بمبلغ " . number_format($wallet->available_balance, 2) . " ريال");
                    } else {
                        $failed++;
                        $this->error("❌ فشل في معالجة محفظة {$wallet->partner->name}");
                    }
                } else {
                    $processed++;
                    $totalAmount += $wallet->available_balance;
                    $this->info("✅ سيتم إنشاء طلب سحب بمبلغ " . number_format($wallet->available_balance, 2) . " ريال");
                }

            } catch (\Exception $e) {
                $failed++;
                $this->error("❌ خطأ في معالجة محفظة {$wallet->partner->name}: {$e->getMessage()}");
                
                Log::error('خطأ في معالجة السحب التلقائي', [
                    'wallet_id' => $wallet->id,
                    'partner_id' => $wallet->partner_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // عرض النتائج النهائية
        $this->newLine();
        $this->info('📈 ملخص النتائج:');
        $this->info("✅ تم معالجة: {$processed} طلب");
        $this->info("❌ فشل: {$failed} طلب");
        $this->info("💰 إجمالي المبلغ: " . number_format($totalAmount, 2) . " ريال");

        if (!$isDryRun && $processed > 0) {
            Log::info('تم إكمال معالجة السحوبات التلقائية', [
                'processed' => $processed,
                'failed' => $failed,
                'total_amount' => $totalAmount
            ]);
        }

        return 0;
    }
}
