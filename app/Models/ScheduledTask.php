<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledTask whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperScheduledTask
 */
class ScheduledTask extends Model
{
    use HasFactory;
}
