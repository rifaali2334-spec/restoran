<?php
/**
 * Script untuk cek kenapa foto "GAGA" tidak muncul
 */

echo "<h2>Cek Foto yang Tidak Muncul</h2>";
echo "<hr>";

// Connect ke database
$envPath = '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html/abi/.env';

if (file_exists($envPath)) {
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
            echo "<hr>";
            
            // Cari berita dengan title "GAGA"
            echo "<h3>🔍 Cari Berita 'GAGA':</h3>";
            
            $stmt = $conn->prepare("SELECT * FROM news WHERE title LIKE '%GAGA%' OR title LIKE '%gaga%'");
            $stmt->execute();
            $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($news) > 0) {
                foreach ($news as $item) {
                    echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
                    echo "<p><strong>ID:</strong> {$item['id']}</p>";
                    echo "<p><strong>Title:</strong> {$item['title']}</p>";
                    echo "<p><strong>Image Path:</strong> <span style='color: blue;'>{$item['image']}</span></p>";
                    echo "<p><strong>Is Published:</strong> " . ($item['is_published'] ? '✅ Yes' : '❌ No') . "</p>";
                    
                    // Cek apakah file foto ada
                    $imagePath = $item['image'];
                    
                    // Cek di beberapa lokasi
                    $locations = [
                        '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html/naufal/storage/' . $imagePath,
                        '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html/abi/storage/app/public/' . $imagePath,
                    ];
                    
                    echo "<p><strong>Cek File:</strong></p>";
                    echo "<ul>";
                    
                    $fileFound = false;
                    foreach ($locations as $loc) {
                        if (file_exists($loc)) {
                            echo "<li style='color: green;'>✅ File ditemukan: $loc</li>";
                            $fileFound = true;
                            
                            // Cek ukuran file
                            $fileSize = filesize($loc);
                            echo "<li>Ukuran file: " . number_format($fileSize / 1024, 2) . " KB</li>";
                        } else {
                            echo "<li style='color: red;'>❌ File tidak ada: $loc</li>";
                        }
                    }
                    echo "</ul>";
                    
                    // Generate URL yang seharusnya
                    $expectedUrl = 'https://naufal.baknus.26.cyberwarrior.co.id/storage/' . $imagePath;
                    echo "<p><strong>URL yang seharusnya:</strong></p>";
                    echo "<p><a href='$expectedUrl' target='_blank' style='color: blue;'>$expectedUrl</a></p>";
                    
                    // Kesimpulan
                    if ($fileFound) {
                        echo "<p style='color: green;'><strong>✅ File foto ada, tapi tidak muncul di website</strong></p>";
                        echo "<p><strong>Kemungkinan masalah:</strong></p>";
                        echo "<ul>";
                        echo "<li>Path di database salah</li>";
                        echo "<li>File corrupt</li>";
                        echo "<li>Permission file salah</li>";
                        echo "</ul>";
                    } else {
                        echo "<p style='color: red;'><strong>❌ File foto tidak ditemukan!</strong></p>";
                        echo "<p><strong>Solusi:</strong> Upload ulang foto untuk berita ini</p>";
                    }
                    
                    echo "</div>";
                }
            } else {
                echo "<p style='color: orange;'>⚠️ Berita dengan title 'GAGA' tidak ditemukan di database</p>";
                
                // Cari semua berita
                echo "<hr>";
                echo "<h3>📋 Semua Berita di Database:</h3>";
                
                $stmt = $conn->query("SELECT id, title, image, is_published FROM news ORDER BY id DESC LIMIT 10");
                $allNews = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
                echo "<tr><th>ID</th><th>Title</th><th>Image</th><th>Published</th></tr>";
                
                foreach ($allNews as $item) {
                    echo "<tr>";
                    echo "<td>{$item['id']}</td>";
                    echo "<td>{$item['title']}</td>";
                    echo "<td>{$item['image']}</td>";
                    echo "<td>" . ($item['is_published'] ? '✅' : '❌') . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
            }
            
        } catch(PDOException $e) {
            echo "<p style='color: red;'>❌ Koneksi database gagal: " . $e->getMessage() . "</p>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ File .env tidak ditemukan</p>";
}

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
echo "<p style='color: red;'><strong>⚠️ HAPUS file ini setelah selesai!</strong></p>";
