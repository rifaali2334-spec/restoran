<?php
// Upload ke public_html/aditya/ lalu akses via browser
// Setelah selesai HAPUS file ini

$base = dirname(__DIR__);
$laravelRoot = $base . '/abi';
$controllerBase = $laravelRoot . '/app/Http/Controllers';
$baseController = $controllerBase . '/Controller.php';

$adminControllers = [
    $controllerBase . '/Admin/GalleryController.php',
    $controllerBase . '/Admin/NewsController.php',
    $controllerBase . '/Admin/FoodItemController.php',
    $controllerBase . '/Admin/ContentController.php',
];

$methodSync = '
    protected function syncFotoKePublic(): void
    {
        $source = storage_path(\'app/public\');
        $target = $this->detectPublicStorage();
        if (!$target || !is_dir($source)) return;
        $this->copyRekursif($source, $target);
    }

    private function detectPublicStorage(): ?string
    {
        $publicPath = public_path();
        $candidates = [
            $publicPath . \'/storage\',
            dirname($publicPath) . \'/aditya/storage\',
            dirname($publicPath) . \'/public/storage\',
        ];
        foreach ($candidates as $path) {
            if (is_dir($path)) return $path;
        }
        @mkdir($publicPath . \'/storage\', 0755, true);
        return $publicPath . \'/storage\';
    }

    private function copyRekursif(string $src, string $dst): void
    {
        if (!is_dir($dst)) @mkdir($dst, 0755, true);
        foreach (scandir($src) as $item) {
            if ($item === \'.\' || $item === \'..\') continue;
            $s = $src . \'/\' . $item;
            $d = $dst . \'/\' . $item;
            if (is_dir($s)) {
                $this->copyRekursif($s, $d);
            } elseif (!file_exists($d) || filemtime($s) > filemtime($d)) {
                @copy($s, $d);
            }
        }
    }
';

$preview = isset($_GET['preview']);
$install = isset($_POST['install']);

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Install Sync Foto</title>";
echo "<style>body{font-family:sans-serif;max-width:800px;margin:30px auto;padding:0 20px}
.ok{color:green}.err{color:red}.warn{color:orange}
pre{background:#f4f4f4;padding:10px;border-radius:5px;overflow-x:auto}
button{padding:10px 20px;background:#2563eb;color:white;border:none;border-radius:5px;cursor:pointer;font-size:15px;margin-top:15px}
button.red{background:#dc2626}</style></head><body>";

echo "<h2>Install Sync Foto ke Controller.php</h2>";

// --- CEK STATUS ---
echo "<h3>Status:</h3>";

// Cek Controller.php
if (!file_exists($baseController)) {
    echo "<p class='err'>✗ Controller.php tidak ditemukan di: $baseController</p></body></html>";
    exit;
}

$isiBase = file_get_contents($baseController);
$sudahAdaSync = strpos($isiBase, 'syncFotoKePublic') !== false;
echo "<p class='" . ($sudahAdaSync ? 'ok' : 'warn') . "'>" . ($sudahAdaSync ? '✓' : '○') . " Controller.php: " . ($sudahAdaSync ? 'Method sync sudah ada' : 'Belum ada method sync') . "</p>";

// Cek 4 controller
$controllerNames = ['GalleryController', 'NewsController', 'FoodItemController', 'ContentController'];
$perluPatch = [];
foreach ($adminControllers as $i => $path) {
    $nama = $controllerNames[$i];
    if (!file_exists($path)) {
        echo "<p class='warn'>○ $nama.php: tidak ditemukan</p>";
        continue;
    }
    $isi = file_get_contents($path);
    $adaSync = strpos($isi, 'syncFotoKePublic') !== false;
    echo "<p class='" . ($adaSync ? 'ok' : 'warn') . "'>" . ($adaSync ? '✓' : '○') . " $nama.php: " . ($adaSync ? 'Sudah ada panggilan sync' : 'Belum ada panggilan sync') . "</p>";
    if (!$adaSync) $perluPatch[] = $i;
}

if ($sudahAdaSync && empty($perluPatch)) {
    echo "<p class='ok'><b>✓ Semua sudah terpasang! Tidak perlu install ulang.</b></p>";
    echo "</body></html>";
    exit;
}

// --- INSTALL ---
if ($install) {
    echo "<h3>Proses Install:</h3>";
    $berhasil = true;

    // 1. Patch Controller.php — tambah method sebelum closing brace terakhir
    if (!$sudahAdaSync) {
        $backup = $baseController . '.bak';
        copy($baseController, $backup);
        $baru = preg_replace('/}\s*$/', $methodSync . "\n}", $isiBase);
        if (file_put_contents($baseController, $baru)) {
            echo "<p class='ok'>✓ Controller.php — method sync ditambahkan (backup: Controller.php.bak)</p>";
        } else {
            echo "<p class='err'>✗ Gagal menulis Controller.php — cek permission</p>";
            $berhasil = false;
        }
    }

    // 2. Patch 4 controller — tambah $this->syncFotoKePublic() setelah ->store(
    foreach ($perluPatch as $i) {
        $path = $adminControllers[$i];
        $nama = $controllerNames[$i];
        $isi = file_get_contents($path);
        $backup = $path . '.bak';
        copy($path, $backup);

        // Tambah setelah baris yang mengandung ->store(
        $baru = preg_replace(
            "/(->store\([^)]+\);)/",
            "$1\n        \$this->syncFotoKePublic();",
            $isi
        );

        if ($baru === $isi) {
            // Fallback: cari pola Storage::disk('public')->put atau $file->store
            $baru = preg_replace(
                "/(Storage::[^;]+;|\\$file->store[^;]+;)/",
                "$1\n        \$this->syncFotoKePublic();",
                $isi,
                1
            );
        }

        if (file_put_contents($path, $baru)) {
            echo "<p class='ok'>✓ $nama.php — panggilan sync ditambahkan (backup: $nama.php.bak)</p>";
        } else {
            echo "<p class='err'>✗ Gagal menulis $nama.php — cek permission</p>";
            $berhasil = false;
        }
    }

    if ($berhasil) {
        echo "<br><p class='ok'><b>✓ Install selesai! Sekarang setiap upload foto akan otomatis sync.</b></p>";
        echo "<p class='warn'>⚠ Hapus file ini setelah selesai!</p>";
    }

} else {
    // Tampilkan tombol install
    echo "<br><form method='POST'>";
    echo "<button type='submit' name='install'>Install Sekarang</button>";
    echo "</form>";
}

echo "</body></html>";
