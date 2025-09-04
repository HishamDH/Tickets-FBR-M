<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|Support active()
 * @method static \Illuminate\Database\Eloquent\Builder|Support byCategory($category)
 * @method static \Illuminate\Database\Eloquent\Builder|Support newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Support newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Support query()
 * @mixin \Eloquent
 * @mixin IdeHelperSupport
 */
class Support extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
