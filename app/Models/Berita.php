<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'konten',
        'gambar',
        'tanggal_publish',
        'status'
    ];

    protected $casts = [
        'tanggal_publish' => 'datetime'
    ];
}