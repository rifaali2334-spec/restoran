<?php
$abiPath      = dirname(__DIR__) . '/abi';
$galeriDir    = __DIR__ . '/storage/galeri';

echo "<pre>";

// 1. Data terbaru di DB
$env = file_get_contents($abiPath . '/.env');
preg_match('/DB_HOST=(.+)/', $env, $m);     $host = trim($m[1]);
preg_match('/DB_DATABASE=(.+)/', $env, $m); $db   = trim($m[1]);
preg_match('/DB_USERNAME=(.+)/', $env, $m); $user = trim($m[1]);
preg_match('/DB_PASSWORD=(.+)/', $env, $m); $pass = trim($m[1]);

echo "=== DB: 5 DATA TERBARU galeris ===\n";
try {
    $pdo  = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $rows = $pdo->query("SELECT id, gambar, status, created_at FROM galeris ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "id={$r['id']} | gambar={$r['gambar']} | status={$r['status']} | {$r['created_at']}\n";
    }
} catch (Exception $e) {
    echo "DB error: " . $e->getMessage() . "\n";
}

// 2. File terbaru di aditya/storage/galeri
echo "\n=== FILE TERBARU di aditya/storage/galeri ===\n";
$files = glob($galeriDir . '/*');
usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
foreach (array_slice($files, 0, 5) as $f) {
    echo basename($f) . " — " . date('d/m/Y H:i:s', filemtime($f)) . "\n";
}

// 3. Isi blade galeri publik
echo "\n=== BLADE galeri.blade.php (baris relevan) ===\n";
$blade = $abiPath . '/resources/views/galeri.blade.php';
if (file_exists($blade)) {
    foreach (file($blade) as $i => $line) {
        if (preg_match('/gambar|storage|asset|src|galeri/i', $line)) {
            echo "L" . ($i+1) . ": " . htmlspecialchars(trim($line)) . "\n";
        }
    }
} else {
    echo "❌ galeri.blade.php tidak ada\n";
    // Cari blade galeri
    foreach (glob($abiPath . '/resources/views/*.blade.php') as $f) {
        echo basename($f) . "\n";
    }
}

echo "</pre>";
