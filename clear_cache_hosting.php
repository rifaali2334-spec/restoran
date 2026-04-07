<?php
/**
 * Script untuk clear cache Laravel di hosting
 * Jalankan sekali saja setelah fix struktur folder
 * Setelah berhasil, HAPUS file ini!
 */

echo "<h2>Clear Cache Laravel - Hosting</h2>";
echo "<hr>";

// Cek apakah artisan ada
$artisanPath = __DIR__ . '/artisan';

if (!file_exists($artisanPath)) {
    echo "<p style='color: red;'>❌ ERROR: File artisan tidak ditemukan!</p>";
    echo "<p>Path: $artisanPath</p>";
    exit;
}

echo "<p>✅ File artisan ditemukan</p>";
echo "<hr>";

// Function untuk jalankan artisan command
function runArtisan($command) {
    $output = [];
    $returnVar = 0;
    
    // Coba jalankan command
    exec("php artisan $command 2>&1", $output, $returnVar);
    
    return [
        'success' => $returnVar === 0,
        'output' => implode("\n", $output)
    ];
}

// Clear cache
echo "<h3>1. Clear Cache</h3>";
$result = runArtisan('cache:clear');
if ($result['success']) {
    echo "<p style='color: green;'>✅ Cache berhasil di-clear</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Cache clear: " . htmlspecialchars($result['output']) . "</p>";
}

// Clear config cache
echo "<h3>2. Clear Config Cache</h3>";
$result = runArtisan('config:clear');
if ($result['success']) {
    echo "<p style='color: green;'>✅ Config cache berhasil di-clear</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Config clear: " . htmlspecialchars($result['output']) . "</p>";
}

// Clear view cache
echo "<h3>3. Clear View Cache</h3>";
$result = runArtisan('view:clear');
if ($result['success']) {
    echo "<p style='color: green;'>✅ View cache berhasil di-clear</p>";
} else {
    echo "<p style='color: orange;'>⚠️ View clear: " . htmlspecialchars($result['output']) . "</p>";
}

// Clear route cache
echo "<h3>4. Clear Route Cache</h3>";
$result = runArtisan('route:clear');
if ($result['success']) {
    echo "<p style='color: green;'>✅ Route cache berhasil di-clear</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Route clear: " . htmlspecialchars($result['output']) . "</p>";
}

echo "<hr>";
echo "<h3>🎉 SELESAI!</h3>";
echo "<p><strong>Semua cache Laravel berhasil di-clear!</strong></p>";

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ PENTING:</strong></p>";
echo "<ol>";
echo "<li><strong>HAPUS file ini (clear_cache_hosting.php) untuk keamanan!</strong></li>";
echo "<li>Test website sekarang, cek apakah foto muncul</li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Script by: Healthy Tasty Food - " . date('Y-m-d H:i:s') . "</small></p>";
