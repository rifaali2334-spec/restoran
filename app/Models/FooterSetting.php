<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    protected $fillable = [
        'company_name',
        'company_description',
        'contact_email',
        'contact_phone',
        'contact_address',
        'social_facebook',
        'social_twitter',
        'social_instagram',
    ];
}
