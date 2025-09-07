<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'offering_id',
        'name',
        'description',
        'price',
        'quantity',
        'available_quantity',
    ];

    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }
}
