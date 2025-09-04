<?php

namespace Database\Seeders;

use App\Models\PaidReservation;
use App\Models\Offering;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PosTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a merchant user
        $merchant = User::where('user_type', 'merchant')->first();
        
        if (!$merchant) {
            $this->command->error('No merchant found. Please run UserSeeder first.');
            return;
        }

        // Get merchant's offerings
        $offerings = Offering::where('user_id', $merchant->id)->take(3)->get();
        
        if ($offerings->isEmpty()) {
            $this->command->error('No offerings found for merchant. Please run OfferingSeeder first.');
            return;
        }

        $this->command->info('Creating POS transaction test data...');

        // Create POS transactions for the last 7 days
        $paymentMethods = ['cash', 'card', 'digital'];
        $posTerminals = ['POS-001', 'POS-002', 'POS-MOBILE'];
        
        for ($day = 6; $day >= 0; $day--) {
            $date = Carbon::now()->subDays($day);
            
            // Create 5-15 transactions per day
            $transactionCount = rand(5, 15);
            
            for ($i = 0; $i < $transactionCount; $i++) {
                $offering = $offerings->random();
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $terminal = $posTerminals[array_rand($posTerminals)];
                
                // Generate transaction time within business hours
                $hour = rand(9, 22);
                $minute = rand(0, 59);
                $transactionTime = $date->copy()->setTime($hour, $minute);
                
                // Calculate amounts
                $basePrice = $offering->price;
                $quantity = rand(1, 3);
                $subtotal = $basePrice * $quantity;
                $discount = $paymentMethod === 'cash' ? rand(0, 10) : 0;
                $discountAmount = ($subtotal * $discount) / 100;
                $total = $subtotal - $discountAmount;
                
                // Prepare payment data
                $paymentData = [
                    'method' => $paymentMethod,
                    'amount' => $total,
                    'discount_percentage' => $discount,
                    'discount_amount' => $discountAmount,
                ];
                
                if ($paymentMethod === 'cash') {
                    $cashReceived = $total + rand(0, 50); // Simulate cash received
                    $paymentData['cash_received'] = $cashReceived;
                    $paymentData['change'] = $cashReceived - $total;
                }
                
                // POS specific data
                $posData = [
                    'terminal_id' => $terminal,
                    'cashier_id' => $merchant->id,
                    'transaction_type' => 'sale',
                    'items' => [
                        [
                            'offering_id' => $offering->id,
                            'name' => $offering->title,
                            'quantity' => $quantity,
                            'unit_price' => $basePrice,
                            'total' => $subtotal,
                        ]
                    ],
                    'payment' => $paymentData,
                    'receipt_number' => 'RCP-' . $date->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                ];
                
                // Additional data
                $additionalData = [
                    'source' => 'pos',
                    'pos_data' => $posData,
                    'transaction_date' => $transactionTime->toISOString(),
                ];
                
                // Create reservation
                $reservation = PaidReservation::create([
                    'item_id' => $offering->id,
                    'item_type' => 'App\\Models\\Offering',
                    'user_id' => $merchant->id, // For POS, merchant acts as customer
                    'code' => 'POS-' . $date->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'quantity' => $quantity,
                    'discount' => $discountAmount,
                    'booking_date' => $date->toDateString(),
                    'booking_time' => $transactionTime->format('H:i'),
                    'guest_count' => $quantity,
                    'total_amount' => $total,
                    'payment_status' => 'paid',
                    'reservation_status' => 'completed',
                    'qr_code' => 'POS-' . uniqid(),
                    'pos_terminal_id' => $terminal,
                    'pos_data' => $posData,
                    'printed_at' => $transactionTime,
                    'print_count' => 1,
                    'is_offline_transaction' => rand(0, 10) < 2, // 20% chance of offline transaction
                    'created_at' => $transactionTime,
                    'updated_at' => $transactionTime,
                ]);
                
                // Mark some as offline and synced later
                if ($reservation->is_offline_transaction) {
                    $reservation->update([
                        'offline_transaction_id' => 'OFF-' . uniqid(),
                        'synced_at' => $transactionTime->addMinutes(rand(5, 30)),
                    ]);
                }
            }
        }
        
        // Create some pending offline transactions (not synced yet)
        $this->createPendingOfflineTransactions($merchant, $offerings, $posTerminals, $paymentMethods);
        
        $totalTransactions = PaidReservation::where('pos_terminal_id', '!=', null)->count();
        $this->command->info("Created {$totalTransactions} POS transactions successfully!");
    }
    
    /**
     * Create pending offline transactions
     */
    private function createPendingOfflineTransactions($merchant, $offerings, $terminals, $paymentMethods)
    {
        $this->command->info('Creating pending offline transactions...');
        
        // Create 2-5 pending offline transactions
        $pendingCount = rand(2, 5);
        
        for ($i = 0; $i < $pendingCount; $i++) {
            $offering = $offerings->random();
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $terminal = $terminals[array_rand($terminals)];
            
            // Recent transactions (last 2 hours)
            $transactionTime = Carbon::now()->subMinutes(rand(30, 120));
            
            $basePrice = $offering->price;
            $quantity = rand(1, 2);
            $total = $basePrice * $quantity;
            
            $paymentData = [
                'method' => $paymentMethod,
                'amount' => $total,
            ];
            
            if ($paymentMethod === 'cash') {
                $cashReceived = $total + rand(0, 20);
                $paymentData['cash_received'] = $cashReceived;
                $paymentData['change'] = $cashReceived - $total;
            }
            
            $posData = [
                'terminal_id' => $terminal,
                'cashier_id' => $merchant->id,
                'transaction_type' => 'sale',
                'items' => [
                    [
                        'offering_id' => $offering->id,
                        'name' => $offering->title,
                        'quantity' => $quantity,
                        'unit_price' => $basePrice,
                        'total' => $total,
                    ]
                ],
                'payment' => $paymentData,
                'receipt_number' => 'OFF-' . $transactionTime->format('YmdHis') . '-' . $i,
                'offline_mode' => true,
            ];
            
            $reservation = PaidReservation::create([
                'item_id' => $offering->id,
                'item_type' => 'App\\Models\\Offering',
                'user_id' => $merchant->id,
                'code' => 'OFF-' . $transactionTime->format('YmdHis') . '-' . $i,
                'quantity' => $quantity,
                'discount' => 0,
                'booking_date' => $transactionTime->toDateString(),
                'booking_time' => $transactionTime->format('H:i'),
                'guest_count' => $quantity,
                'total_amount' => $total,
                'payment_status' => 'paid',
                'reservation_status' => 'completed',
                'qr_code' => 'OFF-' . uniqid(),
                'pos_terminal_id' => $terminal,
                'pos_data' => $posData,
                'printed_at' => $transactionTime,
                'print_count' => 1,
                'is_offline_transaction' => true,
                'offline_transaction_id' => 'PENDING-' . uniqid(),
                'synced_at' => null, // Not synced yet
                'created_at' => $transactionTime,
                'updated_at' => $transactionTime,
            ]);
        }
        
        $this->command->info("Created {$pendingCount} pending offline transactions!");
    }
}
