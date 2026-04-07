<?php
/**
 * Script untuk cari "gaga" di semua tabel
 */

echo "<h2>Cari 'GAGA' di Semua Tabel</h2>";
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
            
            // Cari di tabel NEWS
            echo "<h3>🔍 Cari di Tabel NEWS:</h3>";
            $stmt = $conn->prepare("SELECT * FROM news WHERE title LIKE '%gaga%' OR content LIKE '%gaga%' OR excerpt LIKE '%gaga%'");
            $stmt->execute();
            $newsResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($newsResults) > 0) {
                foreach ($newsResults as $item) {
                    echo "<p>✅ Ditemukan: ID {$item['id']} - {$item['title']} - Image: {$item['image']}</p>";
                }
            } else {
                echo "<p style='color: orange;'>⚠️ Tidak ditemukan di tabel NEWS</p>";
            }
            
            echo "<hr>";
            
            // Cari di tabel GALLERIES
            echo "<h3>🔍 Cari di Tabel GALLERIES:</h3>";
            $stmt = $conn->prepare("SELECT * FROM galleries WHERE title LIKE '%gaga%'");
            $stmt->execute();
            $galleryResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($galleryResults) > 0) {
                foreach ($galleryResults as $item) {
                    echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0;'>";
                    echo "<p><strong>✅ DITEMUKAN!</strong></p>";
                    echo "<p><strong>ID:</strong> {$item['id']}</p>";
                    echo "<p><strong>Title:</strong> {$item['title']}</p>";
                    echo "<p><strong>Image:</strong> <span style='color: blue;'>{$item['image']}</span></p>";
                    echo "<p><strong>Is Published:</strong> " . ($item['is_published'] ? '✅ Yes' : '❌ No') . "</p>";
                    
                    // Cek file
                    $imagePath = $item['image'];
                    $locations = [
                        '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html/naufal/storage/' . $imagePath,
                        '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html/abi/storage/app/public/' . $imagePath,
                    ];
                    
                    echo "<p><strong>Cek File:</strong></p>";
                    $fileFound = false;
                    foreach ($locations as $loc) {
                        if (file_exists($loc)) {
                            echo "<p style='color: green;'>✅ File ada: $loc</p>";
                            $fileFound = true;
                        } else {
                            echo "<p style='color: red;'>❌ File tidak ada: $loc</p>";
                        }
                    }
                    
                    if (!$fileFound) {
                        echo "<p style='color: red;'><strong>❌ MASALAH: File foto tidak ditemukan!</strong></p>";
                        echo "<p><strong>SOLUSI:</strong> Upload ulang foto untuk gallery ini</p>";
                    }
                    
                    echo "</div>";
                }
            } else {
                echo "<p style='color: orange;'>⚠️ Tidak ditemukan di tabel GALLERIES</p>";
            }
            
            echo "<hr>";
            
            // Cari di tabel CONTENTS
            echo "<h3>🔍 Cari di Tabel CONTENTS:</h3>";
            $stmt = $conn->prepare("SELECT * FROM contents WHERE title LIKE '%gaga%' OR content LIKE '%gaga%'");
            $stmt->execute();
            $contentResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($contentResults) > 0) {
                foreach ($contentResults as $item) {
                    echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0;'>";
                    echo "<p><strong>✅ DITEMUKAN!</strong></p>";
                    echo "<p><strong>ID:</strong> {$item['id']}</p>";
                    echo "<p><strong>Key:</strong> {$item['key']}</p>";
                    echo "<p><strong>Title:</strong> {$item['title']}</p>";
                    echo "<p><strong>Content:</strong> {$item['content']}</p>";
                    echo "<p><strong>Image:</strong> <span style='color: blue;'>{$item['image']}</span></p>";
                    
                    if ($item['image']) {
                        // Cek file
                        $imagePath = $item['image'];
                        $locations = [
                            '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html/naufal/storage/' . $imagePath,
                            '/home/aff100/domains/baknus.26.cyberwarrior.co.id/public_html/abi/storage/app/public/' . $imagePath,
                        ];
                        
                        echo "<p><strong>Cek File:</strong></p>";
                        $fileFound = false;
                        foreach ($locations as $loc) {
                            if (file_exists($loc)) {
                                echo "<p style='color: green;'>✅ File ada: $loc</p>";
                                $fileFound = true;
                            } else {
                                echo "<p style='color: red;'>❌ File tidak ada: $loc</p>";
                            }
                        }
                        
                        if (!$fileFound) {
                            echo "<p style='color: red;'><strong>❌ MASALAH: File foto tidak ditemukan!</strong></p>";
                            echo "<p><strong>SOLUSI:</strong> Upload ulang foto untuk content ini</p>";
                        }
                    } else {
                        echo "<p style='color: orange;'>⚠️ Tidak ada image untuk content ini</p>";
                    }
                    
                    echo "</div>";
                }
            } else {
                echo "<p style='color: orange;'>⚠️ Tidak ditemukan di tabel CONTENTS</p>";
            }
            
        } catch(PDOException $e) {
            echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
echo "<p style='color: red;'><strong>⚠️ HAPUS file ini setelah selesai!</strong></p>";
