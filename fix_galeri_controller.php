<?php
// Upload ke public_html/aditya/ lalu akses via browser
// Hapus setelah selesai

$base = dirname(__DIR__);
$path = $base . '/abi/app/Http/Controllers/GaleriController.php';

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Fix GaleriController</title>";
echo "<style>body{font-family:sans-serif;max-width:700px;margin:30px auto;padding:0 20px}
.ok{color:green}.err{color:red}.warn{color:orange}
button{padding:10px 20px;background:#2563eb;color:white;border:none;border-radius:5px;cursor:pointer;font-size:15px;margin-top:15px}</style></head><body>";

echo "<h2>Fix GaleriController</h2>";

if (!file_exists($path)) {
    echo "<p class='err'>✗ GaleriController.php tidak ditemukan di: $path</p></body></html>";
    exit;
}

$isi = file_get_contents($path);
$adaSync = strpos($isi, 'syncFotoKePublic') !== false;

echo "<p>Status: " . ($adaSync ? "<span class='ok'>✓ Sudah ada syncFotoKePublic</span>" : "<span class='warn'>○ Belum ada syncFotoKePublic</span>") . "</p>";

if ($adaSync) {
    echo "<p class='ok'><b>Tidak perlu fix, sudah terpasang.</b></p></body></html>";
    exit;
}

if (isset($_POST['fix'])) {
    // Backup
    copy($path, $path . '.bak');

    // Tambah sync setelah Galeri::create(...)
    $baru = preg_replace(
        '/(Galeri::create\([^;]+\);)/',
        "$1\n            \$this->syncFotoKePublic();",
        $isi
    );

    // Tambah sync setelah $galeri->update($data)
    $baru = preg_replace(
        '/(\$galeri->update\(\$data\);)/',
        "$1\n            \$this->syncFotoKePublic();",
        $baru
    );

    if (file_put_contents($path, $baru)) {
        echo "<p class='ok'><b>✓ Berhasil! syncFotoKePublic ditambahkan ke store() dan update()</b></p>";
        echo "<p>Backup disimpan di GaleriController.php.bak</p>";
        echo "<p class='warn'>⚠ Hapus file ini setelah selesai!</p>";
    } else {
        echo "<p class='err'>✗ Gagal menulis file — cek permission</p>";
    }
} else {
    echo "<p>Yang akan dilakukan:</p>";
    echo "<ul>";
    echo "<li>Tambah <code>\$this->syncFotoKePublic()</code> setelah <code>Galeri::create()</code> di method store()</li>";
    echo "<li>Tambah <code>\$this->syncFotoKePublic()</code> setelah <code>\$galeri->update()</code> di method update()</li>";
    echo "<li>Backup otomatis GaleriController.php.bak</li>";
    echo "</ul>";
    echo "<form method='POST'><button type='submit' name='fix'>Fix Sekarang</button></form>";
}

echo "</body></html>";
