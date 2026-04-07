<?php
// Upload ke public_html/aditya/ lalu akses via browser

$abiPath = dirname(__DIR__) . '/abi';

echo "<pre>";

echo "=== ISI GaleriController.php ===\n\n";
$file = $abiPath . '/app/Http/Controllers/GaleriController.php';
if (file_exists($file)) {
    echo htmlspecialchars(file_get_contents($file));
} else {
    echo "FILE TIDAK ADA: $file\n";
    
    // Cari semua controller
    echo "\n=== SEMUA FILE DI Controllers/ ===\n";
    $files = glob($abiPath . '/app/Http/Controllers/*.php');
    foreach ($files as $f) echo basename($f) . "\n";
    
    echo "\n=== SEMUA FILE DI Controllers/Admin/ ===\n";
    $files = glob($abiPath . '/app/Http/Controllers/Admin/*.php');
    foreach ($files as $f) echo basename($f) . "\n";
}

echo "\n\n=== ISI Model Galeri.php ===\n";
$model = $abiPath . '/app/Models/Galeri.php';
if (file_exists($model)) {
    echo htmlspecialchars(file_get_contents($model));
} else {
    echo "Model Galeri.php tidak ada\n";
    echo "\n=== SEMUA MODEL ===\n";
    foreach (glob($abiPath . '/app/Models/*.php') as $f) echo basename($f) . "\n";
}

echo "</pre>";
