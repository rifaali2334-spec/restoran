<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General Settings
            [
                'key' => 'company_name',
                'value' => 'Tasty Food',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Nama perusahaan yang ditampilkan di footer'
            ],
            [
                'key' => 'company_description',
                'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'type' => 'textarea',
                'group' => 'general',
                'description' => 'Deskripsi perusahaan di footer'
            ],
            
            // Contact Settings
            [
                'key' => 'contact_email',
                'value' => 'tastyfood@gmail.com',
                'type' => 'email',
                'group' => 'contact',
                'description' => 'Email kontak perusahaan'
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62 812 3456 7890',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'Nomor telepon perusahaan'
            ],
            [
                'key' => 'contact_address',
                'value' => 'Kota Bandung, Jawa Barat',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'Alamat perusahaan'
            ],
            
            // Social Media Settings
            [
                'key' => 'social_facebook',
                'value' => '#',
                'type' => 'url',
                'group' => 'social',
                'description' => 'Link Facebook'
            ],
            [
                'key' => 'social_twitter',
                'value' => '#',
                'type' => 'url',
                'group' => 'social',
                'description' => 'Link Twitter'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
