<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Statistics newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Statistics newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Statistics query()
 * @method static \Illuminate\Database\Eloquent\Builder|Statistics whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Statistics whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Statistics whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperStatistics
 */
class Statistics extends Model
{
    use HasFactory;
}
