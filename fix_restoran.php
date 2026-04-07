<?php
// Upload ke public_html/aditya/ akses via browser SEKALI SAJA

$restoranPath   = __DIR__ . '/restoran';
$controllerFile = $restoranPath . '/app/Http/Controllers/GaleriController.php';
$destDir        = __DIR__ . '/storage/galeri';

echo "<pre>";
echo "restoranPath   = $restoranPath\n";
echo "controllerFile = $controllerFile\n";
echo "destDir        = $destDir\n";
echo "restoran exists: " . (is_dir($restoranPath) ? '✅ YA' : '❌ TIDAK') . "\n";
echo "controller exists: " . (file_exists($controllerFile) ? '✅ YA' : '❌ TIDAK') . "\n\n";

if (!is_dir($restoranPath)) {
    echo "❌ Folder restoran tidak ditemukan di: $restoranPath\n";
    echo "Isi __DIR__:\n";
    foreach (scandir(__DIR__) as $item) {
        if (!in_array($item, ['.', '..'])) echo "  $item\n";
    }
    exit;
}

// Buat folder galeri jika belum ada
if (!is_dir($destDir)) mkdir($destDir, 0755, true);

// Tulis GaleriController
file_put_contents($controllerFile, '<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    private function storageDir(): string
    {
        return dirname(app()->publicPath()) . \'/storage/galeri\';
    }

    public function public()
    {
        $galeris = Galeri::where(\'status\', true)->get();
        return view(\'galeri\', compact(\'galeris\'));
    }

    public function index()
    {
        $galeris = Galeri::all();
        return view(\'admin.galeri.index\', compact(\'galeris\'));
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

        $dir = $this->storageDir();
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $file     = $request->file(\'gambar\');
        $filename = time() . \'_\' . $file->getClientOriginalName();
        $file->move($dir, $filename);

        $isCarousel = $request->has(\'is_carousel\');
        if ($isCarousel && Galeri::where(\'is_carousel\', true)->count() >= 6) {
            Galeri::where(\'is_carousel\', true)->orderBy(\'updated_at\', \'asc\')->first()->update([\'is_carousel\' => false]);
        }

        Galeri::create([
            \'gambar\'      => \'galeri/\' . $filename,
            \'status\'      => $request->has(\'status\'),
            \'is_carousel\' => $isCarousel,
        ]);

        return redirect()->route(\'admin.galeri.index\')->with(\'success\', \'Galeri berhasil ditambahkan\');
    }

    public function edit(Galeri $galeri)
    {
        return view(\'admin.galeri.edit\', compact(\'galeri\'));
    }

    public function update(Request $request, Galeri $galeri)
    {
        $request->validate([
            \'gambar\' => \'nullable|image|mimes:jpeg,png,jpg,gif|max:5120\',
        ]);

        $data = [
            \'status\'      => $request->has(\'status\'),
            \'is_carousel\' => $request->has(\'is_carousel\'),
        ];

        if ($request->hasFile(\'gambar\')) {
            $dir = $this->storageDir();
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            if ($galeri->gambar) @unlink($dir . \'/\' . basename($galeri->gambar));
            $file     = $request->file(\'gambar\');
            $filename = time() . \'_\' . $file->getClientOriginalName();
            $file->move($dir, $filename);
            $data[\'gambar\'] = \'galeri/\' . $filename;
        }

        $galeri->update($data);
        return redirect()->route(\'admin.galeri.index\')->with(\'success\', \'Galeri berhasil diperbarui\');
    }

    public function destroy(Galeri $galeri)
    {
        if ($galeri->gambar) {
            @unlink($this->storageDir() . \'/\' . basename($galeri->gambar));
        }
        $galeri->delete();
        return redirect()->route(\'admin.galeri.index\')->with(\'success\', \'Galeri berhasil dihapus\');
    }
}
');

echo "✅ GaleriController.php ditulis ulang\n\n";

// Clear cache
echo "=== CLEAR CACHE ===\n";
echo shell_exec("cd $restoranPath && php artisan cache:clear 2>&1") . "\n";
echo shell_exec("cd $restoranPath && php artisan config:clear 2>&1") . "\n";
echo shell_exec("cd $restoranPath && php artisan view:clear 2>&1") . "\n";
echo shell_exec("cd $restoranPath && php artisan route:clear 2>&1") . "\n";

// Verifikasi
echo "\n=== VERIFIKASI ===\n";
echo "controller ditulis: " . (file_exists($controllerFile) ? '✅ YA' : '❌ TIDAK') . "\n";
echo "storage/galeri ada: " . (is_dir($destDir) ? '✅ YA' : '❌ TIDAK') . "\n";
echo "\n✅ SELESAI — Coba upload foto baru dari admin/galeri/create\n";
echo "</pre>";
