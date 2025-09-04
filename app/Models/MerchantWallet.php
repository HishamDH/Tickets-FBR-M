<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperMerchantWallet
 * @property int $id
 * @property int $user_id
 * @property string $balance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereUserId($value)
 * @mixin \Eloquent
 */
class MerchantWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
