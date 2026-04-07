<?php
// Upload ke public_html/aditya/ lalu akses via browser SEKALI SAJA

$abiPath = dirname(__DIR__) . '/abi';
$file = $abiPath . '/app/Http/Controllers/GaleriController.php';

$content = file_get_contents($file);
$original = $content;

// Cek apakah sudah di-patch sebelumnya
if (strpos($content, 'Pastikan folder ada') !== false) {
    echo "ℹ️ Controller sudah di-patch sebelumnya<br>";
} else {
    // Patch store() - ganti storeAs galeri di method store
    $content = preg_replace(
        '/(\$filename\s*=\s*time\(\)\s*\.\s*\'_\'\s*\.\s*\$file->getClientOriginalName\(\);\s*\n\s*)(\$gambar\s*=\s*\$file->storeAs\(\'galeri\',\s*\$filename,\s*\'public\'\);)/',
        '$1// Pastikan folder ada' . "\n" .
        '                    $storageDir = storage_path(\'app/public/galeri\');' . "\n" .
        '                    if (!is_dir($storageDir)) mkdir($storageDir, 0755, true);' . "\n" .
        '                    $gambar = $file->storeAs(\'galeri\', $filename, \'public\');' . "\n" .
        '                    // Sync ke public' . "\n" .
        '                    $publicDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . \'/aditya/storage/galeri\';' . "\n" .
        '                    if (!is_dir($publicDir)) mkdir($publicDir, 0755, true);' . "\n" .
        '                    @copy($storageDir . \'/\' . $filename, $publicDir . \'/\' . $filename);',
        $content
    );

    // Patch update() - ganti storeAs galeri di method update
    $content = preg_replace(
        '/(\$filename\s*=\s*time\(\)\s*\.\s*\'_\'\s*\.\s*\$file->getClientOriginalName\(\);\s*\n\s*)(\$data\[\'gambar\'\]\s*=\s*\$file->storeAs\(\'galeri\',\s*\$filename,\s*\'public\'\);)/',
        '$1// Pastikan folder ada' . "\n" .
        '                    $storageDir = storage_path(\'app/public/galeri\');' . "\n" .
        '                    if (!is_dir($storageDir)) mkdir($storageDir, 0755, true);' . "\n" .
        '                    $data[\'gambar\'] = $file->storeAs(\'galeri\', $filename, \'public\');' . "\n" .
        '                    // Sync ke public' . "\n" .
        '                    $publicDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . \'/aditya/storage/galeri\';' . "\n" .
        '                    if (!is_dir($publicDir)) mkdir($publicDir, 0755, true);' . "\n" .
        '                    @copy($storageDir . \'/\' . $filename, $publicDir . \'/\' . $filename);',
        $content
    );

    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "✅ GaleriController.php berhasil di-patch<br>";
    } else {
        echo "⚠️ Regex juga tidak cocok. Tampilkan bagian upload untuk cek manual:<br><pre>";
        // Tampilkan baris sekitar storeAs
        $lines = explode("\n", $original);
        foreach ($lines as $i => $line) {
            if (strpos($line, 'storeAs') !== false || strpos($line, 'getClientOriginalName') !== false) {
                $start = max(0, $i - 2);
                $end = min(count($lines) - 1, $i + 2);
                for ($j = $start; $j <= $end; $j++) {
                    echo "Line " . ($j+1) . ": " . htmlspecialchars($lines[$j]) . "\n";
                }
                echo "---\n";
            }
        }
        echo "</pre>";
        exit;
    }
}

// Deteksi path aditya yang benar
$possiblePaths = [
    dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/aditya/storage/galeri',
    dirname(__DIR__) . '/storage/galeri',
    '/home/' . get_current_user() . '/public_html/aditya/storage/galeri',
];

echo "<br>=== CEK PATH ADITYA/STORAGE/GALERI ===<br>";
foreach ($possiblePaths as $p) {
    echo htmlspecialchars($p) . " → " . (is_dir($p) ? '✅ ADA' : '❌ TIDAK') . "<br>";
}

echo "<br>=== __DIR__ dari controller ===<br>";
echo htmlspecialchars($abiPath . '/app/Http/Controllers') . "<br>";
echo "public_path() = " . htmlspecialchars(public_path()) . "<br>";
echo "storage_path() = " . htmlspecialchars(storage_path()) . "<br>";
