<?php
// Upload ke public_html/aditya/ lalu akses via browser SEKALI SAJA

$abiPath = dirname(__DIR__) . '/abi';
$file = $abiPath . '/app/Http/Controllers/GaleriController.php';

// Buat folder galeri di storage jika belum ada
$galeriStoragePath = $abiPath . '/storage/app/public/galeri';
if (!is_dir($galeriStoragePath)) {
    mkdir($galeriStoragePath, 0755, true);
    echo "✅ Folder storage/app/public/galeri/ dibuat<br>";
} else {
    echo "ℹ️ Folder storage/app/public/galeri/ sudah ada<br>";
}

// Buat folder di public/aditya/storage/galeri juga
$galeriPublicPath = dirname(__DIR__) . '/storage/galeri';
if (!is_dir($galeriPublicPath)) {
    mkdir($galeriPublicPath, 0755, true);
    echo "✅ Folder aditya/storage/galeri/ dibuat<br>";
} else {
    echo "ℹ️ Folder aditya/storage/galeri/ sudah ada<br>";
}

$content = file_get_contents($file);
$original = $content;

// 1. Patch method store() - tambah mkdir + sync setelah storeAs
$oldStore = <<<'PHP'
                $filename = time() . '_' . $file->getClientOriginalName();
                    $gambar = $file->storeAs('galeri', $filename, 'public');
PHP;

$newStore = <<<'PHP'
                $filename = time() . '_' . $file->getClientOriginalName();
                    // Pastikan folder ada
                    $storageDir = storage_path('app/public/galeri');
                    if (!is_dir($storageDir)) mkdir($storageDir, 0755, true);
                    $gambar = $file->storeAs('galeri', $filename, 'public');
                    // Sync ke public
                    $publicDir = public_path('../storage/galeri');
                    if (!is_dir($publicDir)) mkdir($publicDir, 0755, true);
                    @copy($storageDir . '/' . $filename, $publicDir . '/' . $filename);
PHP;

// 2. Patch method update() - tambah mkdir + sync setelah storeAs
$oldUpdate = <<<'PHP'
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $data['gambar'] = $file->storeAs('galeri', $filename, 'public');
PHP;

$newUpdate = <<<'PHP'
                    $filename = time() . '_' . $file->getClientOriginalName();
                    // Pastikan folder ada
                    $storageDir = storage_path('app/public/galeri');
                    if (!is_dir($storageDir)) mkdir($storageDir, 0755, true);
                    $data['gambar'] = $file->storeAs('galeri', $filename, 'public');
                    // Sync ke public
                    $publicDir = public_path('../storage/galeri');
                    if (!is_dir($publicDir)) mkdir($publicDir, 0755, true);
                    @copy($storageDir . '/' . $filename, $publicDir . '/' . $filename);
PHP;

$content = str_replace($oldStore, $newStore, $content);
$content = str_replace($oldUpdate, $newUpdate, $content);

if ($content !== $original) {
    file_put_contents($file, $content);
    echo "✅ GaleriController.php berhasil di-patch<br>";
} else {
    echo "⚠️ Tidak ada perubahan — string tidak cocok, cek manual<br>";
    echo "<br>Cari string ini di controller:<br>";
    echo "<pre>" . htmlspecialchars($oldStore) . "</pre>";
}

// Verifikasi
echo "<br>=== VERIFIKASI ===<br>";
echo "storage/app/public/galeri/ exists: " . (is_dir($galeriStoragePath) ? '✅ YA' : '❌ TIDAK') . "<br>";
echo "aditya/storage/galeri/ exists: " . (is_dir($galeriPublicPath) ? '✅ YA' : '❌ TIDAK') . "<br>";

// Cek apakah public_path('../storage') mengarah ke aditya/storage
$publicBase = public_path('../storage');
echo "public_path('../storage') = $publicBase<br>";
echo "Exists: " . (is_dir($publicBase) ? '✅ YA' : '❌ TIDAK') . "<br>";
