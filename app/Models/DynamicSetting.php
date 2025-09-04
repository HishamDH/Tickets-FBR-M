<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperDynamicSetting
 * @property int $id
 * @property string $key
 * @property string $value
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DynamicSetting whereValue($value)
 * @mixin \Eloquent
 */
class DynamicSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'description',
    ];
}
