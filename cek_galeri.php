<?php
// Upload ke public_html/aditya/ lalu akses via browser
echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Cek Galeri</title>";
echo "<style>body{font-family:sans-serif;max-width:800px;margin:30px auto;padding:0 20px}
.ok{color:green}.err{color:red}.warn{color:orange}
pre{background:#f4f4f4;padding:10px;border-radius:5px;overflow-x:auto;font-size:12px}</style></head><body>";

$base = dirname(__DIR__);
$laravelRoot = $base . '/abi';
$publicRoot = __DIR__;

echo "<h2>Cek Masalah Galeri</h2>";

// 1. Cek GaleriController
echo "<h3>1. Isi GaleriController.php:</h3>";
$path = $laravelRoot . '/app/Http/Controllers/GaleriController.php';
if (file_exists($path)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($path)) . "</pre>";
} else {
    echo "<p class='warn'>Tidak ditemukan di Controllers root, cek subfolder...</p>";
    // Cari di semua subfolder
    $found = glob($laravelRoot . '/app/Http/Controllers/**/*aleri*.php');
    foreach ($found as $f) {
        echo "<p>Ditemukan: $f</p>";
        echo "<pre>" . htmlspecialchars(file_get_contents($f)) . "</pre>";
    }
}

// 2. Cek folder galeri vs gallery di storage
echo "<h3>2. Folder galeri vs gallery di storage/app/public:</h3>";
$storageApp = $laravelRoot . '/storage/app/public';
foreach (['galeri', 'gallery', 'galleries'] as $folder) {
    $path = $storageApp . '/' . $folder;
    if (is_dir($path)) {
        $files = glob($path . '/*');
        echo "<p class='ok'>✓ $folder/ — " . count($files) . " file</p>";
        // Sample 3 file
        foreach (array_slice($files, 0, 3) as $f) {
            echo "&nbsp;&nbsp;&nbsp;- " . basename($f) . "<br>";
        }
    } else {
        echo "<p class='err'>✗ $folder/ — tidak ada</p>";
    }
}

// 3. Cek folder galeri vs gallery di aditya/storage
echo "<h3>3. Folder galeri vs gallery di aditya/storage:</h3>";
$storagePublic = $publicRoot . '/storage';
foreach (['galeri', 'gallery', 'galleries'] as $folder) {
    $path = $storagePublic . '/' . $folder;
    if (is_dir($path)) {
        $files = glob($path . '/*');
        echo "<p class='ok'>✓ $folder/ — " . count($files) . " file</p>";
    } else {
        echo "<p class='err'>✗ $folder/ — tidak ada</p>";
    }
}

// 4. Cek folder images
echo "<h3>4. Folder images di aditya/:</h3>";
$imagesPath = $publicRoot . '/images';
if (is_dir($imagesPath)) {
    $files = glob($imagesPath . '/*');
    echo "<p class='ok'>✓ Ada — " . count($files) . " file</p>";
    $perms = substr(sprintf('%o', fileperms($imagesPath)), -4);
    echo "<p>Permission: <b>$perms</b></p>";
    // Cek .htaccess di dalam images
    if (file_exists($imagesPath . '/.htaccess')) {
        echo "<p class='err'>Ada .htaccess di dalam images/:</p>";
        echo "<pre>" . htmlspecialchars(file_get_contents($imagesPath . '/.htaccess')) . "</pre>";
    }
    // Sample file
    foreach (array_slice($files, 0, 5) as $f) {
        echo "- " . basename($f) . "<br>";
    }
} else {
    echo "<p class='err'>✗ Tidak ada folder images</p>";
}

// 5. Cek blade galeri — cara tampilkan foto
echo "<h3>5. Blade galeri — cara tampilkan foto:</h3>";
$viewPaths = glob($laravelRoot . '/resources/views/**/*aleri*');
foreach ($viewPaths as $vp) {
    if (is_file($vp)) {
        $isi = file_get_contents($vp);
        preg_match_all('/.*(?:img|asset|src|gambar|image).*/', $isi, $matches);
        if (!empty($matches[0])) {
            echo "<p><b>" . str_replace($laravelRoot, '', $vp) . ":</b></p>";
            echo "<pre>" . htmlspecialchars(implode("\n", array_slice($matches[0], 0, 8))) . "</pre>";
        }
    }
}

echo "</body></html>";
