<?php
$base = dirname(__DIR__);
$storageApp = $base . '/abi/storage/app/public';

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Cek File Terbaru</title>";
echo "<style>body{font-family:sans-serif;max-width:800px;margin:30px auto;padding:0 20px}
.ok{color:green}.err{color:red}.warn{color:orange}</style></head><body>";

echo "<h2>File Terbaru di Semua Folder Storage</h2>";
echo "<p>Waktu server sekarang: <b>" . date('d/m/Y H:i:s') . "</b></p>";

if (!is_dir($storageApp)) {
    echo "<p class='err'>✗ storage/app/public tidak ada!</p>";
    // Cek storage/app
    $storageAppDir = $base . '/abi/storage/app';
    echo "<p>Isi storage/app/:</p>";
    if (is_dir($storageAppDir)) {
        foreach (scandir($storageAppDir) as $item) {
            if ($item === '.' || $item === '..') continue;
            echo "<p>- $item</p>";
        }
    }
} else {
    // Kumpulkan semua file dari semua subfolder
    $allFiles = [];
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($storageApp));
    foreach ($rii as $file) {
        if ($file->isFile()) {
            $allFiles[] = [
                'path' => str_replace($storageApp, '', $file->getPathname()),
                'time' => filemtime($file->getPathname()),
                'size' => $file->getSize()
            ];
        }
    }
    usort($allFiles, fn($a,$b) => $b['time'] - $a['time']);

    echo "<h3>10 File Terbaru:</h3>";
    foreach (array_slice($allFiles, 0, 10) as $f) {
        echo "<p class='ok'>✓ " . $f['path'] . " — " . date('d/m/Y H:i:s', $f['time']) . " (" . round($f['size']/1024) . " KB)</p>";
    }
}

// Cek juga di aditya/storage semua folder
echo "<h3>File Terbaru di aditya/storage (semua folder):</h3>";
$publicStorage = __DIR__ . '/storage';
if (is_dir($publicStorage)) {
    $allFiles = [];
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($publicStorage));
    foreach ($rii as $file) {
        if ($file->isFile()) {
            $allFiles[] = [
                'path' => str_replace($publicStorage, '', $file->getPathname()),
                'time' => filemtime($file->getPathname())
            ];
        }
    }
    usort($allFiles, fn($a,$b) => $b['time'] - $a['time']);
    foreach (array_slice($allFiles, 0, 5) as $f) {
        echo "<p class='ok'>✓ " . $f['path'] . " — " . date('d/m/Y H:i:s', $f['time']) . "</p>";
    }
}

echo "</body></html>";
