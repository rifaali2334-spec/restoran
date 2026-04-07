<?php
$abiPath   = dirname(__DIR__) . '/abi';
$gallerDir = __DIR__ . '/storage/galleries';

echo "<pre>";

// DB
$env = file_get_contents($abiPath . '/.env');
preg_match('/DB_HOST=(.+)/',     $env, $m); $host = trim($m[1]);
preg_match('/DB_DATABASE=(.+)/', $env, $m); $db   = trim($m[1]);
preg_match('/DB_USERNAME=(.+)/', $env, $m); $user = trim($m[1]);
preg_match('/DB_PASSWORD=(.+)/', $env, $m); $pass = trim($m[1]);

echo "=== 5 DATA TERBARU galleries ===\n";
try {
    $pdo  = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $rows = $pdo->query("SELECT id, image_url, status, is_carousel, created_at FROM galleries ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "id={$r['id']} | {$r['image_url']} | status={$r['status']} | {$r['created_at']}\n";
    }
} catch (Exception $e) {
    echo "DB error: " . $e->getMessage() . "\n";
}

// File terbaru
echo "\n=== 5 FILE TERBARU di aditya/storage/galleries ===\n";
$files = glob($gallerDir . '/*');
usort($files, fn($a,$b) => filemtime($b) - filemtime($a));
foreach (array_slice($files, 0, 5) as $f) {
    echo basename($f) . " — " . date('d/m/Y H:i:s', filemtime($f)) . "\n";
}

// Cek blade admin galeri index
echo "\n=== BLADE admin/galeri/index.blade.php (baris relevan) ===\n";
$blade = $abiPath . '/resources/views/admin/galeri/index.blade.php';
if (file_exists($blade)) {
    foreach (file($blade) as $i => $line) {
        if (preg_match('/image_url|gambar|storage|asset|src/i', $line)) {
            echo "L".($i+1).": " . htmlspecialchars(trim($line)) . "\n";
        }
    }
} else {
    echo "❌ blade tidak ada\n";
}

// Cek blade galeri publik
echo "\n=== BLADE galeri.blade.php (baris relevan) ===\n";
$blade2 = $abiPath . '/resources/views/galeri.blade.php';
if (file_exists($blade2)) {
    foreach (file($blade2) as $i => $line) {
        if (preg_match('/image_url|gambar|storage|asset|src|featuredGalleries|galleries/i', $line)) {
            echo "L".($i+1).": " . htmlspecialchars(trim($line)) . "\n";
        }
    }
}

echo "</pre>";
