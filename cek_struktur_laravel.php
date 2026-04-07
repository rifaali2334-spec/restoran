<?php
/**
 * Script untuk cari struktur folder Laravel yang benar
 */

echo "<h2>Cari Struktur Folder Laravel</h2>";
echo "<hr>";

$currentDir = __DIR__;
echo "<p><strong>Current Directory:</strong> $currentDir</p>";
echo "<hr>";

// Cek apakah ada file artisan di current directory
echo "<h3>🔍 Cek File Artisan:</h3>";

$possiblePaths = [
    $currentDir . '/artisan',
    $currentDir . '/../artisan',
    $currentDir . '/../../artisan',
    dirname($currentDir) . '/artisan',
    '/home/aff100/domains/baknus.26.cyberwarrior.co.id/artisan',
    '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html/artisan',
];

$artisanFound = false;
$laravelRoot = null;

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        echo "<p style='color: green;'>✅ File artisan ditemukan: <strong>$path</strong></p>";
        $laravelRoot = dirname($path);
        $artisanFound = true;
        break;
    }
}

if (!$artisanFound) {
    echo "<p style='color: red;'>❌ File artisan tidak ditemukan di semua lokasi</p>";
}

echo "<hr>";

// List semua folder di current directory
echo "<h3>📁 Isi Folder Current Directory:</h3>";
echo "<p><strong>$currentDir</strong></p>";
echo "<ul>";

$items = scandir($currentDir);
foreach ($items as $item) {
    if ($item != '.' && $item != '..') {
        $fullPath = $currentDir . '/' . $item;
        $isDir = is_dir($fullPath);
        $icon = $isDir ? '📁' : '📄';
        
        echo "<li>$icon $item";
        
        // Cek apakah ada artisan di dalam folder ini
        if ($isDir && file_exists($fullPath . '/artisan')) {
            echo " <strong style='color: green;'>← LARAVEL ROOT!</strong>";
            $laravelRoot = $fullPath;
        }
        
        echo "</li>";
    }
}
echo "</ul>";

echo "<hr>";

// Cek parent directory
$parentDir = dirname($currentDir);
echo "<h3>📁 Isi Folder Parent Directory:</h3>";
echo "<p><strong>$parentDir</strong></p>";
echo "<ul>";

$items = scandir($parentDir);
foreach ($items as $item) {
    if ($item != '.' && $item != '..') {
        $fullPath = $parentDir . '/' . $item;
        $isDir = is_dir($fullPath);
        $icon = $isDir ? '📁' : '📄';
        
        echo "<li>$icon $item";
        
        // Cek apakah ada artisan di dalam folder ini
        if ($isDir && file_exists($fullPath . '/artisan')) {
            echo " <strong style='color: green;'>← LARAVEL ROOT!</strong>";
            $laravelRoot = $fullPath;
        }
        
        echo "</li>";
    }
}
echo "</ul>";

echo "<hr>";

// Kesimpulan
echo "<h3>📋 Kesimpulan:</h3>";

if ($laravelRoot) {
    echo "<p style='color: green;'><strong>✅ Laravel Root ditemukan:</strong></p>";
    echo "<p><strong>$laravelRoot</strong></p>";
    
    echo "<p><strong>Struktur folder:</strong></p>";
    echo "<ul>";
    
    $checkFolders = ['app', 'config', 'storage', 'public', 'routes', 'database'];
    foreach ($checkFolders as $folder) {
        $folderPath = $laravelRoot . '/' . $folder;
        if (is_dir($folderPath)) {
            echo "<li style='color: green;'>✅ $folder/</li>";
        } else {
            echo "<li style='color: red;'>❌ $folder/ (tidak ada)</li>";
        }
    }
    echo "</ul>";
    
    // Cek dimana folder naufal
    echo "<hr>";
    echo "<h3>📍 Lokasi Folder 'naufal':</h3>";
    
    if (strpos($currentDir, '/naufal') !== false) {
        echo "<p><strong>Current directory ada di dalam 'naufal'</strong></p>";
        echo "<p>Path: $currentDir</p>";
        
        // Cek apakah naufal adalah public folder
        if (file_exists($currentDir . '/index.php')) {
            echo "<p style='color: green;'>✅ Folder 'naufal' adalah PUBLIC FOLDER (ada index.php)</p>";
            echo "<p><strong>Berarti struktur hosting:</strong></p>";
            echo "<pre>";
            echo "Laravel Root: $laravelRoot\n";
            echo "Public Folder: $currentDir (naufal)\n";
            echo "</pre>";
        }
    }
    
} else {
    echo "<p style='color: red;'><strong>❌ Laravel Root tidak ditemukan!</strong></p>";
    echo "<p>Kemungkinan:</p>";
    echo "<ul>";
    echo "<li>File Laravel belum lengkap terupload</li>";
    echo "<li>Struktur folder berbeda dengan standar</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
echo "<p style='color: red;'><strong>⚠️ HAPUS file ini setelah selesai!</strong></p>";
