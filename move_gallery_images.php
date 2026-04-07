<?php
/**
 * Script untuk memindahkan foto gallery dari lokasi lama ke lokasi baru
 * Jalankan sekali saja setelah update code
 * 
 * CARA PAKAI:
 * 1. Upload file ini ke root folder hosting
 * 2. Akses via browser: http://domain.com/move_gallery_images.php
 * 3. Setelah selesai, HAPUS file ini untuk keamanan
 */

// Path lokasi lama dan baru
$oldPath = __DIR__ . '/public/images/gallery';
$newPath = __DIR__ . '/healthy/storage/app/public/gallery';

echo "<h2>🔄 Memindahkan Foto Gallery</h2>";
echo "<hr>";

// Cek apakah folder lama ada
if (!is_dir($oldPath)) {
    echo "❌ Folder lama tidak ditemukan: $oldPath<br>";
    echo "Kemungkinan foto sudah dipindahkan atau tidak ada foto.<br>";
    exit;
}

// Buat folder baru jika belum ada
if (!is_dir($newPath)) {
    if (mkdir($newPath, 0755, true)) {
        echo "✅ Folder baru berhasil dibuat: $newPath<br><br>";
    } else {
        echo "❌ Gagal membuat folder baru: $newPath<br>";
        exit;
    }
} else {
    echo "✅ Folder baru sudah ada: $newPath<br><br>";
}

// Ambil semua file di folder lama
$files = glob($oldPath . '/*');

if (empty($files)) {
    echo "ℹ️ Tidak ada file untuk dipindahkan.<br>";
    exit;
}

echo "<h3>Memindahkan file:</h3>";
$success = 0;
$failed = 0;

foreach ($files as $file) {
    if (is_file($file)) {
        $filename = basename($file);
        $destination = $newPath . '/' . $filename;
        
        // Copy file ke lokasi baru
        if (copy($file, $destination)) {
            echo "✅ $filename berhasil dipindahkan<br>";
            $success++;
        } else {
            echo "❌ $filename gagal dipindahkan<br>";
            $failed++;
        }
    }
}

echo "<hr>";
echo "<h3>📊 Ringkasan:</h3>";
echo "✅ Berhasil: $success file<br>";
echo "❌ Gagal: $failed file<br>";
echo "<br>";

if ($success > 0) {
    echo "<p style='color: green; font-weight: bold;'>✅ Proses selesai! Foto gallery berhasil dipindahkan.</p>";
    echo "<p><strong>PENTING:</strong></p>";
    echo "<ol>";
    echo "<li>Cek website apakah foto sudah muncul</li>";
    echo "<li>Jika sudah muncul, HAPUS file ini (move_gallery_images.php) untuk keamanan</li>";
    echo "<li>Jika sudah yakin, bisa hapus folder lama: public/images/gallery/</li>";
    echo "</ol>";
} else {
    echo "<p style='color: red;'>❌ Tidak ada file yang berhasil dipindahkan. Cek permission folder.</p>";
}
?>
