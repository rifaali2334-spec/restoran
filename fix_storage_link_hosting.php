<?php
/**
 * Script untuk membuat symbolic link di hosting
 * Jalankan sekali saja setelah upload ke hosting
 * Setelah berhasil, HAPUS file ini untuk keamanan!
 */

// Cek apakah sudah ada symbolic link
$target = __DIR__ . '/storage/app/public';
$link = __DIR__ . '/public/storage';

echo "<h2>Fix Storage Link - Hosting</h2>";
echo "<hr>";

// Cek apakah target folder ada
if (!is_dir($target)) {
    echo "<p style='color: red;'>❌ ERROR: Folder storage/app/public tidak ditemukan!</p>";
    echo "<p>Path: $target</p>";
    exit;
}

echo "<p>✅ Folder storage/app/public ditemukan</p>";

// Hapus link lama jika ada (file atau symlink)
if (file_exists($link)) {
    if (is_link($link)) {
        unlink($link);
        echo "<p>🗑️ Symbolic link lama dihapus</p>";
    } elseif (is_dir($link)) {
        echo "<p style='color: orange;'>⚠️ WARNING: public/storage adalah folder biasa, bukan symbolic link</p>";
        echo "<p>Silakan hapus folder ini secara manual via File Manager, lalu jalankan script ini lagi</p>";
        exit;
    } else {
        unlink($link);
        echo "<p>🗑️ File lama dihapus</p>";
    }
}

// Buat symbolic link baru
try {
    // Untuk hosting, gunakan relative path
    $relativePath = '../storage/app/public';
    
    // Coba buat symlink
    if (symlink($relativePath, $link)) {
        echo "<p style='color: green;'>✅ Symbolic link berhasil dibuat!</p>";
        echo "<p>Link: $link → $relativePath</p>";
        
        // Test apakah link berfungsi
        if (is_link($link) && is_dir($link)) {
            echo "<p style='color: green;'>✅ Symbolic link berfungsi dengan baik!</p>";
            
            // Cek isi folder
            $files = scandir($link);
            $fileCount = count($files) - 2; // minus . dan ..
            echo "<p>📁 Jumlah folder di storage: $fileCount</p>";
            
            if ($fileCount > 0) {
                echo "<p>Folder yang ditemukan:</p><ul>";
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        $itemPath = $link . '/' . $file;
                        $itemCount = is_dir($itemPath) ? count(scandir($itemPath)) - 2 : 0;
                        echo "<li>$file ($itemCount files)</li>";
                    }
                }
                echo "</ul>";
            }
            
            echo "<hr>";
            echo "<h3 style='color: green;'>🎉 SUKSES!</h3>";
            echo "<p><strong>Symbolic link berhasil dibuat dan berfungsi!</strong></p>";
            echo "<p style='color: red;'><strong>⚠️ PENTING: Segera HAPUS file ini (fix_storage_link_hosting.php) untuk keamanan!</strong></p>";
            
        } else {
            echo "<p style='color: orange;'>⚠️ WARNING: Symbolic link dibuat tapi tidak berfungsi</p>";
            echo "<p>Silakan hubungi hosting provider untuk mengaktifkan symlink</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ ERROR: Gagal membuat symbolic link</p>";
        echo "<p>Kemungkinan penyebab:</p>";
        echo "<ul>";
        echo "<li>Hosting tidak mengizinkan symlink (shared hosting)</li>";
        echo "<li>Permission tidak cukup</li>";
        echo "<li>Safe mode aktif</li>";
        echo "</ul>";
        echo "<p><strong>Solusi alternatif:</strong></p>";
        echo "<p>1. Hubungi hosting provider untuk membuat symlink manual</p>";
        echo "<p>2. Atau gunakan script copy file (bukan symlink)</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ ERROR: " . $e->getMessage() . "</p>";
    echo "<p>Silakan hubungi hosting provider untuk bantuan</p>";
}

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
