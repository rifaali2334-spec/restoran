<?php
/**
 * Script untuk cari folder root Laravel yang sebenarnya di hosting
 */

echo "<h2>Cari Folder Root Laravel</h2>";
echo "<hr>";

$currentDir = __DIR__;
echo "<p><strong>Current Directory:</strong> $currentDir</p>";
echo "<hr>";

// Cek parent directories
echo "<h3>🔍 Cek Parent Directories:</h3>";

$checkPaths = [
    $currentDir,
    dirname($currentDir),
    dirname(dirname($currentDir)),
    dirname(dirname(dirname($currentDir))),
];

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Path</th><th>Ada artisan?</th><th>Ada app/?</th><th>Ada config/?</th><th>Ada storage/?</th></tr>";

$laravelRoot = null;

foreach ($checkPaths as $path) {
    $hasArtisan = file_exists($path . '/artisan') ? '✅' : '❌';
    $hasApp = is_dir($path . '/app') ? '✅' : '❌';
    $hasConfig = is_dir($path . '/config') ? '✅' : '❌';
    $hasStorage = is_dir($path . '/storage') ? '✅' : '❌';
    
    echo "<tr>";
    echo "<td><strong>$path</strong></td>";
    echo "<td>$hasArtisan</td>";
    echo "<td>$hasApp</td>";
    echo "<td>$hasConfig</td>";
    echo "<td>$hasStorage</td>";
    echo "</tr>";
    
    if (file_exists($path . '/artisan') && is_dir($path . '/app')) {
        $laravelRoot = $path;
    }
}

echo "</table>";
echo "<hr>";

// List isi current directory
echo "<h3>📁 Isi Current Directory ($currentDir):</h3>";
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

// List isi parent directory
$parentDir = dirname($currentDir);
echo "<h3>📁 Isi Parent Directory ($parentDir):</h3>";
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
    echo "<p style='font-size: 18px;'><strong>$laravelRoot</strong></p>";
    
    // Cek struktur folder
    echo "<p><strong>Struktur folder Laravel:</strong></p>";
    echo "<ul>";
    $checkFolders = ['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes', 'storage', 'vendor'];
    foreach ($checkFolders as $folder) {
        $folderPath = $laravelRoot . '/' . $folder;
        if (is_dir($folderPath)) {
            echo "<li style='color: green;'>✅ $folder/</li>";
        } else {
            echo "<li style='color: red;'>❌ $folder/ (tidak ada)</li>";
        }
    }
    echo "</ul>";
    
    // Cek dimana foto sekarang
    echo "<hr>";
    echo "<h3>📸 Lokasi Foto Sekarang:</h3>";
    
    $photoLocations = [
        'naufal/storage/gallery' => $currentDir . '/storage/gallery',
        'Laravel storage/app/public/gallery' => $laravelRoot . '/storage/app/public/gallery',
        'Laravel public/storage/gallery' => $laravelRoot . '/public/storage/gallery',
    ];
    
    echo "<ul>";
    foreach ($photoLocations as $name => $path) {
        if (is_dir($path)) {
            $fileCount = count(scandir($path)) - 2;
            echo "<li style='color: green;'>✅ <strong>$name</strong>: $fileCount files</li>";
        } else {
            echo "<li style='color: red;'>❌ <strong>$name</strong>: tidak ada</li>";
        }
    }
    echo "</ul>";
    
} else {
    echo "<p style='color: red;'><strong>❌ Laravel Root tidak ditemukan!</strong></p>";
    echo "<p>Kemungkinan:</p>";
    echo "<ul>";
    echo "<li>File Laravel belum lengkap terupload</li>";
    echo "<li>Struktur folder berbeda dengan standar Laravel</li>";
    echo "<li>Folder 'naufal' bukan bagian dari project Laravel</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
echo "<p style='color: red;'><strong>⚠️ Screenshot hasil ini dan kasih tau saya!</strong></p>";
