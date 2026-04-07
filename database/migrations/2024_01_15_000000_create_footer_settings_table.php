<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->text('company_description');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('contact_address');
            $table->string('social_facebook')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_instagram')->nullable();
            $table->timestamps();
        });
        
        // Insert default data
        DB::table('footer_settings')->insert([
            'company_name' => 'Tasty Food',
            'company_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'contact_email' => 'tastyfood@gmail.com',
            'contact_phone' => '+62 812 3456 7890',
            'contact_address' => 'Kota Bandung, Jawa Barat',
            'social_facebook' => '#',
            'social_twitter' => '#',
            'social_instagram' => '#',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('footer_settings');
    }
};
