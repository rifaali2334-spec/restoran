<?php
$restoranPath = __DIR__ . '/restoran';
$galeriDir    = __DIR__ . '/storage/galeri';

echo "<pre>";

// 1. Cek DB dari .env restoran
$env = file_get_contents($restoranPath . '/.env');
preg_match('/DB_HOST=(.+)/',     $env, $m); $host = trim($m[1]);
preg_match('/DB_DATABASE=(.+)/', $env, $m); $db   = trim($m[1]);
preg_match('/DB_USERNAME=(.+)/', $env, $m); $user = trim($m[1]);
preg_match('/DB_PASSWORD=(.+)/', $env, $m); $pass = trim($m[1]);

echo "DB: $db\n\n";

echo "=== 5 DATA TERBARU galeris ===\n";
try {
    $pdo  = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $rows = $pdo->query("SELECT id, gambar, status, created_at FROM galeris ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "id={$r['id']} | {$r['gambar']} | status={$r['status']} | {$r['created_at']}\n";
    }
} catch (Exception $e) {
    echo "DB error: " . $e->getMessage() . "\n";
}

// 2. File terbaru di storage/galeri
echo "\n=== 5 FILE TERBARU di aditya/storage/galeri ===\n";
$files = glob($galeriDir . '/*');
usort($files, fn($a,$b) => filemtime($b) - filemtime($a));
foreach (array_slice($files, 0, 5) as $f) {
    echo basename($f) . " — " . date('d/m/Y H:i:s', filemtime($f)) . "\n";
}

// 3. Cek isi GaleriController yang sekarang
echo "\n=== GaleriController — method store() ===\n";
$ctrl  = file_get_contents($restoranPath . '/app/Http/Controllers/GaleriController.php');
$lines = explode("\n", $ctrl);
$inStore = false;
foreach ($lines as $i => $line) {
    if (strpos($line, 'public function store') !== false) $inStore = true;
    if ($inStore) {
        echo "L".($i+1).": " . htmlspecialchars($line) . "\n";
        if ($inStore && substr_count(implode("\n", array_slice($lines, 0, $i+1)), '{') == substr_count(implode("\n", array_slice($lines, 0, $i+1)), '}') && $i > 10) break;
    }
}

// 4. Cek blade galeri publik
echo "\n=== BLADE galeri.blade.php (baris relevan) ===\n";
$blade = $restoranPath . '/resources/views/galeri.blade.php';
if (file_exists($blade)) {
    foreach (file($blade) as $i => $line) {
        if (preg_match('/gambar|storage|asset|src|status/i', $line)) {
            echo "L".($i+1).": " . htmlspecialchars(trim($line)) . "\n";
        }
    }
} else {
    echo "❌ galeri.blade.php tidak ada\n";
}

echo "</pre>";
