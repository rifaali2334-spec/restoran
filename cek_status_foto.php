<?php
echo "<h2>🔍 CEK STATUS FOTO & STORAGE</h2>";
echo "<hr>";

// 1. CEK LOKASI FOTO LAMA
echo "<h3>1️⃣ CEK LOKASI FOTO LAMA:</h3>";

$locations = [
    'Lokasi 1' => __DIR__ . '/healthy/public/images/gallery',
    'Lokasi 2' => __DIR__ . '/naufal/public/images/gallery',
    'Lokasi 3' => __DIR__ . '/public/images/gallery',
    'Lokasi 4' => __DIR__ . '/images/gallery',
];

$found_old = false;
foreach ($locations as $name => $path) {
    if (is_dir($path)) {
        $files = glob($path . '/*');
        $count = count($files);
        if ($count > 0) {
            echo "✅ <strong>$name:</strong> $path<br>";
            echo "   📸 Ditemukan <strong>$count file</strong><br>";
            echo "   <span style='color: green;'>FOTO ADA DI SINI!</span><br><br>";
            $found_old = true;
        } else {
            echo "⚠️ <strong>$name:</strong> $path<br>";
            echo "   Folder ada tapi KOSONG<br><br>";
        }
    } else {
        echo "❌ <strong>$name:</strong> $path<br>";
        echo "   Folder tidak ada<br><br>";
    }
}

if (!$found_old) {
    echo "<p style='color: orange;'>⚠️ Tidak ditemukan foto di lokasi lama.</p>";
}

echo "<hr>";

// 2. CEK LOKASI STORAGE (TUJUAN)
echo "<h3>2️⃣ CEK LOKASI STORAGE (TUJUAN):</h3>";

$storage_path = __DIR__ . '/healthy/storage/app/public/gallery';
if (is_dir($storage_path)) {
    $files = glob($storage_path . '/*');
    $count = count($files);
    echo "✅ Folder storage/gallery: <strong>ADA</strong><br>";
    echo "📸 Jumlah foto: <strong>$count file</strong><br>";
    if ($count > 0) {
        echo "<span style='color: green;'>FOTO SUDAH ADA DI STORAGE!</span><br>";
    } else {
        echo "<span style='color: orange;'>Folder ada tapi KOSONG</span><br>";
    }
} else {
    echo "❌ Folder storage/gallery: <strong>TIDAK ADA</strong><br>";
    echo "<span style='color: red;'>Perlu dibuat!</span><br>";
}

echo "<hr>";

// 3. CEK SYMBOLIC LINK
echo "<h3>3️⃣ CEK SYMBOLIC LINK:</h3>";

$link_path = __DIR__ . '/storage';
if (file_exists($link_path)) {
    if (is_link($link_path)) {
        $target = readlink($link_path);
        echo "✅ Symbolic link: <strong>ADA</strong><br>";
        echo "📍 Link: $link_path<br>";
        echo "🎯 Target: $target<br>";
        echo "<span style='color: green;'>SYMBOLIC LINK SUDAH DIBUAT!</span><br>";
    } else {
        echo "⚠️ Folder 'storage' ada tapi <strong>BUKAN symbolic link</strong><br>";
        echo "<span style='color: red;'>Ini masalah! Harus dihapus dan dibuat ulang sebagai symbolic link!</span><br>";
    }
} else {
    echo "❌ Symbolic link: <strong>BELUM ADA</strong><br>";
    echo "<span style='color: red;'>HARUS DIBUAT!</span><br>";
}

echo "<hr>";

// 4. CEK DATABASE
echo "<h3>4️⃣ CEK DATABASE (Sample):</h3>";

try {
    // Load Laravel
    require __DIR__ . '/healthy/vendor/autoload.php';
    $app = require_once __DIR__ . '/healthy/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $galleries = DB::table('galleries')->limit(5)->get();
    
    if ($galleries->count() > 0) {
        echo "✅ Koneksi database: <strong>BERHASIL</strong><br>";
        echo "📊 Sample data galleries:<br>";
        echo "<table border='1' cellpadding='5' style='margin-top: 10px;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Image Path</th><th>Status</th></tr>";
        
        foreach ($galleries as $gallery) {
            $image_path = $gallery->image;
            $is_new_format = strpos($image_path, 'gallery/') === 0;
            $status = $is_new_format ? '<span style="color: green;">✅ Format Baru</span>' : '<span style="color: red;">❌ Format Lama</span>';
            
            echo "<tr>";
            echo "<td>$gallery->id</td>";
            echo "<td>$gallery->title</td>";
            echo "<td>$image_path</td>";
            echo "<td>$status</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "⚠️ Tidak ada data gallery di database<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error koneksi database: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 5. KESIMPULAN & SOLUSI
echo "<h3>5️⃣ KESIMPULAN & SOLUSI:</h3>";

echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";

if ($found_old && !is_dir($storage_path)) {
    echo "<p><strong>📋 STATUS:</strong> Foto ada di lokasi lama, tapi belum dipindahkan ke storage</p>";
    echo "<p><strong>✅ SOLUSI:</strong></p>";
    echo "<ol>";
    echo "<li>Pindahkan foto dari lokasi lama ke storage</li>";
    echo "<li>Buat symbolic link</li>";
    echo "<li>Clear cache</li>";
    echo "</ol>";
}

if (is_dir($storage_path) && !file_exists($link_path)) {
    echo "<p><strong>📋 STATUS:</strong> Foto sudah di storage, tapi symbolic link belum dibuat</p>";
    echo "<p><strong>✅ SOLUSI:</strong></p>";
    echo "<ol>";
    echo "<li>Buat symbolic link (php artisan storage:link)</li>";
    echo "<li>Clear cache</li>";
    echo "</ol>";
}

if (is_dir($storage_path) && is_link($link_path)) {
    echo "<p style='color: green;'><strong>✅ STATUS:</strong> Semua sudah OK! Foto harusnya muncul!</p>";
    echo "<p><strong>Jika foto masih belum muncul:</strong></p>";
    echo "<ol>";
    echo "<li>Clear cache browser (Ctrl + F5)</li>";
    echo "<li>Clear cache Laravel (php artisan cache:clear)</li>";
    echo "<li>Cek permission folder storage (chmod 775)</li>";
    echo "</ol>";
}

echo "</div>";

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ HAPUS FILE INI SETELAH SELESAI CEK!</strong></p>";
?>
