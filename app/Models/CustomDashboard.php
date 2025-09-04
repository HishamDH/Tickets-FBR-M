<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property array $config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomDashboard whereUserId($value)
 * @mixin \Eloquent
 */
class CustomDashboard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'config',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'config' => 'array',
    ];

    /**
     * Get the user that owns the custom dashboard.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
