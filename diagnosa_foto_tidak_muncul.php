<?php
/**
 * Script untuk cek kenapa foto tidak muncul
 */

echo "<h2>Diagnosa Foto Tidak Muncul</h2>";
echo "<hr>";

$currentDir = __DIR__;
echo "<p><strong>Current Directory:</strong> $currentDir</p>";
echo "<hr>";

// STEP 1: Cek apakah foto ada di storage/app/public
echo "<h3>STEP 1: Cek Foto di Storage</h3>";

$storagePath = dirname($currentDir) . '/storage/app/public/gallery';
echo "<p><strong>Path storage:</strong> $storagePath</p>";

if (is_dir($storagePath)) {
    $files = scandir($storagePath);
    $fileCount = count($files) - 2;
    echo "<p style='color: green;'>✅ Folder storage/app/public/gallery ditemukan ($fileCount files)</p>";
    
    // Tampilkan 5 file pertama
    echo "<p><strong>Sample files:</strong></p>";
    echo "<ul>";
    $count = 0;
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $count < 5) {
            echo "<li>$file</li>";
            $count++;
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ Folder storage/app/public/gallery TIDAK ditemukan</p>";
}

echo "<hr>";

// STEP 2: Cek apakah foto ada di public/storage (naufal/storage)
echo "<h3>STEP 2: Cek Foto di Public/Storage</h3>";

$publicStoragePath = $currentDir . '/storage/gallery';
echo "<p><strong>Path public storage:</strong> $publicStoragePath</p>";

if (is_dir($publicStoragePath)) {
    $files = scandir($publicStoragePath);
    $fileCount = count($files) - 2;
    echo "<p style='color: green;'>✅ Folder public/storage/gallery ditemukan ($fileCount files)</p>";
    
    // Tampilkan 5 file pertama
    echo "<p><strong>Sample files:</strong></p>";
    echo "<ul>";
    $count = 0;
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $count < 5) {
            echo "<li>$file</li>";
            $count++;
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ Folder public/storage/gallery TIDAK ditemukan</p>";
    echo "<p><strong>Solusi:</strong> Folder ini harus dibuat dan diisi dengan foto</p>";
}

echo "<hr>";

// STEP 3: Test akses foto langsung
echo "<h3>STEP 3: Test Akses Foto</h3>";

// Ambil 1 file foto untuk test
$testFile = null;
if (is_dir($storagePath)) {
    $files = scandir($storagePath);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != '.gitignore') {
            $testFile = $file;
            break;
        }
    }
}

if ($testFile) {
    echo "<p><strong>Test file:</strong> $testFile</p>";
    
    // Test URL yang berbeda
    $baseUrl = 'https://naufal.baknus.26.cyberwarrior.co.id';
    
    $testUrls = [
        $baseUrl . '/storage/gallery/' . $testFile,
        $baseUrl . '/naufal/storage/gallery/' . $testFile,
        $baseUrl . '/../storage/app/public/gallery/' . $testFile,
    ];
    
    echo "<p><strong>Test URL (klik untuk cek):</strong></p>";
    echo "<ul>";
    foreach ($testUrls as $url) {
        echo "<li><a href='$url' target='_blank'>$url</a></li>";
    }
    echo "</ul>";
    
    echo "<p><strong>Instruksi:</strong> Klik link di atas, jika foto muncul berarti URL tersebut yang benar!</p>";
} else {
    echo "<p style='color: red;'>❌ Tidak ada file foto untuk di-test</p>";
}

echo "<hr>";

// STEP 4: Cek database - path foto di database
echo "<h3>STEP 4: Cek Path Foto di Database</h3>";

// Coba connect ke database
$envPath = dirname($currentDir) . '/.env';
if (file_exists($envPath)) {
    echo "<p style='color: green;'>✅ File .env ditemukan</p>";
    
    // Parse .env
    $envContent = file_get_contents($envPath);
    preg_match('/DB_HOST=(.*)/', $envContent, $host);
    preg_match('/DB_DATABASE=(.*)/', $envContent, $database);
    preg_match('/DB_USERNAME=(.*)/', $envContent, $username);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $password);
    
    if (isset($host[1]) && isset($database[1]) && isset($username[1])) {
        $dbHost = trim($host[1]);
        $dbName = trim($database[1]);
        $dbUser = trim($username[1]);
        $dbPass = isset($password[1]) ? trim($password[1]) : '';
        
        try {
            $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "<p style='color: green;'>✅ Koneksi database berhasil</p>";
            
            // Query sample foto dari database
            $stmt = $conn->query("SELECT id, image FROM galleries LIMIT 5");
            $galleries = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($galleries) > 0) {
                echo "<p><strong>Sample path foto di database:</strong></p>";
                echo "<ul>";
                foreach ($galleries as $gallery) {
                    echo "<li>ID: {$gallery['id']} - Path: <strong>{$gallery['image']}</strong></li>";
                }
                echo "</ul>";
                
                // Cek format path
                $firstImage = $galleries[0]['image'];
                if (strpos($firstImage, 'gallery/') === 0) {
                    echo "<p style='color: green;'>✅ Format path sudah benar: <strong>gallery/namafile.jpg</strong></p>";
                } else {
                    echo "<p style='color: orange;'>⚠️ Format path: <strong>$firstImage</strong></p>";
                    echo "<p>Seharusnya: <strong>gallery/namafile.jpg</strong></p>";
                }
            } else {
                echo "<p style='color: orange;'>⚠️ Tidak ada data gallery di database</p>";
            }
            
        } catch(PDOException $e) {
            echo "<p style='color: red;'>❌ Koneksi database gagal: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Tidak bisa parse database config dari .env</p>";
    }
} else {
    echo "<p style='color: red;'>❌ File .env tidak ditemukan</p>";
}

echo "<hr>";

// STEP 5: Cek struktur folder public
echo "<h3>STEP 5: Cek Struktur Folder Public (naufal)</h3>";

echo "<p><strong>Isi folder naufal/:</strong></p>";
echo "<ul>";
$items = scandir($currentDir);
foreach ($items as $item) {
    if ($item != '.' && $item != '..') {
        $fullPath = $currentDir . '/' . $item;
        $isDir = is_dir($fullPath);
        $icon = $isDir ? '📁' : '📄';
        echo "<li>$icon $item</li>";
    }
}
echo "</ul>";

echo "<hr>";

// Kesimpulan
echo "<h3>📋 Kesimpulan & Solusi:</h3>";

$hasStorageFolder = is_dir($storagePath);
$hasPublicStorage = is_dir($publicStoragePath);

if ($hasStorageFolder && !$hasPublicStorage) {
    echo "<p style='color: red;'><strong>❌ MASALAH:</strong> Foto ada di storage/app/public/ tapi tidak ada di public/storage/</p>";
    echo "<p><strong>SOLUSI:</strong></p>";
    echo "<ol>";
    echo "<li>Folder <strong>naufal/storage/</strong> harus dibuat</li>";
    echo "<li>Copy semua foto dari <strong>storage/app/public/</strong> ke <strong>naufal/storage/</strong></li>";
    echo "</ol>";
    echo "<p style='color: green;'><strong>Saya akan buatkan script untuk fix ini!</strong></p>";
} elseif (!$hasStorageFolder && !$hasPublicStorage) {
    echo "<p style='color: red;'><strong>❌ MASALAH:</strong> Foto tidak ada di storage maupun public</p>";
} elseif ($hasPublicStorage) {
    echo "<p style='color: green;'><strong>✅ Foto sudah ada di public/storage/</strong></p>";
    echo "<p><strong>Kemungkinan masalah:</strong></p>";
    echo "<ul>";
    echo "<li>Path foto di database salah</li>";
    echo "<li>URL di view salah</li>";
    echo "<li>Cache belum di-clear</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
echo "<p style='color: red;'><strong>⚠️ HAPUS file ini setelah selesai!</strong></p>";
