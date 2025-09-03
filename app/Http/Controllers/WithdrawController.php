<?php

namespace App\Http\Controllers;

use App\Models\MerchantWallet;
use App\Models\MerchantWithdraw;
use App\Models\WithdrawLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:wallet_view')->only(['index', 'show']);
        $this->middleware('permission:wallet_withdraw')->only(['create', 'store']);
    }

    /**
     * Display withdrawal requests
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $withdrawals = MerchantWithdraw::where('merchant_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $wallet = MerchantWallet::firstOrCreate(
            ['merchant_id' => $user->id],
            ['balance' => 0, 'pending_balance' => 0]
        );

        return view('withdrawals.index', compact('withdrawals', 'wallet'));
    }

    /**
     * Show withdrawal request form
     */
    public function create()
    {
        $user = Auth::user();

        $wallet = MerchantWallet::firstOrCreate(
            ['merchant_id' => $user->id],
            ['balance' => 0, 'pending_balance' => 0]
        );

        if ($wallet->balance <= 0) {
            return redirect()->route('withdrawals.index')
                ->with('error', 'Insufficient balance for withdrawal');
        }

        return view('withdrawals.create', compact('wallet'));
    }

    /**
     * Store withdrawal request
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:50000',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:100',
            'iban' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $wallet = MerchantWallet::where('merchant_id', $user->id)->first();

            if (! $wallet || $wallet->balance < $request->amount) {
                return back()->with('error', 'Insufficient balance for withdrawal');
            }

            // Check if there's a pending withdrawal
            $pendingWithdraw = MerchantWithdraw::where('merchant_id', $user->id)
                ->where('status', 'pending')
                ->exists();

            if ($pendingWithdraw) {
                return back()->with('error', 'You already have a pending withdrawal request');
            }

            // Create withdrawal request
            $withdrawal = MerchantWithdraw::create([
                'merchant_id' => $user->id,
                'amount' => $request->amount,
                'status' => 'pending',
                'bank_details' => [
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_holder_name' => $request->account_holder_name,
                    'iban' => $request->iban,
                    'swift_code' => $request->swift_code,
                ],
                'notes' => $request->notes,
                'requested_at' => now(),
            ]);

            // Update wallet balance
            $wallet->balance -= $request->amount;
            $wallet->pending_balance += $request->amount;
            $wallet->save();

            // Log the withdrawal request
            WithdrawLog::create([
                'merchant_withdraw_id' => $withdrawal->id,
                'merchant_id' => $user->id,
                'action' => 'requested',
                'amount' => $request->amount,
                'status' => 'pending',
                'performed_by' => $user->id,
                'notes' => 'Withdrawal request submitted',
                'action_date' => now(),
            ]);

            DB::commit();

            return redirect()->route('withdrawals.index')
                ->with('success', 'Withdrawal request submitted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal request error: '.$e->getMessage());

            return back()->with('error', 'Error processing withdrawal request');
        }
    }

    /**
     * Show withdrawal details
     */
    public function show(MerchantWithdraw $withdrawal)
    {
        // Check if withdrawal belongs to current merchant
        if ($withdrawal->merchant_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $logs = WithdrawLog::where('merchant_withdraw_id', $withdrawal->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('withdrawals.show', compact('withdrawal', 'logs'));
    }

    /**
     * Cancel withdrawal request (only if pending)
     */
    public function cancel(MerchantWithdraw $withdrawal)
    {
        // Check if withdrawal belongs to current merchant
        if ($withdrawal->merchant_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending withdrawals can be cancelled');
        }

        try {
            DB::beginTransaction();

            // Update withdrawal status
            $withdrawal->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Restore wallet balance
            $wallet = MerchantWallet::where('merchant_id', $withdrawal->merchant_id)->first();
            if ($wallet) {
                $wallet->balance += $withdrawal->amount;
                $wallet->pending_balance -= $withdrawal->amount;
                $wallet->save();
            }

            // Log the cancellation
            WithdrawLog::create([
                'merchant_withdraw_id' => $withdrawal->id,
                'merchant_id' => $withdrawal->merchant_id,
                'action' => 'cancelled',
                'amount' => $withdrawal->amount,
                'status' => 'cancelled',
                'performed_by' => Auth::id(),
                'notes' => 'Withdrawal cancelled by merchant',
                'action_date' => now(),
            ]);

            DB::commit();

            return redirect()->route('withdrawals.index')
                ->with('success', 'Withdrawal request cancelled successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal cancellation error: '.$e->getMessage());

            return back()->with('error', 'Error cancelling withdrawal request');
        }
    }
}
