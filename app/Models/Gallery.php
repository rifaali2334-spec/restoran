<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'is_published',
    ];
    
    // Accessor untuk mendapatkan URL gambar yang benar
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        $basename = basename($this->image);
        
        // Cek di storage/gallery terlebih dahulu (untuk hosting dengan struktur berbeda)
        $storagePath = storage_path('gallery/' . $basename);
        if (file_exists($storagePath)) {
            // Untuk hosting, foto ada di storage/gallery/ langsung
            // Kita perlu serve via route atau public path
            return asset('storage/gallery/' . $basename);
        }
        
        // Cek di public/images/gallery (fallback)
        if (file_exists(public_path('images/gallery/' . $basename))) {
            return asset('images/gallery/' . $basename);
        }
        
        // Fallback ke storage standar Laravel
        return asset('storage/' . $this->image);
    }
}