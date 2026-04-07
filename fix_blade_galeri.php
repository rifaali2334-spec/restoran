<?php
$abiPath = dirname(__DIR__) . '/abi';

echo "<pre>";

// 1. Cek form upload - lihat blade admin galeri create
echo "=== CEK BLADE admin/galeri/ ===\n";
$adminGaleriDir = $abiPath . '/resources/views/admin/galeri';
if (is_dir($adminGaleriDir)) {
    foreach (glob($adminGaleriDir . '/*.blade.php') as $f) {
        echo "✅ " . basename($f) . "\n";
    }
} else {
    echo "❌ Folder admin/galeri tidak ada\n";
    // Cek semua folder di admin views
    foreach (glob($abiPath . '/resources/views/admin/*', GLOB_ONLYDIR) as $d) {
        echo "📁 " . basename($d) . "\n";
    }
}

// 2. Fix blade galeri.blade.php - hapus file_exists yang salah
$bladeFile = $abiPath . '/resources/views/galeri.blade.php';
$blade = file_get_contents($bladeFile);
$original = $blade;

// Ganti kondisi file_exists dengan cek sederhana
$blade = str_replace(
    "@if(\$gallery->image_url && file_exists(public_path('storage/' . \$gallery->image_url)))",
    "@if(\$gallery->image_url)",
    $blade
);

if ($blade !== $original) {
    file_put_contents($bladeFile, $blade);
    echo "\n✅ galeri.blade.php dipatch — file_exists dihapus\n";
} else {
    echo "\n⚠️ Patch blade tidak cocok, tampilkan baris L64-L70:\n";
    $lines = file($bladeFile);
    for ($i = 63; $i <= 70 && $i < count($lines); $i++) {
        echo "L".($i+1).": " . htmlspecialchars($lines[$i]);
    }
}

// 3. Cek apakah ada blade admin/galleries (bukan galeri)
echo "\n=== CEK BLADE admin/galleries/ ===\n";
$adminGalleriesDir = $abiPath . '/resources/views/admin/galleries';
if (is_dir($adminGalleriesDir)) {
    foreach (glob($adminGalleriesDir . '/*.blade.php') as $f) {
        echo "✅ " . basename($f) . "\n";
    }
} else {
    echo "❌ Folder admin/galleries tidak ada\n";
}

// 4. Clear view cache
echo "\n=== CLEAR VIEW CACHE ===\n";
echo shell_exec("cd $abiPath && php artisan view:clear 2>&1");

echo "\n✅ Selesai — refresh halaman galeri publik dan cek apakah foto lama muncul\n";
echo "</pre>";
