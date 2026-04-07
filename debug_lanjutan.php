<?php
// Upload ke public_html/aditya/ lalu akses via browser
echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Debug Lanjutan</title>";
echo "<style>body{font-family:sans-serif;max-width:900px;margin:30px auto;padding:0 20px}
.ok{color:green}.err{color:red}.warn{color:orange}
pre{background:#f4f4f4;padding:10px;border-radius:5px;overflow-x:auto;font-size:12px}</style></head><body>";

$base = dirname(__DIR__);
$laravelRoot = $base . '/abi';
$publicRoot = __DIR__; // public_html/aditya

echo "<h2>Debug Lanjutan Foto</h2>";

// 1. Cek isi Controller.php — apakah method sync sudah ada
echo "<h3>1. Controller.php — method sync:</h3>";
$controllerPath = $laravelRoot . '/app/Http/Controllers/Controller.php';
if (file_exists($controllerPath)) {
    $isi = file_get_contents($controllerPath);
    $adaSync = strpos($isi, 'syncFotoKePublic') !== false;
    echo "<p class='" . ($adaSync ? 'ok' : 'err') . "'>" . ($adaSync ? '✓ Ada' : '✗ TIDAK ADA') . " method syncFotoKePublic</p>";
    echo "<pre>" . htmlspecialchars($isi) . "</pre>";
} else {
    echo "<p class='err'>✗ File tidak ditemukan</p>";
}

// 2. Cek salah satu controller admin — apakah ada panggilan sync
echo "<h3>2. GalleryController.php:</h3>";
$galleryPath = $laravelRoot . '/app/Http/Controllers/Admin/GalleryController.php';
if (file_exists($galleryPath)) {
    $isi = file_get_contents($galleryPath);
    $adaSync = strpos($isi, 'syncFotoKePublic') !== false;
    echo "<p class='" . ($adaSync ? 'ok' : 'err') . "'>" . ($adaSync ? '✓ Ada' : '✗ TIDAK ADA') . " panggilan syncFotoKePublic</p>";
    echo "<pre>" . htmlspecialchars($isi) . "</pre>";
}

// 3. Cek permission folder storage di public
echo "<h3>3. Permission folder di aditya/storage:</h3>";
$storagePublic = $publicRoot . '/storage';
if (is_dir($storagePublic)) {
    $perms = substr(sprintf('%o', fileperms($storagePublic)), -4);
    echo "<p>Permission storage/: <b>$perms</b></p>";
    foreach (scandir($storagePublic) as $folder) {
        if ($folder === '.' || $folder === '..') continue;
        $path = $storagePublic . '/' . $folder;
        if (is_dir($path)) {
            $p = substr(sprintf('%o', fileperms($path)), -4);
            $files = glob($path . '/*');
            echo "<p>storage/$folder/ — permission: <b>$p</b> — " . count($files) . " file</p>";
        }
    }
} else {
    echo "<p class='err'>✗ Folder storage tidak ada di aditya/</p>";
}

// 4. Cek .htaccess di aditya/
echo "<h3>4. .htaccess di aditya/:</h3>";
$htaccess = $publicRoot . '/.htaccess';
if (file_exists($htaccess)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($htaccess)) . "</pre>";
} else {
    echo "<p class='warn'>Tidak ada .htaccess</p>";
}

// 5. Cek bagaimana blade menampilkan foto
echo "<h3>5. Cara blade tampilkan foto (sample view):</h3>";
$viewPaths = [
    $laravelRoot . '/resources/views/admin/gallery',
    $laravelRoot . '/resources/views/admin/news',
    $laravelRoot . '/resources/views/gallery',
    $laravelRoot . '/resources/views/news',
];
foreach ($viewPaths as $vp) {
    if (is_dir($vp)) {
        foreach (glob($vp . '/*.blade.php') as $blade) {
            $isi = file_get_contents($blade);
            // Cari baris yang ada img atau asset
            preg_match_all('/.*(?:img|asset|src).*/', $isi, $matches);
            if (!empty($matches[0])) {
                echo "<p><b>" . str_replace($laravelRoot, '', $blade) . ":</b></p>";
                echo "<pre>" . htmlspecialchars(implode("\n", array_slice($matches[0], 0, 5))) . "</pre>";
            }
        }
    }
}

// 6. Cek foto terbaru — ada di storage tapi ada di public storage tidak
echo "<h3>6. Foto terbaru — ada di kedua tempat?</h3>";
$srcStorage = $laravelRoot . '/storage/app/public';
$dstStorage = $publicRoot . '/storage';
$allSrc = [];
if (is_dir($srcStorage)) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($srcStorage));
    foreach ($rii as $f) {
        if ($f->isFile()) {
            $rel = str_replace($srcStorage, '', $f->getPathname());
            $allSrc[$rel] = $f->getPathname();
        }
    }
    arsort($allSrc);
    $sample = array_slice($allSrc, 0, 5, true);
    foreach ($sample as $rel => $srcPath) {
        $dstPath = $dstStorage . $rel;
        $adaDst = file_exists($dstPath);
        echo "<p class='" . ($adaDst ? 'ok' : 'err') . "'>" . ($adaDst ? '✓' : '✗') . " $rel</p>";
    }
}

echo "</body></html>";
