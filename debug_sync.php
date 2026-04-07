<?php
echo "<h2>🔍 DEBUG AUTO-SYNC</h2>";
echo "<hr>";

// Cek AdminController.php
$controller_path = __DIR__ . '/healthy/app/Http/Controllers/AdminController.php';
$content = file_get_contents($controller_path);

echo "<h3>1. Cek Fungsi syncFotoToPublic()</h3>";
if (strpos($content, 'private function syncFotoToPublic()') !== false) {
    echo "✅ Fungsi syncFotoToPublic() ADA<br>";
} else {
    echo "❌ Fungsi syncFotoToPublic() TIDAK ADA<br>";
}

echo "<br><h3>2. Cek Pemanggilan di storeNews()</h3>";
if (strpos($content, 'public function storeNews') !== false) {
    // Ambil isi fungsi storeNews
    preg_match('/public function storeNews\(.*?\{(.*?)\n    \}/s', $content, $matches);
    if (isset($matches[1])) {
        $storeNewsContent = $matches[1];
        if (strpos($storeNewsContent, '$this->syncFotoToPublic()') !== false) {
            echo "✅ storeNews() MEMANGGIL syncFotoToPublic()<br>";
        } else {
            echo "❌ storeNews() TIDAK MEMANGGIL syncFotoToPublic()<br>";
            echo "<strong>INI MASALAHNYA!</strong><br>";
        }
    }
}

echo "<br><h3>3. Cek Pemanggilan di updateNewsData()</h3>";
if (strpos($content, 'public function updateNewsData') !== false) {
    preg_match('/public function updateNewsData\(.*?\{(.*?)\n    \}/s', $content, $matches);
    if (isset($matches[1])) {
        $updateNewsContent = $matches[1];
        if (strpos($updateNewsContent, '$this->syncFotoToPublic()') !== false) {
            echo "✅ updateNewsData() MEMANGGIL syncFotoToPublic()<br>";
        } else {
            echo "❌ updateNewsData() TIDAK MEMANGGIL syncFotoToPublic()<br>";
            echo "<strong>INI MASALAHNYA!</strong><br>";
        }
    }
}

echo "<br><h3>4. Cek Pemanggilan di addGallery()</h3>";
if (strpos($content, 'public function addGallery') !== false) {
    preg_match('/public function addGallery\(.*?\{(.*?)\n    \}/s', $content, $matches);
    if (isset($matches[1])) {
        $addGalleryContent = $matches[1];
        if (strpos($addGalleryContent, '$this->syncFotoToPublic()') !== false) {
            echo "✅ addGallery() MEMANGGIL syncFotoToPublic()<br>";
        } else {
            echo "❌ addGallery() TIDAK MEMANGGIL syncFotoToPublic()<br>";
        }
    }
}

echo "<br><h3>5. Last Modified</h3>";
$last_modified = date("Y-m-d H:i:s", filemtime($controller_path));
echo "📅 AdminController.php terakhir diubah: <strong>$last_modified</strong><br>";

echo "<hr>";

echo "<h3>📊 KESIMPULAN:</h3>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
echo "<strong>Jika ada yang ❌ di atas, berarti AdminController.php belum terupdate dengan benar!</strong><br><br>";
echo "<strong>SOLUSI:</strong><br>";
echo "1. Upload ulang AdminController.php dengan benar<br>";
echo "2. Atau gunakan Cron Job untuk auto-sync setiap 5 menit<br>";
echo "3. Atau akses manual: <a href='/auto_sync_foto.php'>auto_sync_foto.php</a> setelah tambah foto<br>";
echo "</div>";

echo "<hr>";
echo "<p style='color: red;'><strong>HAPUS FILE INI SETELAH SELESAI!</strong></p>";
?>
