<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    use HasFactory;

    protected $table = 'contact_messages';

    protected $fillable = [
        'subject',
        'name',
        'email',
        'message',
        'is_read',
        'replied_at'
    ];
}