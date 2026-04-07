<?php
$base = dirname(__DIR__);
$laravelStorage = $base . '/abi/storage/app/public';

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Cek Folder Foto</title>";
echo "<style>body{font-family:sans-serif;max-width:800px;margin:30px auto;padding:0 20px}
.ok{color:green}.err{color:red}.warn{color:orange}
pre{background:#f4f4f4;padding:10px;font-size:12px;overflow-x:auto}</style></head><body>";

echo "<h2>Cek Folder Foto Baru</h2>";

// 1. Semua folder di storage/app/public beserta file terbaru
echo "<h3>1. Semua folder di storage/app/public:</h3>";
if (is_dir($laravelStorage)) {
    $folders = glob($laravelStorage . '/*', GLOB_ONLYDIR);
    foreach ($folders as $folder) {
        $files = glob($folder . '/*');
        usort($files, fn($a,$b) => filemtime($b) - filemtime($a));
        $latest = !empty($files) ? basename($files[0]) . ' — ' . date('d/m/Y H:i:s', filemtime($files[0])) : 'kosong';
        echo "<p class='ok'>✓ " . basename($folder) . "/ (" . count($files) . " file) — terbaru: $latest</p>";
    }
} else {
    echo "<p class='err'>✗ Folder storage/app/public tidak ada!</p>";
    // Cek storage/app saja
    $storageApp = $base . '/abi/storage/app';
    if (is_dir($storageApp)) {
        echo "<p class='warn'>Isi storage/app/:</p>";
        foreach (glob($storageApp . '/*') as $f) {
            echo "<p>- " . basename($f) . (is_dir($f) ? '/' : '') . "</p>";
        }
    }
}

// 2. Tampilkan isi GaleriController yang sekarang
echo "<h3>2. Isi GaleriController.php saat ini:</h3>";
$ctrlPath = $base . '/abi/app/Http/Controllers/GaleriController.php';
if (file_exists($ctrlPath)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($ctrlPath)) . "</pre>";
} else {
    echo "<p class='err'>✗ Tidak ditemukan</p>";
}

echo "</body></html>";
