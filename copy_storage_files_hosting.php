<?php
/**
 * Script ALTERNATIF jika hosting tidak support symbolic link
 * Script ini akan COPY semua file dari storage/app/public ke public/storage
 * 
 * CATATAN: Setiap kali admin upload foto baru, foto akan otomatis di-copy
 * oleh method syncFotoToPublic() di AdminController
 * 
 * Script ini hanya untuk sync foto yang sudah ada sebelumnya
 * Setelah berhasil, HAPUS file ini untuk keamanan!
 */

set_time_limit(300); // 5 menit

$source = __DIR__ . '/storage/app/public';
$destination = __DIR__ . '/public/storage';

echo "<h2>Copy Storage Files - Hosting (Alternatif Symlink)</h2>";
echo "<hr>";

// Cek apakah source folder ada
if (!is_dir($source)) {
    echo "<p style='color: red;'>❌ ERROR: Folder storage/app/public tidak ditemukan!</p>";
    exit;
}

echo "<p>✅ Folder storage/app/public ditemukan</p>";

// Buat destination folder jika belum ada
if (!is_dir($destination)) {
    if (mkdir($destination, 0755, true)) {
        echo "<p>✅ Folder public/storage berhasil dibuat</p>";
    } else {
        echo "<p style='color: red;'>❌ ERROR: Gagal membuat folder public/storage</p>";
        exit;
    }
} else {
    echo "<p>✅ Folder public/storage sudah ada</p>";
}

// Function untuk copy folder secara rekursif
function copyDirectory($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    
    $count = 0;
    while(false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..') && ($file != '.gitignore')) {
            if (is_dir($src . '/' . $file)) {
                $count += copyDirectory($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
                $count++;
            }
        }
    }
    closedir($dir);
    return $count;
}

// Folder yang akan di-copy
$folders = ['gallery', 'news', 'contents', 'tentang', 'cards'];

echo "<h3>Proses Copy File:</h3>";

$totalFiles = 0;
foreach ($folders as $folder) {
    $srcFolder = $source . '/' . $folder;
    $dstFolder = $destination . '/' . $folder;
    
    if (is_dir($srcFolder)) {
        echo "<p>📁 Copy folder: <strong>$folder</strong>...</p>";
        
        $fileCount = copyDirectory($srcFolder, $dstFolder);
        $totalFiles += $fileCount;
        
        echo "<p style='color: green;'>✅ $fileCount file berhasil di-copy</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Folder $folder tidak ditemukan (skip)</p>";
    }
}

echo "<hr>";
echo "<h3 style='color: green;'>🎉 SELESAI!</h3>";
echo "<p><strong>Total $totalFiles file berhasil di-copy ke public/storage/</strong></p>";

echo "<h3>Verifikasi:</h3>";
echo "<ul>";

foreach ($folders as $folder) {
    $dstFolder = $destination . '/' . $folder;
    if (is_dir($dstFolder)) {
        $files = scandir($dstFolder);
        $count = count($files) - 2; // minus . dan ..
        echo "<li>$folder: <strong>$count files</strong></li>";
    }
}

echo "</ul>";

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ PENTING:</strong></p>";
echo "<ol>";
echo "<li><strong>HAPUS file ini (copy_storage_files_hosting.php) untuk keamanan!</strong></li>";
echo "<li>Setiap kali admin upload foto baru, foto akan otomatis di-copy oleh sistem</li>";
echo "<li>Jika foto baru tidak muncul, cek permission folder public/storage (harus 755)</li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
