<?php
$base = dirname(__DIR__);

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Cek Routes Admin</title>";
echo "<style>body{font-family:sans-serif;max-width:900px;margin:30px auto;padding:0 20px}
pre{background:#f4f4f4;padding:10px;font-size:12px;overflow-x:auto}</style></head><body>";

echo "<h2>Isi Lengkap routes/web.php</h2>";
$routeFile = $base . '/abi/routes/web.php';
if (file_exists($routeFile)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($routeFile)) . "</pre>";
} else {
    echo "<p style='color:red'>✗ Tidak ditemukan</p>";
}

echo "<h2>Semua Controller di folder Admin:</h2>";
$adminCtrl = $base . '/abi/app/Http/Controllers/Admin';
if (is_dir($adminCtrl)) {
    foreach (glob($adminCtrl . '/*.php') as $f) {
        echo "<h3>" . basename($f) . "</h3>";
        echo "<pre>" . htmlspecialchars(file_get_contents($f)) . "</pre>";
    }
} else {
    echo "<p style='color:red'>✗ Folder Admin tidak ada</p>";
}

echo "</body></html>";
