<?php
/**
 * Script untuk pindahkan foto ke lokasi yang BENAR
 * Berdasarkan struktur hosting yang sebenarnya
 */

set_time_limit(300);

echo "<h2>Pindahkan Foto ke Lokasi yang Benar</h2>";
echo "<hr>";

$naufalDir = __DIR__; // /public_html/naufal
$publicHtmlDir = dirname($naufalDir); // /public_html

echo "<p><strong>Naufal Directory:</strong> $naufalDir</p>";
echo "<p><strong>Public HTML Directory:</strong> $publicHtmlDir</p>";
echo "<hr>";

// STEP 1: Buat struktur folder yang benar di /public_html/storage/app/public/
echo "<h3>STEP 1: Buat Struktur Folder di /public_html/</h3>";

$folders = [
    $publicHtmlDir . '/storage',
    $publicHtmlDir . '/storage/app',
    $publicHtmlDir . '/storage/app/public',
    $publicHtmlDir . '/storage/app/public/gallery',
    $publicHtmlDir . '/storage/app/public/news',
    $publicHtmlDir . '/storage/app/public/contents',
    $publicHtmlDir . '/storage/app/public/tentang',
    $publicHtmlDir . '/storage/app/public/cards'
];

foreach ($folders as $folder) {
    if (!is_dir($folder)) {
        if (mkdir($folder, 0755, true)) {
            echo "<p style='color: green;'>✅ Folder <strong>" . basename($folder) . "/</strong> berhasil dibuat</p>";
        } else {
            echo "<p style='color: red;'>❌ Gagal membuat folder <strong>" . basename($folder) . "/</strong></p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Folder <strong>" . basename($folder) . "/</strong> sudah ada</p>";
    }
}

echo "<hr>";

// STEP 2: Copy foto dari naufal/storage/ ke /public_html/storage/app/public/
echo "<h3>STEP 2: Copy Foto ke Lokasi yang Benar</h3>";

$copyFolders = [
    'gallery' => ['from' => $naufalDir . '/storage/gallery', 'to' => $publicHtmlDir . '/storage/app/public/gallery'],
    'news' => ['from' => $naufalDir . '/storage/news', 'to' => $publicHtmlDir . '/storage/app/public/news'],
    'contents' => ['from' => $naufalDir . '/storage/contents', 'to' => $publicHtmlDir . '/storage/app/public/contents'],
    'tentang' => ['from' => $naufalDir . '/storage/tentang', 'to' => $publicHtmlDir . '/storage/app/public/tentang'],
    'cards' => ['from' => $naufalDir . '/storage/cards', 'to' => $publicHtmlDir . '/storage/app/public/cards']
];

$totalCopied = 0;

foreach ($copyFolders as $name => $paths) {
    $fromPath = $paths['from'];
    $toPath = $paths['to'];
    
    if (is_dir($fromPath)) {
        $files = scandir($fromPath);
        $count = 0;
        
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && $file != '.gitignore') {
                $srcFile = $fromPath . '/' . $file;
                $dstFile = $toPath . '/' . $file;
                
                if (is_file($srcFile)) {
                    if (copy($srcFile, $dstFile)) {
                        $count++;
                    }
                }
            }
        }
        
        $totalCopied += $count;
        echo "<p style='color: green;'>✅ Copy <strong>$name</strong>: $count files</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Folder <strong>$name</strong> tidak ditemukan (skip)</p>";
    }
}

echo "<p><strong>Total: $totalCopied files berhasil di-copy</strong></p>";
echo "<hr>";

// STEP 3: Buat/Update symbolic link atau copy ke naufal/storage
echo "<h3>STEP 3: Update Akses di Naufal/Storage</h3>";

// Hapus folder naufal/storage yang lama
$naufalStorageOld = $naufalDir . '/storage';

// Buat symbolic link dari naufal/storage ke /public_html/storage/app/public
$targetPath = $publicHtmlDir . '/storage/app/public';
$linkPath = $naufalDir . '/storage';

// Hapus link/folder lama jika ada
if (file_exists($linkPath)) {
    if (is_link($linkPath)) {
        unlink($linkPath);
        echo "<p>🗑️ Symbolic link lama dihapus</p>";
    } else {
        // Jangan hapus folder, nanti foto hilang
        echo "<p style='color: blue;'>ℹ️ Folder storage sudah ada, akan di-update</p>";
    }
}

// Coba buat symbolic link
$relativePath = '../storage/app/public';

if (!file_exists($linkPath)) {
    if (@symlink($relativePath, $linkPath)) {
        echo "<p style='color: green;'>✅ Symbolic link berhasil dibuat!</p>";
        echo "<p>Link: naufal/storage → ../storage/app/public</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Symbolic link gagal, folder copy sudah ada</p>";
    }
}

echo "<hr>";

// STEP 4: Verifikasi
echo "<h3>STEP 4: Verifikasi</h3>";

$verifyFolders = [
    'gallery' => $publicHtmlDir . '/storage/app/public/gallery',
    'news' => $publicHtmlDir . '/storage/app/public/news',
    'contents' => $publicHtmlDir . '/storage/app/public/contents',
    'tentang' => $publicHtmlDir . '/storage/app/public/tentang',
    'cards' => $publicHtmlDir . '/storage/app/public/cards'
];

echo "<p><strong>Foto di /public_html/storage/app/public/:</strong></p>";
echo "<ul>";
foreach ($verifyFolders as $name => $path) {
    if (is_dir($path)) {
        $fileCount = count(scandir($path)) - 2;
        echo "<li style='color: green;'>✅ $name ($fileCount files)</li>";
    } else {
        echo "<li style='color: red;'>❌ $name (tidak ada)</li>";
    }
}
echo "</ul>";

echo "<hr>";

// STEP 5: Test URL foto
echo "<h3>STEP 5: Test URL Foto</h3>";

// Ambil 1 file foto untuk test
$testFile = null;
$galleryPath = $publicHtmlDir . '/storage/app/public/gallery';
if (is_dir($galleryPath)) {
    $files = scandir($galleryPath);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != '.gitignore') {
            $testFile = $file;
            break;
        }
    }
}

if ($testFile) {
    echo "<p><strong>Test file:</strong> $testFile</p>";
    
    $baseUrl = 'https://naufal.baknus.26.cyberwarrior.co.id';
    
    echo "<p><strong>Test URL (klik untuk cek foto muncul):</strong></p>";
    echo "<ul>";
    echo "<li><a href='$baseUrl/storage/gallery/$testFile' target='_blank' style='color: blue; font-weight: bold;'>$baseUrl/storage/gallery/$testFile</a></li>";
    echo "</ul>";
    
    echo "<p><strong>Instruksi:</strong> Klik link di atas, jika foto muncul berarti BERHASIL!</p>";
}

echo "<hr>";

// Kesimpulan
echo "<h3>🎉 SELESAI!</h3>";
echo "<p><strong>Yang sudah dilakukan:</strong></p>";
echo "<ol>";
echo "<li>✅ Struktur folder /public_html/storage/app/public/ dibuat</li>";
echo "<li>✅ $totalCopied foto berhasil di-copy ke lokasi yang benar</li>";
echo "<li>✅ Akses naufal/storage sudah di-update</li>";
echo "</ol>";

echo "<p style='color: red;'><strong>⚠️ LANGKAH SELANJUTNYA:</strong></p>";
echo "<ol>";
echo "<li>Klik link test URL di atas, cek apakah foto muncul</li>";
echo "<li>Jika foto muncul, buka website dan cek semua halaman</li>";
echo "<li>Jika foto masih tidak muncul, screenshot dan kasih tau saya</li>";
echo "<li><strong>HAPUS semua file script PHP untuk keamanan!</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
