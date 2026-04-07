<?php
// Upload ke public_html/aditya/ lalu akses via browser

$abiPath = dirname(__DIR__) . '/abi';

echo "<pre>";

// 1. Cek semua path kandidat
echo "=== PATH INFO ===\n";
echo "dirname(__DIR__)          = " . dirname(__DIR__) . "\n";
echo "__DIR__ (script ini)      = " . __DIR__ . "\n";

// Simulasi app()->publicPath() = public_html/aditya
$publicPath = __DIR__;
echo "publicPath (aditya/)      = $publicPath\n";

// Kandidat path aditya/storage/galeri
$candidates = [
    dirname(__DIR__) . '/storage/galeri',                          // public_html/storage/galeri
    __DIR__ . '/storage/galeri',                                    // public_html/aditya/storage/galeri
    dirname(dirname(__DIR__)) . '/aditya/storage/galeri',          // naik 2 level
];

echo "\n=== KANDIDAT PATH SYNC ===\n";
foreach ($candidates as $c) {
    echo htmlspecialchars($c) . " → " . (is_dir($c) ? '✅ ADA' : '❌ TIDAK') . "\n";
}

// 2. Cek isi storage/app/public/galeri (Laravel storage)
echo "\n=== ISI storage/app/public/galeri/ ===\n";
$laravelGaleri = $abiPath . '/storage/app/public/galeri';
if (is_dir($laravelGaleri)) {
    $files = scandir($laravelGaleri);
    $files = array_diff($files, ['.', '..']);
    if (empty($files)) {
        echo "(kosong)\n";
    } else {
        foreach ($files as $f) {
            $size = filesize($laravelGaleri . '/' . $f);
            echo "$f ($size bytes)\n";
        }
    }
} else {
    echo "❌ Folder tidak ada\n";
}

// 3. Cek isi aditya/storage/galeri (public)
echo "\n=== ISI aditya/storage/galeri/ ===\n";
$adityaGaleri = __DIR__ . '/storage/galeri';
if (is_dir($adityaGaleri)) {
    $files = scandir($adityaGaleri);
    $files = array_diff($files, ['.', '..']);
    if (empty($files)) {
        echo "(kosong)\n";
    } else {
        foreach ($files as $f) {
            $size = filesize($adityaGaleri . '/' . $f);
            echo "$f ($size bytes)\n";
        }
    }
} else {
    echo "❌ Folder tidak ada\n";
}

// 4. Cek data di database
echo "\n=== DATA TABEL galeris (5 terbaru) ===\n";
$envFile = file_get_contents($abiPath . '/.env');
preg_match('/DB_HOST=(.+)/', $envFile, $m); $host = trim($m[1]);
preg_match('/DB_DATABASE=(.+)/', $envFile, $m); $db = trim($m[1]);
preg_match('/DB_USERNAME=(.+)/', $envFile, $m); $user = trim($m[1]);
preg_match('/DB_PASSWORD=(.+)/', $envFile, $m); $pass = trim($m[1]);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $rows = $pdo->query("SELECT id, gambar, status, is_carousel, created_at FROM galeris ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "id={$r['id']} gambar={$r['gambar']} status={$r['status']} created={$r['created_at']}\n";
    }
} catch (Exception $e) {
    echo "DB error: " . $e->getMessage() . "\n";
}

// 5. Cek blade view galeri publik - pakai path apa?
echo "\n=== CEK BLADE galeri.blade.php (baris pakai 'gambar') ===\n";
$blade = $abiPath . '/resources/views/galeri.blade.php';
if (file_exists($blade)) {
    $lines = file($blade);
    foreach ($lines as $i => $line) {
        if (stripos($line, 'gambar') !== false || stripos($line, 'storage') !== false || stripos($line, 'asset') !== false) {
            echo "Line " . ($i+1) . ": " . htmlspecialchars(trim($line)) . "\n";
        }
    }
} else {
    echo "❌ galeri.blade.php tidak ada\n";
}

echo "</pre>";
