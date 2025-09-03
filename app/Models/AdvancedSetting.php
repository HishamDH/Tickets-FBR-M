<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperAdvancedSetting
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
