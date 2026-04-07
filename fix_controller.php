<?php
// Upload ke public_html/aditya/ lalu akses via browser
echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Fix Controller</title>";
echo "<style>body{font-family:sans-serif;max-width:800px;margin:30px auto;padding:0 20px}
.ok{color:green}.err{color:red}.warn{color:orange}
pre{background:#f4f4f4;padding:10px;border-radius:5px;overflow-x:auto;font-size:12px}
button{padding:10px 20px;background:#dc2626;color:white;border:none;border-radius:5px;cursor:pointer;font-size:15px;margin-top:15px}</style></head><body>";

$base = dirname(__DIR__);
$laravelRoot = $base . '/abi';
$controllerAdmin = $laravelRoot . '/app/Http/Controllers/Admin';

$controllers = [
    'GalleryController.php',
    'NewsController.php',
    'FoodItemController.php',
    'ContentController.php',
];

echo "<h2>Fix: Hapus 'use AutoSyncFoto' dari Controller</h2>";

if (isset($_POST['fix'])) {
    echo "<h3>Proses:</h3>";
    foreach ($controllers as $ctrl) {
        $path = $controllerAdmin . '/' . $ctrl;
        if (!file_exists($path)) {
            echo "<p class='warn'>○ $ctrl: tidak ditemukan</p>";
            continue;
        }
        $isi = file_get_contents($path);
        if (strpos($isi, 'use AutoSyncFoto') === false) {
            echo "<p class='ok'>✓ $ctrl: tidak ada AutoSyncFoto, skip</p>";
            continue;
        }
        // Backup
        copy($path, $path . '.bak2');
        // Hapus baris "use AutoSyncFoto;"
        $baru = preg_replace('/\n\s*use AutoSyncFoto;\s*/', "\n", $isi);
        file_put_contents($path, $baru);
        echo "<p class='ok'>✓ $ctrl: 'use AutoSyncFoto' dihapus (backup: .bak2)</p>";
    }

    // Cek apakah folder storage ada di aditya/ — kalau tidak ada buat symlink atau folder
    $storagePublic = __DIR__ . '/storage';
    $storageTarget = $base . '/abi/storage/app/public';
    
    echo "<h3>Cek folder storage di aditya/:</h3>";
    if (is_dir($storagePublic)) {
        echo "<p class='ok'>✓ Folder storage sudah ada di aditya/</p>";
    } else {
        // Coba buat symlink
        if (@symlink($storageTarget, $storagePublic)) {
            echo "<p class='ok'>✓ Symlink storage berhasil dibuat!</p>";
        } else {
            echo "<p class='warn'>⚠ Symlink gagal — folder storage tidak ada. Jalankan fix_foto_teman.php untuk copy manual.</p>";
        }
    }

    echo "<br><p class='ok'><b>✓ Selesai! Sekarang coba upload foto baru.</b></p>";
    echo "<p class='warn'>⚠ Hapus file ini setelah selesai!</p>";

} else {
    // Preview
    echo "<h3>Yang akan dilakukan:</h3>";
    echo "<ul>";
    echo "<li>Hapus <code>use AutoSyncFoto;</code> dari 4 controller (penyebab PHP error diam-diam)</li>";
    echo "<li>Method sync tetap ada di Controller.php — tidak dihapus</li>";
    echo "<li>Panggilan <code>\$this->syncFotoKePublic()</code> tetap ada — sync tetap jalan</li>";
    echo "<li>Backup otomatis <code>.bak2</code> sebelum ubah</li>";
    echo "</ul>";

    echo "<h3>Preview controller:</h3>";
    foreach ($controllers as $ctrl) {
        $path = $controllerAdmin . '/' . $ctrl;
        if (file_exists($path)) {
            $isi = file_get_contents($path);
            $ada = strpos($isi, 'use AutoSyncFoto') !== false;
            echo "<p class='" . ($ada ? 'err' : 'ok') . "'>" . ($ada ? '✗ Perlu fix' : '✓ OK') . ": $ctrl</p>";
        }
    }

    echo "<form method='POST'>";
    echo "<button type='submit' name='fix'>Fix Sekarang</button>";
    echo "</form>";
}

echo "</body></html>";
