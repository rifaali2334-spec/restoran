<?php
$base = dirname(__DIR__);
$laravelRoot = $base . '/abi';

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Cek Routes Tersembunyi</title>";
echo "<style>body{font-family:sans-serif;max-width:900px;margin:30px auto;padding:0 20px}
pre{background:#f4f4f4;padding:10px;font-size:12px;overflow-x:auto}</style></head><body>";

// 1. Cek bootstrap/app.php
echo "<h3>bootstrap/app.php:</h3>";
$f = $laravelRoot . '/bootstrap/app.php';
if (file_exists($f)) echo "<pre>" . htmlspecialchars(file_get_contents($f)) . "</pre>";

// 2. Cek AppServiceProvider
echo "<h3>AppServiceProvider.php:</h3>";
$f = $laravelRoot . '/app/Providers/AppServiceProvider.php';
if (file_exists($f)) echo "<pre>" . htmlspecialchars(file_get_contents($f)) . "</pre>";

// 3. Cari semua file yang ada kata 'galeri' di folder routes dan providers
echo "<h3>Semua Provider:</h3>";
foreach (glob($laravelRoot . '/app/Providers/*.php') as $f) {
    echo "<h4>" . basename($f) . ":</h4>";
    echo "<pre>" . htmlspecialchars(file_get_contents($f)) . "</pre>";
}

// 4. Cek admin views sidebar — cari link galeri
echo "<h3>Sidebar admin (cari link galeri):</h3>";
$viewFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($laravelRoot . '/resources/views'));
foreach ($viewFiles as $file) {
    if (!$file->isFile()) continue;
    $isi = file_get_contents($file->getPathname());
    if (stripos($isi, 'admin/galeri') !== false || stripos($isi, 'admin.galeri') !== false) {
        echo "<h4>" . str_replace($laravelRoot, '', $file->getPathname()) . ":</h4>";
        preg_match_all('/.*galeri.*/i', $isi, $matches);
        echo "<pre>" . htmlspecialchars(implode("\n", $matches[0])) . "</pre>";
    }
}

echo "</body></html>";
