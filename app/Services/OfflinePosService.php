<?php

namespace App\Services;

use App\Models\PaidReservation;
use App\Models\Offering;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OfflinePosService
{
    private $offlineStoragePath = 'offline/pos/';
    private $offlineTransactionsFile = 'offline_transactions.json';
    private $offlineQueueFile = 'offline_queue.json';
    private $lastSyncFile = 'last_sync.json';

    /**
     * Store transaction offline
     */
    public function storeOfflineTransaction(array $transactionData)
    {
        try {
            $merchantId = Auth::id();
            $transactionData['merchant_id'] = $merchantId;
            $transactionData['offline_timestamp'] = now()->toISOString();
            $transactionData['offline_id'] = uniqid('offline_', true);
            $transactionData['sync_status'] = 'pending';

            // Create offline storage directory if not exists
            $merchantPath = $this->offlineStoragePath . $merchantId . '/';
            Storage::makeDirectory($merchantPath);

            // Load existing offline transactions
            $filePath = $merchantPath . $this->offlineTransactionsFile;
            $existingTransactions = [];
            
            if (Storage::exists($filePath)) {
                $content = Storage::get($filePath);
                $existingTransactions = json_decode($content, true) ?: [];
            }

            // Add new transaction
            $existingTransactions[] = $transactionData;

            // Save updated transactions
            Storage::put($filePath, json_encode($existingTransactions, JSON_PRETTY_PRINT));

            Log::info('Offline transaction stored', [
                'offline_id' => $transactionData['offline_id'],
                'merchant_id' => $merchantId,
            ]);

            return [
                'success' => true,
                'offline_id' => $transactionData['offline_id'],
                'message' => 'Transaction stored offline successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Offline transaction storage error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to store transaction offline'
            ];
        }
    }

    /**
     * Get offline transactions
     */
    public function getOfflineTransactions($status = null)
    {
        try {
            $merchantId = Auth::id();
            $filePath = $this->offlineStoragePath . $merchantId . '/' . $this->offlineTransactionsFile;

            if (!Storage::exists($filePath)) {
                return [];
            }

            $content = Storage::get($filePath);
            $transactions = json_decode($content, true) ?: [];

            if ($status) {
                $transactions = array_filter($transactions, function ($transaction) use ($status) {
                    return $transaction['sync_status'] === $status;
                });
            }

            return array_values($transactions);

        } catch (\Exception $e) {
            Log::error('Get offline transactions error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Sync offline transactions to database
     */
    public function syncOfflineTransactions()
    {
        try {
            $merchantId = Auth::id();
            $transactions = $this->getOfflineTransactions('pending');

            if (empty($transactions)) {
                return [
                    'success' => true,
                    'synced_count' => 0,
                    'message' => 'No offline transactions to sync'
                ];
            }

            $syncedCount = 0;
            $failedCount = 0;
            $syncResults = [];

            DB::beginTransaction();

            foreach ($transactions as $index => $transactionData) {
                try {
                    // Create online reservation from offline data
                    $reservation = $this->createReservationFromOfflineData($transactionData);

                    if ($reservation) {
                        // Mark as synced
                        $transactionData['sync_status'] = 'synced';
                        $transactionData['synced_at'] = now()->toISOString();
                        $transactionData['online_reservation_id'] = $reservation->id;
                        $syncedCount++;

                        $syncResults[] = [
                            'offline_id' => $transactionData['offline_id'],
                            'status' => 'synced',
                            'reservation_id' => $reservation->id,
                        ];
                    } else {
                        $transactionData['sync_status'] = 'failed';
                        $failedCount++;

                        $syncResults[] = [
                            'offline_id' => $transactionData['offline_id'],
                            'status' => 'failed',
                            'error' => 'Failed to create reservation',
                        ];
                    }

                    // Update transaction status
                    $transactions[$index] = $transactionData;

                } catch (\Exception $e) {
                    $transactionData['sync_status'] = 'failed';
                    $transactionData['sync_error'] = $e->getMessage();
                    $transactions[$index] = $transactionData;
                    $failedCount++;

                    $syncResults[] = [
                        'offline_id' => $transactionData['offline_id'],
                        'status' => 'failed',
                        'error' => $e->getMessage(),
                    ];

                    Log::error('Sync transaction error', [
                        'offline_id' => $transactionData['offline_id'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();

            // Save updated transactions
            $filePath = $this->offlineStoragePath . $merchantId . '/' . $this->offlineTransactionsFile;
            Storage::put($filePath, json_encode($transactions, JSON_PRETTY_PRINT));

            // Update last sync time
            $this->updateLastSyncTime();

            Log::info('Offline sync completed', [
                'merchant_id' => $merchantId,
                'synced_count' => $syncedCount,
                'failed_count' => $failedCount,
            ]);

            return [
                'success' => true,
                'synced_count' => $syncedCount,
                'failed_count' => $failedCount,
                'total_count' => count($transactions),
                'results' => $syncResults,
                'message' => "Sync completed: {$syncedCount} successful, {$failedCount} failed"
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Offline sync error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create reservation from offline data
     */
    private function createReservationFromOfflineData($transactionData)
    {
        try {
            $offering = Offering::find($transactionData['offering_id']);
            
            if (!$offering || $offering->user_id !== $transactionData['merchant_id']) {
                throw new \Exception('Invalid offering or unauthorized access');
            }

            // Create reservation
            $reservation = new PaidReservation();
            $reservation->offering_id = $transactionData['offering_id'];
            $reservation->user_name = $transactionData['customer']['name'];
            $reservation->user_email = $transactionData['customer']['email'] ?? null;
            $reservation->user_phone = $transactionData['customer']['phone'] ?? null;
            $reservation->num_people = $transactionData['num_people'];
            $reservation->total_amount = $transactionData['total_amount'];
            $reservation->status = 'paid';
            $reservation->payment_intent_id = 'offline_' . $transactionData['offline_id'];
            
            // Add offline source information
            $additionalData = $transactionData['additional_data'] ?? [];
            $additionalData['source'] = 'pos_offline';
            $additionalData['offline_id'] = $transactionData['offline_id'];
            $additionalData['offline_timestamp'] = $transactionData['offline_timestamp'];
            $additionalData['synced_at'] = now()->toISOString();
            
            $reservation->additional_data = $additionalData;
            $reservation->created_at = Carbon::parse($transactionData['offline_timestamp']);
            $reservation->updated_at = now();

            $reservation->save();

            return $reservation;

        } catch (\Exception $e) {
            Log::error('Create reservation from offline data error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if offline mode is enabled
     */
    public function isOfflineModeEnabled()
    {
        return config('pos.offline_mode.enabled', false);
    }

    /**
     * Get offline statistics
     */
    public function getOfflineStats()
    {
        try {
            $merchantId = Auth::id();
            $transactions = $this->getOfflineTransactions();

            $stats = [
                'total_transactions' => count($transactions),
                'pending_sync' => count(array_filter($transactions, fn($t) => $t['sync_status'] === 'pending')),
                'synced' => count(array_filter($transactions, fn($t) => $t['sync_status'] === 'synced')),
                'failed_sync' => count(array_filter($transactions, fn($t) => $t['sync_status'] === 'failed')),
                'total_amount' => array_sum(array_column($transactions, 'total_amount')),
                'last_sync' => $this->getLastSyncTime(),
                'storage_size' => $this->getOfflineStorageSize(),
            ];

            return $stats;

        } catch (\Exception $e) {
            Log::error('Get offline stats error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Clear synced transactions (cleanup)
     */
    public function clearSyncedTransactions()
    {
        try {
            $merchantId = Auth::id();
            $transactions = $this->getOfflineTransactions();

            // Keep only pending and failed transactions
            $keepTransactions = array_filter($transactions, function ($transaction) {
                return in_array($transaction['sync_status'], ['pending', 'failed']);
            });

            // Save filtered transactions
            $filePath = $this->offlineStoragePath . $merchantId . '/' . $this->offlineTransactionsFile;
            Storage::put($filePath, json_encode(array_values($keepTransactions), JSON_PRETTY_PRINT));

            $removedCount = count($transactions) - count($keepTransactions);

            Log::info('Offline transactions cleanup', [
                'merchant_id' => $merchantId,
                'removed_count' => $removedCount,
                'remaining_count' => count($keepTransactions),
            ]);

            return [
                'success' => true,
                'removed_count' => $removedCount,
                'remaining_count' => count($keepTransactions),
                'message' => "Cleanup completed: {$removedCount} synced transactions removed"
            ];

        } catch (\Exception $e) {
            Log::error('Clear synced transactions error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update last sync time
     */
    private function updateLastSyncTime()
    {
        try {
            $merchantId = Auth::id();
            $filePath = $this->offlineStoragePath . $merchantId . '/' . $this->lastSyncFile;
            
            $syncData = [
                'last_sync' => now()->toISOString(),
                'merchant_id' => $merchantId,
            ];

            Storage::put($filePath, json_encode($syncData, JSON_PRETTY_PRINT));

        } catch (\Exception $e) {
            Log::error('Update last sync time error: ' . $e->getMessage());
        }
    }

    /**
     * Get last sync time
     */
    private function getLastSyncTime()
    {
        try {
            $merchantId = Auth::id();
            $filePath = $this->offlineStoragePath . $merchantId . '/' . $this->lastSyncFile;

            if (!Storage::exists($filePath)) {
                return null;
            }

            $content = Storage::get($filePath);
            $syncData = json_decode($content, true);

            return $syncData['last_sync'] ?? null;

        } catch (\Exception $e) {
            Log::error('Get last sync time error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get offline storage size
     */
    private function getOfflineStorageSize()
    {
        try {
            $merchantId = Auth::id();
            $merchantPath = $this->offlineStoragePath . $merchantId . '/';
            
            $size = 0;
            $files = Storage::files($merchantPath);
            
            foreach ($files as $file) {
                $size += Storage::size($file);
            }

            return $size; // Size in bytes

        } catch (\Exception $e) {
            Log::error('Get offline storage size error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Export offline data for backup
     */
    public function exportOfflineData()
    {
        try {
            $merchantId = Auth::id();
            $transactions = $this->getOfflineTransactions();
            $stats = $this->getOfflineStats();

            $exportData = [
                'merchant_id' => $merchantId,
                'export_date' => now()->toISOString(),
                'transactions' => $transactions,
                'statistics' => $stats,
                'version' => '1.0',
            ];

            $fileName = "offline_export_{$merchantId}_" . now()->format('Y-m-d_H-i-s') . '.json';
            $exportPath = $this->offlineStoragePath . $merchantId . '/exports/' . $fileName;

            Storage::put($exportPath, json_encode($exportData, JSON_PRETTY_PRINT));

            return [
                'success' => true,
                'file_path' => $exportPath,
                'file_name' => $fileName,
                'file_size' => Storage::size($exportPath),
                'transactions_count' => count($transactions),
                'message' => 'Offline data exported successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Export offline data error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ];
        }
    }
}
