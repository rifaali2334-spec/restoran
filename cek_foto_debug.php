<?php
// Upload ke public_html/aditya/ lalu akses via browser
echo "<h2>Debug Foto</h2>";

$base = dirname(__DIR__); // public_html

// 1. Cek struktur folder storage di public
echo "<h3>1. Folder storage di public (aditya/):</h3>";
$publicStorage = __DIR__ . '/storage';
if (is_dir($publicStorage)) {
    $folders = glob($publicStorage . '/*', GLOB_ONLYDIR);
    echo "Ada " . count($folders) . " subfolder:<br>";
    foreach ($folders as $f) {
        $files = glob($f . '/*');
        echo "- " . basename($f) . " (" . count($files) . " file)<br>";
    }
} else {
    echo "<b style='color:red'>Folder storage TIDAK ADA di public!</b><br>";
}

// 2. Cek storage/app/public di Laravel root
echo "<h3>2. Folder storage/app/public di Laravel (abi/):</h3>";
$laravelStorage = $base . '/abi/storage/app/public';
if (is_dir($laravelStorage)) {
    $folders = glob($laravelStorage . '/*', GLOB_ONLYDIR);
    echo "Ada " . count($folders) . " subfolder:<br>";
    foreach ($folders as $f) {
        $files = glob($f . '/*');
        echo "- " . basename($f) . " (" . count($files) . " file)<br>";
    }
} else {
    echo "<b style='color:red'>Folder tidak ditemukan di path: $laravelStorage</b><br>";
}

// 3. Cek isi controller apakah AutoSyncFoto sudah terpasang
echo "<h3>3. Cek AutoSyncFoto di Controller:</h3>";
$controllerPath = $base . '/abi/app/Http/Controllers/Admin';
$controllers = ['GalleryController.php', 'NewsController.php', 'FoodItemController.php', 'ContentController.php'];
foreach ($controllers as $ctrl) {
    $path = $controllerPath . '/' . $ctrl;
    if (file_exists($path)) {
        $isi = file_get_contents($path);
        $adaTrait = strpos($isi, 'AutoSyncFoto') !== false;
        $adaSync = strpos($isi, 'syncFotoKePublic') !== false;
        $status = ($adaTrait && $adaSync) ? "<span style='color:green'>✓ Terpasang</span>" : "<span style='color:red'>✗ BELUM terpasang</span>";
        echo "$ctrl: $status<br>";
    } else {
        echo "$ctrl: <span style='color:orange'>File tidak ditemukan</span><br>";
    }
}

// 4. Cek trait AutoSyncFoto ada atau tidak
echo "<h3>4. File Trait AutoSyncFoto:</h3>";
$traitPaths = [
    $base . '/abi/app/Traits/AutoSyncFoto.php',
    $base . '/abi/app/Http/Controllers/Admin/AutoSyncFoto.php',
];
foreach ($traitPaths as $tp) {
    if (file_exists($tp)) {
        echo "<span style='color:green'>✓ Ada: $tp</span><br>";
    } else {
        echo "<span style='color:red'>✗ Tidak ada: $tp</span><br>";
    }
}

// 5. Sample foto terbaru di storage
echo "<h3>5. Sample foto terbaru di storage/app/public:</h3>";
$allFiles = [];
if (is_dir($laravelStorage)) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($laravelStorage));
    foreach ($rii as $file) {
        if ($file->isFile()) $allFiles[] = $file->getPathname();
    }
    usort($allFiles, fn($a,$b) => filemtime($b) - filemtime($a));
    echo "5 file terbaru:<br>";
    foreach (array_slice($allFiles, 0, 5) as $f) {
        $rel = str_replace($laravelStorage, '', $f);
        echo "- $rel (modified: " . date('d/m/Y H:i', filemtime($f)) . ")<br>";
    }
}
