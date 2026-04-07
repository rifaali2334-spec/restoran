<?php
/**
 * Script FINAL - Pindahkan foto ke lokasi yang BENAR
 * Dari: /naufal/storage/
 * Ke: /healthy/storage/app/public/
 */

set_time_limit(300);

echo "<h2>Fix Foto - Pindahkan ke Lokasi yang Benar</h2>";
echo "<hr>";

// Deteksi path otomatis
$currentDir = __DIR__;
echo "<p><strong>Current Directory:</strong> $currentDir</p>";

// Cari folder healthy (root Laravel)
$healthyPath = null;
$possiblePaths = [
    dirname(dirname($currentDir)) . '/healthy',
    dirname($currentDir) . '/healthy',
    '/home/aff100/domains/baknus.26.cyberwarrior.co.id/healthy',
];

foreach ($possiblePaths as $path) {
    if (is_dir($path) && file_exists($path . '/artisan')) {
        $healthyPath = $path;
        break;
    }
}

if (!$healthyPath) {
    echo "<p style='color: red;'>❌ ERROR: Folder 'healthy' (root Laravel) tidak ditemukan!</p>";
    echo "<p>Coba cek manual di File Manager</p>";
    exit;
}

echo "<p style='color: green;'>✅ Root Laravel ditemukan: <strong>$healthyPath</strong></p>";
echo "<hr>";

// STEP 1: Buat struktur folder di healthy/storage/app/public/
echo "<h3>STEP 1: Buat Struktur Folder</h3>";

$folders = [
    $healthyPath . '/storage/app',
    $healthyPath . '/storage/app/public',
    $healthyPath . '/storage/app/public/gallery',
    $healthyPath . '/storage/app/public/news',
    $healthyPath . '/storage/app/public/contents',
    $healthyPath . '/storage/app/public/tentang',
    $healthyPath . '/storage/app/public/cards'
];

foreach ($folders as $folder) {
    if (!is_dir($folder)) {
        if (mkdir($folder, 0755, true)) {
            echo "<p style='color: green;'>✅ Folder berhasil dibuat: " . str_replace($healthyPath, '', $folder) . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Gagal membuat folder: " . str_replace($healthyPath, '', $folder) . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Folder sudah ada: " . str_replace($healthyPath, '', $folder) . "</p>";
    }
}

echo "<hr>";

// STEP 2: Copy foto dari naufal/storage/ ke healthy/storage/app/public/
echo "<h3>STEP 2: Copy Foto</h3>";

$copyMap = [
    'gallery' => ['from' => $currentDir . '/storage/gallery', 'to' => $healthyPath . '/storage/app/public/gallery'],
    'news' => ['from' => $currentDir . '/storage/news', 'to' => $healthyPath . '/storage/app/public/news'],
    'contents' => ['from' => $currentDir . '/storage/contents', 'to' => $healthyPath . '/storage/app/public/contents'],
    'tentang' => ['from' => $currentDir . '/storage/tentang', 'to' => $healthyPath . '/storage/app/public/tentang'],
    'cards' => ['from' => $currentDir . '/storage/cards', 'to' => $healthyPath . '/storage/app/public/cards']
];

$totalCopied = 0;

foreach ($copyMap as $name => $paths) {
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

// STEP 3: Buat/Update symbolic link di healthy/public/storage
echo "<h3>STEP 3: Buat Symbolic Link</h3>";

$publicStoragePath = $healthyPath . '/public/storage';
$targetPath = $healthyPath . '/storage/app/public';

// Hapus jika sudah ada
if (file_exists($publicStoragePath)) {
    if (is_link($publicStoragePath)) {
        unlink($publicStoragePath);
        echo "<p>🗑️ Symbolic link lama dihapus</p>";
    } elseif (is_dir($publicStoragePath)) {
        echo "<p style='color: blue;'>ℹ️ Folder public/storage sudah ada</p>";
    }
}

// Buat symbolic link baru
if (!file_exists($publicStoragePath)) {
    $relativePath = '../../storage/app/public';
    
    if (@symlink($relativePath, $publicStoragePath)) {
        echo "<p style='color: green;'>✅ Symbolic link berhasil dibuat!</p>";
        echo "<p>Link: healthy/public/storage → ../../storage/app/public</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Symbolic link gagal, akan copy file</p>";
        
        // Copy sebagai alternatif
        if (!is_dir($publicStoragePath)) {
            mkdir($publicStoragePath, 0755, true);
        }
        
        $copyFolders = ['gallery', 'news', 'contents', 'tentang', 'cards'];
        foreach ($copyFolders as $folder) {
            $srcFolder = $targetPath . '/' . $folder;
            $dstFolder = $publicStoragePath . '/' . $folder;
            
            if (is_dir($srcFolder)) {
                if (!is_dir($dstFolder)) {
                    mkdir($dstFolder, 0755, true);
                }
                
                $files = scandir($srcFolder);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..' && $file != '.gitignore') {
                        copy($srcFolder . '/' . $file, $dstFolder . '/' . $file);
                    }
                }
            }
        }
        
        echo "<p style='color: green;'>✅ File berhasil di-copy ke public/storage</p>";
    }
}

echo "<hr>";

// STEP 4: Verifikasi
echo "<h3>STEP 4: Verifikasi</h3>";

$verifyFolders = [
    'gallery' => $healthyPath . '/storage/app/public/gallery',
    'news' => $healthyPath . '/storage/app/public/news',
    'contents' => $healthyPath . '/storage/app/public/contents',
    'tentang' => $healthyPath . '/storage/app/public/tentang',
    'cards' => $healthyPath . '/storage/app/public/cards'
];

echo "<p><strong>Foto di healthy/storage/app/public/:</strong></p>";
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

// STEP 5: Test URL
echo "<h3>STEP 5: Test URL Foto</h3>";

$testFile = null;
$galleryPath = $healthyPath . '/storage/app/public/gallery';
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
    $testUrl = $baseUrl . '/storage/gallery/' . $testFile;
    
    echo "<p><strong>Test URL (KLIK untuk cek foto):</strong></p>";
    echo "<p><a href='$testUrl' target='_blank' style='color: blue; font-size: 18px; font-weight: bold;'>$testUrl</a></p>";
    
    echo "<p><strong>Instruksi:</strong></p>";
    echo "<ol>";
    echo "<li>Klik link di atas</li>";
    echo "<li>Jika foto muncul = <strong style='color: green;'>BERHASIL!</strong></li>";
    echo "<li>Jika 404 = Ada masalah dengan symbolic link</li>";
    echo "</ol>";
}

echo "<hr>";

// Kesimpulan
echo "<h3>🎉 SELESAI!</h3>";
echo "<p><strong>Yang sudah dilakukan:</strong></p>";
echo "<ol>";
echo "<li>✅ Struktur folder healthy/storage/app/public/ dibuat</li>";
echo "<li>✅ $totalCopied foto berhasil di-copy</li>";
echo "<li>✅ Symbolic link healthy/public/storage dibuat</li>";
echo "</ol>";

echo "<p style='color: red;'><strong>⚠️ LANGKAH SELANJUTNYA:</strong></p>";
echo "<ol>";
echo "<li><strong>Klik link test URL di atas</strong></li>";
echo "<li>Jika foto muncul, buka website dan cek semua halaman</li>";
echo "<li>Jika foto muncul semua, <strong>HAPUS semua file script PHP!</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
