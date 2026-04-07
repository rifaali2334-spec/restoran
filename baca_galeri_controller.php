<?php
// Upload ke public_html/aditya/ lalu akses via browser

$abiPath = dirname(__DIR__) . '/abi';
$file = $abiPath . '/app/Http/Controllers/GaleriController.php';

echo "<pre>";
echo "Path: " . htmlspecialchars($file) . "\n";
echo "Exists: " . (file_exists($file) ? 'YA' : 'TIDAK') . "\n\n";

if (file_exists($file)) {
    $lines = file($file);
    foreach ($lines as $i => $line) {
        echo str_pad($i+1, 4, ' ', STR_PAD_LEFT) . ": " . htmlspecialchars($line);
    }
}
echo "</pre>";
