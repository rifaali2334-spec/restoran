<?php
$abiPath = dirname(__DIR__) . '/abi';

echo "<pre>";

// 1. Cek model Gallery
echo "=== MODEL Gallery.php ===\n";
$galleryModel = $abiPath . '/app/Models/Gallery.php';
if (file_exists($galleryModel)) {
    echo htmlspecialchars(file_get_contents($galleryModel));
} else {
    echo "❌ Gallery.php tidak ada\n";
}

// 2. Cek tabel yang ada di DB
$env = file_get_contents($abiPath . '/.env');
preg_match('/DB_HOST=(.+)/', $env, $m);     $host = trim($m[1]);
preg_match('/DB_DATABASE=(.+)/', $env, $m); $db   = trim($m[1]);
preg_match('/DB_USERNAME=(.+)/', $env, $m); $user = trim($m[1]);
preg_match('/DB_PASSWORD=(.+)/', $env, $m); $pass = trim($m[1]);

echo "\n=== TABEL DI DATABASE ===\n";
try {
    $pdo  = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $rows = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($rows as $r) echo "- $r\n";

    // Cek struktur tabel galleries
    echo "\n=== STRUKTUR TABEL galleries ===\n";
    $cols = $pdo->query("DESCRIBE galleries")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) echo "{$c['Field']} — {$c['Type']}\n";

    echo "\n=== 5 DATA TERBARU galleries ===\n";
    $rows = $pdo->query("SELECT * FROM galleries ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) print_r($r);
} catch (Exception $e) {
    echo "DB error: " . $e->getMessage() . "\n";
}

echo "</pre>";
