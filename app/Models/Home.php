<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    use HasFactory;

    protected $table = 'home';

    protected $fillable = [
        'subtitle',
        'title',
        'description',
        'button_text',
        'hero_image',
        'about_title',
        'about_description',
        'news_title',
        'status'
    ];
}