<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|FaqItem active()
 * @method static \Illuminate\Database\Eloquent\Builder|FaqItem byCategory($category)
 * @method static \Illuminate\Database\Eloquent\Builder|FaqItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FaqItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FaqItem query()
 */
class FaqItem extends Model
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
