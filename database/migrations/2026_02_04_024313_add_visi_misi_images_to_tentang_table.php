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
        Schema::table('tentang', function (Blueprint $table) {
            $table->string('visi_gambar1')->nullable()->after('visi');
            $table->string('visi_gambar2')->nullable()->after('visi_gambar1');
            $table->string('misi_gambar')->nullable()->after('misi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tentang', function (Blueprint $table) {
            $table->dropColumn(['visi_gambar1', 'visi_gambar2', 'misi_gambar']);
        });
    }
};
