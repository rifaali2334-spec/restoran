<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'is_featured',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];
    
    // Accessor untuk mendapatkan URL gambar yang benar
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        // Gunakan asset helper dengan path storage standar Laravel
        return asset('storage/' . $this->image);
    }
}