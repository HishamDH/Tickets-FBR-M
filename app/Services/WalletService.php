<?php

namespace App\Services;

use App\Models\MerchantWallet;
use App\Models\WalletTransaction;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class WalletService
{
    /**
     * Get or create merchant wallet
     */
    public function getOrCreateWallet(int $merchantId): MerchantWallet
    {
        return MerchantWallet::firstOrCreate(
            ['user_id' => $merchantId],
            ['balance' => 0]
        );
    }

    /**
     * Add commission to merchant wallet when booking is completed
     */
    public function addCommission(Booking $booking, Payment $payment): WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($booking->merchant_id);
        
        // Calculate commission (payment amount minus platform fee)
        $platformFeeRate = $this->getPlatformFeeRate($booking->merchant);
        $platformFee = $payment->amount * ($platformFeeRate / 100);
        $commissionAmount = $payment->amount - $platformFee;

        DB::beginTransaction();
        try {
            // Update wallet balance
            $oldBalance = $wallet->balance;
            $wallet->increment('balance', $commissionAmount);
            $newBalance = $wallet->fresh()->balance;

            // Record transaction
            $transaction = WalletTransaction::create([
                'transaction_reference' => $this->generateTransactionReference('COM'),
                'merchant_wallet_id' => $wallet->id,
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'type' => 'credit',
                'category' => 'commission',
                'amount' => $commissionAmount,
                'balance_after' => $newBalance,
                'description' => "Commission for booking #{$booking->booking_reference}",
                'metadata' => [
                    'payment_amount' => $payment->amount,
                    'platform_fee_rate' => $platformFeeRate,
                    'platform_fee' => $platformFee,
                    'old_balance' => $oldBalance,
                ],
                'processed_at' => now(),
            ]);

            DB::commit();
            
            Log::info("Commission added to merchant wallet", [
                'merchant_id' => $booking->merchant_id,
                'booking_id' => $booking->id,
                'amount' => $commissionAmount,
                'new_balance' => $newBalance
            ]);

            return $transaction;
        } catch (Exception $e) {
            DB::rollback();
            Log::error("Failed to add commission", [
                'merchant_id' => $booking->merchant_id,
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Process payout to merchant
     */
    public function processPayout(int $merchantId, float $amount, string $method = 'bank_transfer', array $metadata = []): WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($merchantId);

        if ($wallet->balance < $amount) {
            throw new Exception("Insufficient wallet balance. Available: {$wallet->balance}, Requested: {$amount}");
        }

        DB::beginTransaction();
        try {
            // Update wallet balance
            $oldBalance = $wallet->balance;
            $wallet->decrement('balance', $amount);
            $newBalance = $wallet->fresh()->balance;

            // Record transaction
            $transaction = WalletTransaction::create([
                'transaction_reference' => $this->generateTransactionReference('PAY'),
                'merchant_wallet_id' => $wallet->id,
                'type' => 'debit',
                'category' => 'payout',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => "Payout via {$method}",
                'metadata' => array_merge([
                    'payout_method' => $method,
                    'old_balance' => $oldBalance,
                ], $metadata),
                'status' => 'pending',
                'processed_at' => now(),
            ]);

            DB::commit();
            
            Log::info("Payout processed", [
                'merchant_id' => $merchantId,
                'amount' => $amount,
                'method' => $method,
                'new_balance' => $newBalance
            ]);

            // Here you would integrate with actual payout provider
            $this->processExternalPayout($transaction);

            return $transaction;
        } catch (Exception $e) {
            DB::rollback();
            Log::error("Payout processing failed", [
                'merchant_id' => $merchantId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle refund impact on merchant wallet
     */
    public function processRefundDeduction(Booking $booking, float $refundAmount): WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($booking->merchant_id);

        DB::beginTransaction();
        try {
            // Update wallet balance
            $oldBalance = $wallet->balance;
            $wallet->decrement('balance', $refundAmount);
            $newBalance = $wallet->fresh()->balance;

            // Record transaction
            $transaction = WalletTransaction::create([
                'transaction_reference' => $this->generateTransactionReference('REF'),
                'merchant_wallet_id' => $wallet->id,
                'booking_id' => $booking->id,
                'type' => 'debit',
                'category' => 'refund',
                'amount' => $refundAmount,
                'balance_after' => $newBalance,
                'description' => "Refund deduction for booking #{$booking->booking_reference}",
                'metadata' => [
                    'refund_amount' => $refundAmount,
                    'old_balance' => $oldBalance,
                    'booking_reference' => $booking->booking_reference,
                ],
                'processed_at' => now(),
            ]);

            DB::commit();
            
            Log::info("Refund deducted from merchant wallet", [
                'merchant_id' => $booking->merchant_id,
                'booking_id' => $booking->id,
                'amount' => $refundAmount,
                'new_balance' => $newBalance
            ]);

            return $transaction;
        } catch (Exception $e) {
            DB::rollback();
            Log::error("Refund deduction failed", [
                'merchant_id' => $booking->merchant_id,
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Add manual adjustment to wallet
     */
    public function addAdjustment(int $merchantId, float $amount, string $reason, string $type = 'adjustment'): WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($merchantId);

        DB::beginTransaction();
        try {
            $oldBalance = $wallet->balance;
            
            if ($amount > 0) {
                $wallet->increment('balance', $amount);
                $transactionType = 'credit';
            } else {
                $wallet->decrement('balance', abs($amount));
                $transactionType = 'debit';
            }
            
            $newBalance = $wallet->fresh()->balance;

            $transaction = WalletTransaction::create([
                'transaction_reference' => $this->generateTransactionReference('ADJ'),
                'merchant_wallet_id' => $wallet->id,
                'type' => $transactionType,
                'category' => $type,
                'amount' => abs($amount),
                'balance_after' => $newBalance,
                'description' => $reason,
                'metadata' => [
                    'adjustment_amount' => $amount,
                    'old_balance' => $oldBalance,
                    'reason' => $reason,
                ],
                'processed_at' => now(),
            ]);

            DB::commit();
            
            Log::info("Wallet adjustment applied", [
                'merchant_id' => $merchantId,
                'amount' => $amount,
                'type' => $type,
                'new_balance' => $newBalance
            ]);

            return $transaction;
        } catch (Exception $e) {
            DB::rollback();
            Log::error("Wallet adjustment failed", [
                'merchant_id' => $merchantId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get wallet balance
     */
    public function getBalance(int $merchantId): float
    {
        $wallet = $this->getOrCreateWallet($merchantId);
        return $wallet->balance;
    }

    /**
     * Get wallet transaction history
     */
    public function getTransactionHistory(int $merchantId, int $limit = 50, ?string $category = null)
    {
        $wallet = $this->getOrCreateWallet($merchantId);
        
        $query = WalletTransaction::where('merchant_wallet_id', $wallet->id)
            ->with(['booking', 'payment'])
            ->orderBy('created_at', 'desc');
            
        if ($category) {
            $query->where('category', $category);
        }
        
        return $query->paginate($limit);
    }

    /**
     * Get wallet statistics
     */
    public function getWalletStats(int $merchantId, ?string $period = 'month'): array
    {
        $wallet = $this->getOrCreateWallet($merchantId);
        
        $dateFrom = match($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $transactions = WalletTransaction::where('merchant_wallet_id', $wallet->id)
            ->where('created_at', '>=', $dateFrom)
            ->get();

        return [
            'current_balance' => $wallet->balance,
            'period' => $period,
            'total_transactions' => $transactions->count(),
            'total_credits' => $transactions->where('type', 'credit')->sum('amount'),
            'total_debits' => $transactions->where('type', 'debit')->sum('amount'),
            'total_commissions' => $transactions->where('category', 'commission')->sum('amount'),
            'total_payouts' => $transactions->where('category', 'payout')->sum('amount'),
            'total_refunds' => $transactions->where('category', 'refund')->sum('amount'),
            'pending_payouts' => $transactions->where('category', 'payout')->where('status', 'pending')->sum('amount'),
        ];
    }

    /**
     * Check if merchant can request payout
     */
    public function canRequestPayout(int $merchantId, float $amount): array
    {
        $wallet = $this->getOrCreateWallet($merchantId);
        $minPayoutAmount = 50; // Minimum payout amount
        
        $result = [
            'can_payout' => false,
            'available_balance' => $wallet->balance,
            'requested_amount' => $amount,
            'message' => '',
        ];

        if ($amount < $minPayoutAmount) {
            $result['message'] = "Minimum payout amount is {$minPayoutAmount}";
        } elseif ($wallet->balance < $amount) {
            $result['message'] = "Insufficient balance";
        } else {
            $result['can_payout'] = true;
            $result['message'] = "Payout can be processed";
        }

        return $result;
    }

    /**
     * Get platform fee rate for merchant
     */
    protected function getPlatformFeeRate(User $merchant): float
    {
        // This could be based on merchant tier, volume, etc.
        return $merchant->commission_rate ?? 10.0; // Default 10%
    }

    /**
     * Generate unique transaction reference
     */
    protected function generateTransactionReference(string $prefix = 'TXN'): string
    {
        return $prefix . '-' . strtoupper(uniqid());
    }

    /**
     * Process external payout (integrate with payment provider)
     */
    protected function processExternalPayout(WalletTransaction $transaction): void
    {
        try {
            // Here you would integrate with actual payout provider
            // For now, we'll simulate a successful payout
            sleep(1); // Simulate API call
            
            $transaction->update([
                'status' => 'completed',
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'external_reference' => 'EXT_' . uniqid(),
                    'processed_by' => 'system',
                    'completion_time' => now(),
                ])
            ]);
            
            Log::info("External payout processed successfully", [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount
            ]);
        } catch (Exception $e) {
            $transaction->update(['status' => 'failed']);
            Log::error("External payout failed", [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get pending payouts for admin review
     */
    public function getPendingPayouts(int $limit = 50)
    {
        return WalletTransaction::where('category', 'payout')
            ->where('status', 'pending')
            ->with(['merchantWallet.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Approve payout (admin action)
     */
    public function approvePayout(WalletTransaction $transaction): bool
    {
        if ($transaction->category !== 'payout' || $transaction->status !== 'pending') {
            throw new Exception('Only pending payout transactions can be approved');
        }

        return DB::transaction(function() use ($transaction) {
            $transaction->update(['status' => 'completed']);
            $this->processExternalPayout($transaction);
            return true;
        });
    }
}