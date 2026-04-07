<?php
// Upload ke public_html/aditya/ lalu akses via browser

$abiPath = dirname(__DIR__) . '/abi';
$controllerFile = $abiPath . '/app/Http/Controllers/GaleriController.php';

// Deteksi path aditya/storage yang benar
$adityaStorage = __DIR__ . '/storage'; // public_html/aditya/storage

echo "<pre>";
echo "abiPath       = $abiPath\n";
echo "adityaStorage = $adityaStorage\n";
echo "galeri dir    = $adityaStorage/galeri\n";
echo "exists        = " . (is_dir("$adityaStorage/galeri") ? 'YA' : 'TIDAK') . "\n\n";

// Tulis controller baru — pakai path absolut yang sudah terdeteksi
$controller = '<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    private function adityaStoragePath(): string
    {
        // public_html/aditya/storage/galeri
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

        $gambar = null;
        if ($request->hasFile(\'gambar\')) {
            $file     = $request->file(\'gambar\');
            $filename = time() . \'_\' . $file->getClientOriginalName();
            $destDir  = $this->adityaStoragePath();

            if (!is_dir($destDir)) mkdir($destDir, 0755, true);
            $file->move($destDir, $filename);
            $gambar = \'galeri/\' . $filename;
        }

        $isCarousel = $request->has(\'is_carousel\');
        if ($isCarousel && Galeri::where(\'is_carousel\', true)->count() >= 6) {
            Galeri::where(\'is_carousel\', true)->orderBy(\'updated_at\', \'asc\')->first()->update([\'is_carousel\' => false]);
        }

        Galeri::create([
            \'gambar\'      => $gambar,
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
            $file     = $request->file(\'gambar\');
            $filename = time() . \'_\' . $file->getClientOriginalName();
            $destDir  = $this->adityaStoragePath();

            if (!is_dir($destDir)) mkdir($destDir, 0755, true);

            // Hapus foto lama
            if ($galeri->gambar) {
                @unlink($destDir . \'/\' . basename($galeri->gambar));
            }

            $file->move($destDir, $filename);
            $data[\'gambar\'] = \'galeri/\' . $filename;
        }

        $galeri->update($data);

        return redirect()->route(\'admin.galeri.index\')->with(\'success\', \'Galeri berhasil diperbarui\');
    }

    public function destroy(Galeri $galeri)
    {
        if ($galeri->gambar) {
            @unlink($this->adityaStoragePath() . \'/\' . basename($galeri->gambar));
        }
        $galeri->delete();

        return redirect()->route(\'admin.galeri.index\')->with(\'success\', \'Galeri berhasil dihapus\');
    }
}
';

file_put_contents($controllerFile, $controller);
echo "✅ GaleriController.php ditulis\n\n";

// Verifikasi isi controller yang baru ditulis
$written = file_get_contents($controllerFile);
echo "Berisi adityaStoragePath(): " . (strpos($written, 'adityaStoragePath') !== false ? '✅ YA' : '❌ TIDAK') . "\n";
echo "Berisi method store():      " . (strpos($written, 'public function store') !== false ? '✅ YA' : '❌ TIDAK') . "\n";
echo "Berisi method update():     " . (strpos($written, 'public function update') !== false ? '✅ YA' : '❌ TIDAK') . "\n";
echo "Berisi method destroy():    " . (strpos($written, 'public function destroy') !== false ? '✅ YA' : '❌ TIDAK') . "\n\n";

// Test path adityaStoragePath
$testPath = dirname($abiPath . '/public') . '/storage/galeri';
// Simulasi app()->publicPath() = public_html/aditya
$simulatedPublicPath = $adityaStorage . '/../'; // = public_html/aditya
$simulatedResult = realpath(dirname(realpath($adityaStorage . '/..')) . '/storage/galeri');
echo "app()->publicPath() akan return: " . realpath($abiPath . '/../aditya') . "\n";
echo "adityaStoragePath() akan return: " . realpath($abiPath . '/../aditya') . "/storage/galeri\n";
echo "Folder ini exists: " . (is_dir("$adityaStorage/galeri") ? '✅ YA' : '❌ TIDAK') . "\n\n";

// Clear Laravel cache
echo "=== CLEAR CACHE ===\n";
echo shell_exec("cd $abiPath && php artisan cache:clear 2>&1") . "\n";
echo shell_exec("cd $abiPath && php artisan config:clear 2>&1") . "\n";
echo shell_exec("cd $abiPath && php artisan route:clear 2>&1") . "\n";
echo shell_exec("cd $abiPath && php artisan view:clear 2>&1") . "\n";

echo "\n✅ SELESAI — Coba upload foto baru dari admin/galeri/create\n";
echo "</pre>";
