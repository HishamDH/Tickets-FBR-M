<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperLog
 */
class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'details',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'details' => 'json',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
