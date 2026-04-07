<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->string('title', 255)->nullable();
            $table->text('content')->nullable();
            $table->string('image', 255)->nullable();
            $table->json('meta_data')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('key');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};