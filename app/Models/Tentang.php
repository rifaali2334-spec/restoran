<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tentang extends Model
{
    use HasFactory;

    protected $table = 'tentang';

    protected $fillable = [
        'judul',
        'konten',
        'gambar',
        'gambar2',
        'visi',
        'visi_gambar1',
        'visi_gambar2',
        'misi',
        'misi_gambar',
        'status'
    ];
    
    // Accessor untuk gambar utama
    public function getGambarUrlAttribute()
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }
    
    // Accessor untuk gambar 2
    public function getGambar2UrlAttribute()
    {
        return $this->gambar2 ? asset('storage/' . $this->gambar2) : null;
    }
    
    // Accessor untuk visi gambar 1
    public function getVisiGambar1UrlAttribute()
    {
        return $this->visi_gambar1 ? asset('storage/' . $this->visi_gambar1) : null;
    }
    
    // Accessor untuk visi gambar 2
    public function getVisiGambar2UrlAttribute()
    {
        return $this->visi_gambar2 ? asset('storage/' . $this->visi_gambar2) : null;
    }
    
    // Accessor untuk misi gambar
    public function getMisiGambarUrlAttribute()
    {
        return $this->misi_gambar ? asset('storage/' . $this->misi_gambar) : null;
    }
}