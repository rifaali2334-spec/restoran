<?php
$base = dirname(__DIR__);

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Cek Route Galeri</title>";
echo "<style>body{font-family:sans-serif;max-width:800px;margin:30px auto;padding:0 20px}
pre{background:#f4f4f4;padding:10px;font-size:12px;overflow-x:auto}</style></head><body>";

echo "<h2>Cek Route & Controller Galeri</h2>";

// 1. Cek routes/web.php — cari galeri
echo "<h3>1. Routes yang berhubungan dengan galeri:</h3>";
$routeFile = $base . '/abi/routes/web.php';
if (file_exists($routeFile)) {
    $isi = file_get_contents($routeFile);
    $lines = explode("\n", $isi);
    foreach ($lines as $i => $line) {
        if (stripos($line, 'galeri') !== false) {
            echo "<pre>" . htmlspecialchars($line) . "</pre>";
        }
    }
}

// 2. Cari semua controller yang ada kata galeri
echo "<h3>2. Semua controller yang berhubungan galeri:</h3>";
$ctrlPath = $base . '/abi/app/Http/Controllers';
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($ctrlPath));
foreach ($rii as $file) {
    if ($file->isFile() && stripos($file->getFilename(), 'galeri') !== false) {
        echo "<p><b>" . str_replace($ctrlPath, '', $file->getPathname()) . "</b></p>";
        echo "<pre>" . htmlspecialchars(file_get_contents($file->getPathname())) . "</pre>";
    }
}

echo "</body></html>";
