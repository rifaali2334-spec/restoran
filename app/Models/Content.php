<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $table = 'contents';

    protected $fillable = [
        'key',
        'title',
        'content',
        'image',
        'meta_data',
        'is_active'
    ];

    protected $casts = [
        'meta_data' => 'array',
        'is_active' => 'boolean'
    ];

    // Helper method untuk get content by key
    public static function getByKey($key, $default = null)
    {
        $content = self::where('key', $key)->where('is_active', true)->first();
        return $content ? $content : $default;
    }

    // Helper method untuk get value by key
    public static function getValue($key, $field = 'content', $default = null)
    {
        $content = self::getByKey($key);
        return $content ? $content->$field : $default;
    }
    
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