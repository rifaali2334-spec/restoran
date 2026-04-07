<?php
// Upload ke public_html/aditya/ lalu akses via browser SEKALI SAJA

$abiPath = dirname(__DIR__) . '/abi';
$file = $abiPath . '/app/Http/Controllers/GaleriController.php';

// Cek model mana yang ada
$hasGaleri  = file_exists($abiPath . '/app/Models/Galeri.php');
$hasGallery = file_exists($abiPath . '/app/Models/Gallery.php');

echo "Model Galeri.php: "  . ($hasGaleri  ? '✅ ADA' : '❌ TIDAK') . "<br>";
echo "Model Gallery.php: " . ($hasGallery ? '✅ ADA' : '❌ TIDAK') . "<br>";

// Cek tabel mana yang ada via DB
$dbPath = $abiPath . '/.env';
$env = file_get_contents($dbPath);
preg_match('/DB_DATABASE=(.+)/', $env, $m);
$dbName = trim($m[1] ?? '');
echo "DB: $dbName<br><br>";

// Cek isi model yang ada
if ($hasGaleri) {
    echo "=== Galeri.php ===<br><pre>";
    echo htmlspecialchars(file_get_contents($abiPath . '/app/Models/Galeri.php'));
    echo "</pre>";
}
if ($hasGallery) {
    echo "=== Gallery.php ===<br><pre>";
    echo htmlspecialchars(file_get_contents($abiPath . '/app/Models/Gallery.php'));
    echo "</pre>";
}

// Cek view admin galeri yang ada
echo "<br>=== VIEW admin/galeri/ ===<br>";
$viewPath = $abiPath . '/resources/views/admin/galeri';
if (is_dir($viewPath)) {
    foreach (glob($viewPath . '/*.blade.php') as $v) {
        echo "✅ " . basename($v) . "<br>";
    }
} else {
    echo "❌ Folder view admin/galeri tidak ada<br>";
}

// Cek view admin/galleries juga
$viewPath2 = $abiPath . '/resources/views/admin/galleries';
if (is_dir($viewPath2)) {
    echo "<br>=== VIEW admin/galleries/ ===<br>";
    foreach (glob($viewPath2 . '/*.blade.php') as $v) {
        echo "✅ " . basename($v) . "<br>";
    }
}
