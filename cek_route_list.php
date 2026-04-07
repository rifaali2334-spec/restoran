<?php
// Upload ke public_html/aditya/ lalu akses via browser
// Ini akan jalankan artisan route:list dan tampilkan hasilnya

$abiPath = dirname(__DIR__) . '/abi';

// Cek apakah ini dijalankan dari aditya atau langsung
if (!is_dir($abiPath)) {
    $abiPath = __DIR__;
}

// Jalankan artisan route:list
$output = shell_exec("cd $abiPath && php artisan route:list --path=admin 2>&1");

echo "<pre>";
echo "=== SEMUA ROUTE ADMIN ===\n\n";
echo htmlspecialchars($output);

// Juga cek web.php langsung dengan grep galeri
echo "\n\n=== GREP 'galeri' di web.php ===\n";
$webphp = file_get_contents($abiPath . '/routes/web.php');
$lines = explode("\n", $webphp);
foreach ($lines as $i => $line) {
    if (stripos($line, 'galeri') !== false) {
        echo "Line " . ($i+1) . ": " . htmlspecialchars($line) . "\n";
    }
}

echo "\n\n=== GREP 'galeri' di api.php ===\n";
$apiphp = @file_get_contents($abiPath . '/routes/api.php');
if ($apiphp) {
    $lines = explode("\n", $apiphp);
    foreach ($lines as $i => $line) {
        if (stripos($line, 'galeri') !== false) {
            echo "Line " . ($i+1) . ": " . htmlspecialchars($line) . "\n";
        }
    }
} else {
    echo "(api.php tidak ada atau kosong)\n";
}

echo "\n\n=== SEMUA FILE DI routes/ ===\n";
$routeFiles = glob($abiPath . '/routes/*');
foreach ($routeFiles as $f) {
    echo basename($f) . "\n";
}

echo "</pre>";
