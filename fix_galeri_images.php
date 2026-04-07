<?php
// Upload ke public_html/aditya/ lalu akses via browser
$publicRoot = __DIR__;
$src = $publicRoot . '/images';
$dst = $publicRoot . '/storage/galeri';

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Fix Galeri</title>";
echo "<style>body{font-family:sans-serif;max-width:700px;margin:30px auto;padding:0 20px}
.ok{color:green}.err{color:red}</style></head><body>";
echo "<h2>Fix Foto Galeri</h2>";

if (!is_dir($src)) {
    echo "<p class='err'>✗ Folder images tidak ditemukan</p></body></html>";
    exit;
}

if (!is_dir($dst)) @mkdir($dst, 0755, true);

$files = array_diff(scandir($src), ['.', '..', '.htaccess']);
$berhasil = 0; $gagal = 0;

foreach ($files as $file) {
    $srcFile = $src . '/' . $file;
    $dstFile = $dst . '/' . $file;
    if (is_file($srcFile)) {
        if (@copy($srcFile, $dstFile)) {
            echo "<p class='ok'>✓ $file</p>";
            $berhasil++;
        } else {
            echo "<p class='err'>✗ Gagal: $file</p>";
            $gagal++;
        }
    }
}

echo "<br><p><b>Selesai: $berhasil berhasil, $gagal gagal</b></p>";
echo "<p>Sekarang coba refresh halaman galeri — foto grid harusnya muncul.</p>";
echo "<p style='color:orange'>⚠ Hapus file ini setelah selesai!</p>";
echo "</body></html>";
