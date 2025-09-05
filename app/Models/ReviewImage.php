<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ReviewImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'path',
        'type',
    ];

    /**
     * The review this image belongs to.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Get the full URL for the image.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    /**
     * Delete the file when the model is deleted.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($image) {
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        });
    }
}
