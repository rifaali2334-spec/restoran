<?php
// Upload ke public_html/aditya/ akses SEKALI via browser

$abiPath        = dirname(__DIR__) . '/abi';
$controllerFile = $abiPath . '/app/Http/Controllers/GaleriController.php';
$destDir        = __DIR__ . '/storage/galleries';

// Buat folder jika belum ada
if (!is_dir($destDir)) mkdir($destDir, 0755, true);

// Tulis GaleriController
file_put_contents($controllerFile, '<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    private function storageDir(): string
    {
        return dirname(app()->publicPath()) . \'/storage/galleries\';
    }

    public function public()
    {
        $featuredGalleries = Gallery::active()->carousel()->ordered()->take(5)->get();
        $galleries         = Gallery::active()->ordered()->paginate(12);
        return view(\'galeri\', compact(\'featuredGalleries\', \'galleries\'));
    }

    public function index()
    {
        $galleries = Gallery::ordered()->get();
        return view(\'admin.galeri.index\', compact(\'galleries\'));
    }

    public function create()
    {
        return view(\'admin.galeri.create\');
    }

    public function store(Request $request)
    {
        $request->validate([
            \'gambar\' => \'required|image|mimes:jpeg,png,jpg,gif|max:5120\',
        ]);

        $dir      = $this->storageDir();
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $file     = $request->file(\'gambar\');
        $filename = time() . \'_\' . $file->getClientOriginalName();
        $file->move($dir, $filename);

        $isCarousel = $request->has(\'is_carousel\');
        if ($isCarousel && Gallery::where(\'is_carousel\', true)->count() >= 6) {
            Gallery::where(\'is_carousel\', true)->orderBy(\'updated_at\', \'asc\')->first()->update([\'is_carousel\' => false]);
        }

        Gallery::create([
            \'image_url\'     => \'galleries/\' . $filename,
            \'status\'        => $request->has(\'status\') ? \'active\' : \'inactive\',
            \'is_carousel\'   => $isCarousel,
            \'display_order\' => $request->input(\'display_order\', 0),
        ]);

        return redirect()->route(\'admin.galeri.index\')->with(\'success\', \'Galeri berhasil ditambahkan\');
    }

    public function edit(Gallery $galeri)
    {
        return view(\'admin.galeri.edit\', compact(\'galeri\'));
    }

    public function update(Request $request, Gallery $galeri)
    {
        $request->validate([
            \'gambar\' => \'nullable|image|mimes:jpeg,png,jpg,gif|max:5120\',
        ]);

        $data = [
            \'status\'        => $request->has(\'status\') ? \'active\' : \'inactive\',
            \'is_carousel\'   => $request->has(\'is_carousel\'),
            \'display_order\' => $request->input(\'display_order\', 0),
        ];

        if ($request->hasFile(\'gambar\')) {
            $dir      = $this->storageDir();
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            if ($galeri->image_url) @unlink($dir . \'/\' . basename($galeri->image_url));

            $file     = $request->file(\'gambar\');
            $filename = time() . \'_\' . $file->getClientOriginalName();
            $file->move($dir, $filename);
            $data[\'image_url\'] = \'galleries/\' . $filename;
        }

        $galeri->update($data);
        return redirect()->route(\'admin.galeri.index\')->with(\'success\', \'Galeri berhasil diperbarui\');
    }

    public function destroy(Gallery $galeri)
    {
        if ($galeri->image_url) {
            @unlink($this->storageDir() . \'/\' . basename($galeri->image_url));
        }
        $galeri->delete();
        return redirect()->route(\'admin.galeri.index\')->with(\'success\', \'Galeri berhasil dihapus\');
    }
}
');

echo "<pre>";
echo "✅ GaleriController.php ditulis ulang\n";
echo "✅ Folder galleries: " . (is_dir($destDir) ? 'ADA' : 'TIDAK') . "\n\n";

// Clear cache
echo shell_exec("cd $abiPath && php artisan cache:clear 2>&1");
echo shell_exec("cd $abiPath && php artisan config:clear 2>&1");
echo shell_exec("cd $abiPath && php artisan view:clear 2>&1");
echo shell_exec("cd $abiPath && php artisan route:clear 2>&1");

echo "\n✅ SELESAI — Coba upload foto baru dari admin/galeri/create\n";
echo "</pre>";
