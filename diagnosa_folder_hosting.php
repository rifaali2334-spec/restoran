<?php
/**
 * Script untuk cek struktur folder di hosting
 * Untuk diagnosa masalah storage/app/public tidak ditemukan
 */

echo "<h2>Diagnosa Struktur Folder - Hosting</h2>";
echo "<hr>";

// Cek current directory
$currentDir = __DIR__;
echo "<h3>📍 Current Directory:</h3>";
echo "<p><strong>$currentDir</strong></p>";
echo "<hr>";

// Cek apakah folder storage ada
echo "<h3>🔍 Cek Folder Storage:</h3>";

$storageDir = $currentDir . '/storage';
if (is_dir($storageDir)) {
    echo "<p style='color: green;'>✅ Folder <strong>storage/</strong> ditemukan</p>";
    
    // List isi folder storage
    echo "<p><strong>Isi folder storage/:</strong></p>";
    echo "<ul>";
    $storageFolders = scandir($storageDir);
    foreach ($storageFolders as $folder) {
        if ($folder != '.' && $folder != '..') {
            $isDir = is_dir($storageDir . '/' . $folder) ? '📁' : '📄';
            echo "<li>$isDir $folder</li>";
        }
    }
    echo "</ul>";
    
    // Cek folder storage/app
    $storageAppDir = $storageDir . '/app';
    if (is_dir($storageAppDir)) {
        echo "<p style='color: green;'>✅ Folder <strong>storage/app/</strong> ditemukan</p>";
        
        // List isi folder storage/app
        echo "<p><strong>Isi folder storage/app/:</strong></p>";
        echo "<ul>";
        $appFolders = scandir($storageAppDir);
        foreach ($appFolders as $folder) {
            if ($folder != '.' && $folder != '..') {
                $isDir = is_dir($storageAppDir . '/' . $folder) ? '📁' : '📄';
                echo "<li>$isDir $folder</li>";
            }
        }
        echo "</ul>";
        
        // Cek folder storage/app/public
        $storagePublicDir = $storageAppDir . '/public';
        if (is_dir($storagePublicDir)) {
            echo "<p style='color: green;'>✅ Folder <strong>storage/app/public/</strong> ditemukan</p>";
            
            // List isi folder storage/app/public
            echo "<p><strong>Isi folder storage/app/public/:</strong></p>";
            echo "<ul>";
            $publicFolders = scandir($storagePublicDir);
            foreach ($publicFolders as $folder) {
                if ($folder != '.' && $folder != '..') {
                    $fullPath = $storagePublicDir . '/' . $folder;
                    $isDir = is_dir($fullPath);
                    $icon = $isDir ? '📁' : '📄';
                    
                    if ($isDir) {
                        $fileCount = count(scandir($fullPath)) - 2;
                        echo "<li>$icon $folder ($fileCount files)</li>";
                    } else {
                        echo "<li>$icon $folder</li>";
                    }
                }
            }
            echo "</ul>";
            
        } else {
            echo "<p style='color: red;'>❌ Folder <strong>storage/app/public/</strong> TIDAK ditemukan</p>";
            echo "<p><strong>Solusi:</strong> Folder ini harus dibuat manual</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Folder <strong>storage/app/</strong> TIDAK ditemukan</p>";
        echo "<p><strong>Solusi:</strong> Folder ini harus dibuat manual</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Folder <strong>storage/</strong> TIDAK ditemukan</p>";
    echo "<p><strong>Solusi:</strong> Upload folder storage dari local ke hosting</p>";
}

echo "<hr>";

// Cek folder public
echo "<h3>🔍 Cek Folder Public:</h3>";

$publicDir = $currentDir . '/public';
if (is_dir($publicDir)) {
    echo "<p style='color: green;'>✅ Folder <strong>public/</strong> ditemukan</p>";
    
    // Cek apakah ada folder/symlink storage di public
    $publicStorageDir = $publicDir . '/storage';
    if (file_exists($publicStorageDir)) {
        if (is_link($publicStorageDir)) {
            echo "<p style='color: green;'>✅ Symbolic link <strong>public/storage</strong> sudah ada</p>";
            $linkTarget = readlink($publicStorageDir);
            echo "<p>Link target: <strong>$linkTarget</strong></p>";
        } elseif (is_dir($publicStorageDir)) {
            echo "<p style='color: orange;'>⚠️ <strong>public/storage</strong> adalah folder biasa (bukan symlink)</p>";
            
            // List isi folder public/storage
            echo "<p><strong>Isi folder public/storage/:</strong></p>";
            echo "<ul>";
            $pubStorageFolders = scandir($publicStorageDir);
            foreach ($pubStorageFolders as $folder) {
                if ($folder != '.' && $folder != '..') {
                    $fullPath = $publicStorageDir . '/' . $folder;
                    $isDir = is_dir($fullPath);
                    $icon = $isDir ? '📁' : '📄';
                    
                    if ($isDir) {
                        $fileCount = count(scandir($fullPath)) - 2;
                        echo "<li>$icon $folder ($fileCount files)</li>";
                    } else {
                        echo "<li>$icon $folder</li>";
                    }
                }
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ <strong>public/storage</strong> belum ada</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Folder <strong>public/</strong> TIDAK ditemukan</p>";
}

echo "<hr>";

// Cek dimana foto gallery disimpan
echo "<h3>🔍 Cek Lokasi Foto Gallery:</h3>";

$possiblePaths = [
    'storage/app/public/gallery',
    'public/storage/gallery',
    'public/images/gallery',
    'storage/gallery'
];

$foundGallery = false;
foreach ($possiblePaths as $path) {
    $fullPath = $currentDir . '/' . $path;
    if (is_dir($fullPath)) {
        $fileCount = count(scandir($fullPath)) - 2;
        echo "<p style='color: green;'>✅ Ditemukan: <strong>$path/</strong> ($fileCount files)</p>";
        $foundGallery = true;
        
        // List beberapa file
        if ($fileCount > 0) {
            echo "<p><strong>Sample files:</strong></p>";
            echo "<ul>";
            $files = array_slice(scandir($fullPath), 2, 5); // ambil 5 file pertama
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    echo "<li>$file</li>";
                }
            }
            if ($fileCount > 5) {
                echo "<li>... dan " . ($fileCount - 5) . " file lainnya</li>";
            }
            echo "</ul>";
        }
    }
}

if (!$foundGallery) {
    echo "<p style='color: red;'>❌ Tidak ditemukan folder gallery di semua lokasi yang dicek</p>";
}

echo "<hr>";

// Kesimpulan dan rekomendasi
echo "<h3>📋 Kesimpulan & Rekomendasi:</h3>";

if (!is_dir($storageDir)) {
    echo "<p style='color: red;'><strong>❌ MASALAH UTAMA:</strong> Folder storage tidak ada di hosting</p>";
    echo "<p><strong>SOLUSI:</strong></p>";
    echo "<ol>";
    echo "<li>Upload folder <strong>storage/</strong> dari local ke hosting</li>";
    echo "<li>Pastikan struktur folder: <strong>storage/app/public/</strong></li>";
    echo "<li>Set permission: chmod -R 755 storage</li>";
    echo "</ol>";
} elseif (!is_dir($storageAppDir)) {
    echo "<p style='color: red;'><strong>❌ MASALAH:</strong> Folder storage/app tidak ada</p>";
    echo "<p><strong>SOLUSI:</strong> Buat folder manual via File Manager atau upload dari local</p>";
} elseif (!is_dir($storagePublicDir)) {
    echo "<p style='color: red;'><strong>❌ MASALAH:</strong> Folder storage/app/public tidak ada</p>";
    echo "<p><strong>SOLUSI:</strong> Buat folder manual via File Manager atau upload dari local</p>";
} else {
    echo "<p style='color: green;'><strong>✅ Struktur folder sudah benar!</strong></p>";
    echo "<p>Silakan lanjut ke langkah berikutnya untuk membuat symbolic link</p>";
}

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
echo "<p style='color: red;'><strong>⚠️ HAPUS file ini setelah selesai diagnosa!</strong></p>";
