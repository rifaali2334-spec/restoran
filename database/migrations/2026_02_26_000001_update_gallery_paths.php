<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update path gallery yang sudah ada dari format lama ke format baru
        // Format lama: filename.jpg (disimpan di public/images/gallery/)
        // Format baru: gallery/filename.jpg (disimpan di storage/app/public/gallery/)
        
        $galleries = DB::table('galleries')->get();
        
        foreach ($galleries as $gallery) {
            // Jika image tidak mengandung 'gallery/', berarti masih format lama
            if ($gallery->image && strpos($gallery->image, 'gallery/') === false) {
                DB::table('galleries')
                    ->where('id', $gallery->id)
                    ->update(['image' => 'gallery/' . $gallery->image]);
            }
        }
    }

    public function down(): void
    {
        // Kembalikan ke format lama jika rollback
        $galleries = DB::table('galleries')->get();
        
        foreach ($galleries as $gallery) {
            if ($gallery->image && strpos($gallery->image, 'gallery/') === 0) {
                $newImage = str_replace('gallery/', '', $gallery->image);
                DB::table('galleries')
                    ->where('id', $gallery->id)
                    ->update(['image' => $newImage]);
            }
        }
    }
};
