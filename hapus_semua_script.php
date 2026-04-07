<?php
/**
 * Script untuk hapus semua file script helper
 * Jalankan sekali, lalu hapus file ini juga!
 */

echo "<h2>Hapus Semua File Script Helper</h2>";
echo "<hr>";

$baseDir = __DIR__;

$filesToDelete = [
    // Script diagnosa
    'diagnosa_folder_hosting.php',
    'diagnosa_foto_tidak_muncul.php',
    'cek_status_foto.php',
    'cek_struktur_laravel.php',
    'cari_laravel_root.php',
    'cari_gaga_semua.php',
    'cari_gaga_hosting.php',
    'cek_foto_gaga.php',
    
    // Script fix foto
    'fix_struktur_hosting.php',
    'fix_storage_link_hosting.php',
    'fix_foto_final.php',
    'fix_foto_benar.php',
    'fix_foto_final_benar.php',
    'fix_foto_gaga.php',
    'fix_foto_otomatis.php',
    'fix_foto_tanpa_symlink.php',
    
    // Script sync foto
    'auto_sync_foto.php',
    'sync_foto_baru.php',
    'sync_foto.php',
    'test_sync_foto.php',
    'debug_sync.php',
    
    // Script lainnya
    'copy_storage_files_hosting.php',
    'clear_cache_hosting.php',
    'clear_cache.php',
    'create_storage_link.php',
    'move_gallery_images.php',
    'regenerate_autoload.php',
    'test_api_direct.php',
];

$deleted = 0;
$notFound = 0;

echo "<h3>🗑️ Proses Hapus File:</h3>";

foreach ($filesToDelete as $file) {
    $fullPath = $baseDir . '/' . $file;
    
    if (file_exists($fullPath)) {
        if (unlink($fullPath)) {
            echo "<p style='color: green;'>✅ Berhasil dihapus: <strong>$file</strong></p>";
            $deleted++;
        } else {
            echo "<p style='color: red;'>❌ Gagal hapus: <strong>$file</strong></p>";
        }
    } else {
        echo "<p style='color: gray;'>⚪ Tidak ditemukan: <strong>$file</strong></p>";
        $notFound++;
    }
}

echo "<hr>";
echo "<h3>📋 Hasil:</h3>";
echo "<p>✅ Berhasil dihapus: <strong>$deleted file</strong></p>";
echo "<p>⚪ Tidak ditemukan: <strong>$notFound file</strong></p>";

echo "<hr>";
echo "<h3 style='color: red;'>⚠️ PENTING!</h3>";
echo "<p style='color: red; font-size: 16px;'><strong>Sekarang HAPUS file ini juga (hapus_semua_script.php) secara MANUAL via File Manager!</strong></p>";
echo "<p>Karena script ini tidak bisa menghapus dirinya sendiri.</p>";

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
