<?php
// Upload ke public_html/aditya/ lalu akses via browser SEKALI SAJA

$abiPath = dirname(__DIR__) . '/abi';
$file = $abiPath . '/app/Http/Controllers/GaleriController.php';

// Deteksi path aditya/storage
$adityaStorage = dirname(__DIR__) . '/storage';

$newController = <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    public function public()
    {
        \$galeris = Galeri::where('status', true)->get();
        return view('galeri', compact('galeris'));
    }

    public function index()
    {
        \$galeris = Galeri::all();
        return view('admin.galeri.index', compact('galeris'));
    }

    public function create()
    {
        return view('admin.galeri.create');
    }

    public function store(Request \$request)
    {
        \$request->validate([
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        \$gambar = null;
        if (\$request->hasFile('gambar')) {
            \$file = \$request->file('gambar');
            \$filename = time() . '_' . \$file->getClientOriginalName();

            // Simpan ke storage/app/public/galeri
            \$storageDir = storage_path('app/public/galeri');
            if (!is_dir(\$storageDir)) mkdir(\$storageDir, 0755, true);
            \$file->move(\$storageDir, \$filename);
            \$gambar = 'galeri/' . \$filename;

            // Sync ke public (aditya/storage/galeri)
            \$publicDir = dirname(dirname(app()->publicPath())) . '/aditya/storage/galeri';
            if (!is_dir(\$publicDir)) mkdir(\$publicDir, 0755, true);
            @copy(\$storageDir . '/' . \$filename, \$publicDir . '/' . \$filename);
        }

        \$isCarousel = \$request->has('is_carousel');
        if (\$isCarousel && Galeri::where('is_carousel', true)->count() >= 6) {
            Galeri::where('is_carousel', true)->orderBy('updated_at', 'asc')->first()->update(['is_carousel' => false]);
        }

        Galeri::create([
            'gambar'      => \$gambar,
            'status'      => \$request->has('status'),
            'is_carousel' => \$isCarousel,
        ]);

        return redirect()->route('admin.galeri.index')->with('success', 'Galeri berhasil ditambahkan');
    }

    public function edit(Galeri \$galeri)
    {
        return view('admin.galeri.edit', compact('galeri'));
    }

    public function update(Request \$request, Galeri \$galeri)
    {
        \$request->validate([
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        \$data = ['status' => \$request->has('status')];

        if (\$request->hasFile('gambar')) {
            \$file = \$request->file('gambar');
            \$filename = time() . '_' . \$file->getClientOriginalName();

            \$storageDir = storage_path('app/public/galeri');
            if (!is_dir(\$storageDir)) mkdir(\$storageDir, 0755, true);

            // Hapus foto lama
            if (\$galeri->gambar) {
                @unlink(\$storageDir . '/' . basename(\$galeri->gambar));
                \$publicDir = dirname(dirname(app()->publicPath())) . '/aditya/storage/galeri';
                @unlink(\$publicDir . '/' . basename(\$galeri->gambar));
            }

            \$file->move(\$storageDir, \$filename);
            \$data['gambar'] = 'galeri/' . \$filename;

            \$publicDir = dirname(dirname(app()->publicPath())) . '/aditya/storage/galeri';
            if (!is_dir(\$publicDir)) mkdir(\$publicDir, 0755, true);
            @copy(\$storageDir . '/' . \$filename, \$publicDir . '/' . \$filename);
        }

        \$isCarousel = \$request->has('is_carousel');
        if (\$isCarousel && !\$galeri->is_carousel && Galeri::where('is_carousel', true)->count() >= 6) {
            Galeri::where('is_carousel', true)->where('id', '!=', \$galeri->id)->orderBy('updated_at', 'asc')->first()->update(['is_carousel' => false]);
        }
        \$data['is_carousel'] = \$isCarousel;

        \$galeri->update(\$data);

        return redirect()->route('admin.galeri.index')->with('success', 'Galeri berhasil diperbarui');
    }

    public function destroy(Galeri \$galeri)
    {
        if (\$galeri->gambar) {
            \$storageDir = storage_path('app/public/galeri');
            \$publicDir  = dirname(dirname(app()->publicPath())) . '/aditya/storage/galeri';
            @unlink(\$storageDir . '/' . basename(\$galeri->gambar));
            @unlink(\$publicDir  . '/' . basename(\$galeri->gambar));
        }
        \$galeri->delete();

        return redirect()->route('admin.galeri.index')->with('success', 'Galeri berhasil dihapus');
    }
}
PHP;

file_put_contents($file, $newController);
echo "✅ GaleriController.php berhasil ditulis ulang<br>";

// Verifikasi path sync
$publicDir = dirname(dirname(dirname(__DIR__))) . '/aditya/storage/galeri';
echo "<br>=== VERIFIKASI PATH ===<br>";
echo "aditya/storage/galeri: " . htmlspecialchars($publicDir) . "<br>";
echo "Exists: " . (is_dir($publicDir) ? '✅ YA' : '❌ TIDAK — akan dibuat otomatis saat upload') . "<br>";

echo "<br>✅ Selesai! Coba upload foto baru dari admin panel.";
