<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperAdvancedSetting
 * @property int $id
 * @property string $key
 * @property array $value
 * @property string|null $description
 * @property string $type
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvancedSetting whereValue($value)
 * @mixin \Eloquent
 */
class AdvancedSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'value' => 'json',
    ];
}
