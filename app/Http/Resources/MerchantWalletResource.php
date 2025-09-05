<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantWalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'merchant_id' => $this->merchant_id,
            'balance' => (float) $this->balance,
            'balance_formatted' => number_format($this->balance, 2) . ' ريال',
            'pending_balance' => (float) $this->pending_balance,
            'pending_balance_formatted' => number_format($this->pending_balance, 2) . ' ريال',
            'total_earned' => (float) $this->total_earned,
            'total_earned_formatted' => number_format($this->total_earned, 2) . ' ريال',
            'total_withdrawn' => (float) $this->total_withdrawn,
            'total_withdrawn_formatted' => number_format($this->total_withdrawn, 2) . ' ريال',
            'currency' => $this->currency ?? 'SAR',
            'status' => $this->status ?? 'active',
            'last_payout_at' => $this->last_payout_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}