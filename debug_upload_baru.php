<?php
// Upload ke public_html/aditya/ lalu akses via browser
$base = dirname(__DIR__);
$laravelStorage = $base . '/abi/storage/app/public/galeri';
$publicStorage = __DIR__ . '/storage/galeri';

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Debug Upload Baru</title>";
echo "<style>body{font-family:sans-serif;max-width:800px;margin:30px auto;padding:0 20px}
.ok{color:green}.err{color:red}.warn{color:orange}</style></head><body>";

echo "<h2>Debug Foto Baru</h2>";

// 1. File terbaru di storage/app/public/galeri
echo "<h3>1. File terbaru di storage/app/public/galeri (Laravel):</h3>";
if (is_dir($laravelStorage)) {
    $files = glob($laravelStorage . '/*');
    usort($files, fn($a,$b) => filemtime($b) - filemtime($a));
    echo "<p>Total: " . count($files) . " file</p>";
    foreach (array_slice($files, 0, 5) as $f) {
        echo "<p class='ok'>✓ " . basename($f) . " — " . date('d/m/Y H:i:s', filemtime($f)) . "</p>";
    }
} else {
    echo "<p class='err'>✗ Folder tidak ada</p>";
}

// 2. File terbaru di aditya/storage/galeri
echo "<h3>2. File terbaru di aditya/storage/galeri (Public):</h3>";
if (is_dir($publicStorage)) {
    $files = glob($publicStorage . '/*');
    usort($files, fn($a,$b) => filemtime($b) - filemtime($a));
    echo "<p>Total: " . count($files) . " file</p>";
    foreach (array_slice($files, 0, 5) as $f) {
        echo "<p class='ok'>✓ " . basename($f) . " — " . date('d/m/Y H:i:s', filemtime($f)) . "</p>";
    }
} else {
    echo "<p class='err'>✗ Folder tidak ada</p>";
}

// 3. Cek file yang ada di Laravel tapi tidak ada di public
echo "<h3>3. File yang ada di Laravel tapi TIDAK ada di public:</h3>";
if (is_dir($laravelStorage) && is_dir($publicStorage)) {
    $srcFiles = array_map('basename', glob($laravelStorage . '/*'));
    $dstFiles = array_map('basename', glob($publicStorage . '/*'));
    $missing = array_diff($srcFiles, $dstFiles);
    if (empty($missing)) {
        echo "<p class='ok'>✓ Semua file sudah tersync</p>";
    } else {
        echo "<p class='err'>✗ " . count($missing) . " file belum tersync:</p>";
        foreach ($missing as $f) {
            echo "<p class='err'>- $f</p>";
        }
    }
}

// 4. Cek GaleriController apakah sync sudah terpasang
echo "<h3>4. GaleriController — status sync:</h3>";
$ctrlPath = $base . '/abi/app/Http/Controllers/GaleriController.php';
if (file_exists($ctrlPath)) {
    $isi = file_get_contents($ctrlPath);
    $adaSync = strpos($isi, 'syncFotoKePublic') !== false;
    echo "<p class='" . ($adaSync ? 'ok' : 'err') . "'>" . ($adaSync ? '✓ syncFotoKePublic ada' : '✗ syncFotoKePublic TIDAK ADA') . "</p>";
    // Tampilkan bagian store() saja
    preg_match('/public function store.*?(?=public function)/s', $isi, $match);
    if ($match) {
        echo "<pre style='background:#f4f4f4;padding:10px;font-size:12px'>" . htmlspecialchars($match[0]) . "</pre>";
    }
}

// 5. Manual sync sekarang
echo "<h3>5. Manual sync sekarang:</h3>";
if (isset($_POST['sync'])) {
    $src = $base . '/abi/storage/app/public';
    $dst = __DIR__ . '/storage';
    $count = 0;
    
    function copyDir($src, $dst) {
        global $count;
        if (!is_dir($dst)) mkdir($dst, 0755, true);
        foreach (scandir($src) as $item) {
            if ($item === '.' || $item === '..') continue;
            $s = $src . '/' . $item;
            $d = $dst . '/' . $item;
            if (is_dir($s)) copyDir($s, $d);
            elseif (!file_exists($d) || filemtime($s) > filemtime($d)) {
                if (copy($s, $d)) $count++;
            }
        }
    }
    copyDir($src, $dst);
    echo "<p class='ok'>✓ Sync selesai — $count file di-copy</p>";
} else {
    echo "<form method='POST'><button type='submit' name='sync' style='padding:8px 16px;background:#2563eb;color:white;border:none;border-radius:4px;cursor:pointer'>Sync Manual Sekarang</button></form>";
}

echo "</body></html>";
