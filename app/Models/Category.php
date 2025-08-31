<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get all offerings in this category
     */
    public function offerings()
    {
        return $this->hasMany(Offering::class, 'category', 'name');
    }
}
