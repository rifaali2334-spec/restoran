<?php
$base = dirname(__DIR__);

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Cek Routes</title>";
echo "<style>body{font-family:sans-serif;max-width:900px;margin:30px auto;padding:0 20px}
pre{background:#f4f4f4;padding:10px;font-size:12px;overflow-x:auto}</style></head><body>";

echo "<h2>Semua File Routes</h2>";

// Tampilkan semua file di folder routes
$routesDir = $base . '/abi/routes';
foreach (scandir($routesDir) as $file) {
    if ($file === '.' || $file === '..') continue;
    echo "<h3>routes/$file:</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents($routesDir . '/' . $file)) . "</pre>";
}

echo "</body></html>";
