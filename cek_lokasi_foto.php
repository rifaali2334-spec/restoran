<?php
/**
 * SCRIPT CEK LOKASI FOTO
 * ======================
 * Upload ke folder public hosting, akses via browser.
 * HAPUS setelah selesai!
 */

function scanImages(string $dir, int $maxDepth = 4): array
{
    if (!is_dir($dir)) return [];
    $files = [];
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        $iterator->setMaxDepth($maxDepth);
        foreach ($iterator as $file) {
            if ($file->isFile() && in_array(strtolower($file->getExtension()), ['jpg','jpeg','png','gif','webp','svg'])) {
                $files[] = $file->getPathname();
            }
        }
    } catch (Exception $e) {}
    return $files;
}

function findLaravelRoot(): ?string
{
    $dir = __DIR__;
    for ($i = 0; $i < 6; $i++) {
        if (file_exists($dir . '/artisan') && file_exists($dir . '/composer.json')) return $dir;
        $parent = dirname($dir);
        if ($parent === $dir) break;
        $dir = $parent;
    }
    foreach (glob(dirname(__DIR__) . '/*', GLOB_ONLYDIR) as $sibling) {
        if (file_exists($sibling . '/artisan') && file_exists($sibling . '/composer.json')) return $sibling;
    }
    return null;
}

function scanAllFolders(string $root): array
{
    $results = [];
    $checkDirs = [
        'public/images'        => $root . '/public/images',
        'public/uploads'       => $root . '/public/uploads',
        'public/storage'       => $root . '/public/storage',
        'storage/app/public'   => $root . '/storage/app/public',
        'storage/app/public/gallery'  => $root . '/storage/app/public/gallery',
        'storage/app/public/images'   => $root . '/storage/app/public/images',
        'storage/app/public/galeri'   => $root . '/storage/app/public/galeri',
        'public/img'           => $root . '/public/img',
        'public/assets'        => $root . '/public/assets',
        'public/foto'          => $root . '/public/foto',
    ];

    foreach ($checkDirs as $label => $path) {
        $exists = is_dir($path);
        $files  = $exists ? scanImages($path) : [];
        $results[] = [
            'label'  => $label,
            'path'   => $path,
            'exists' => $exists,
            'count'  => count($files),
            'sample' => array_slice(array_map('basename', $files), 0, 3),
        ];
    }
    return $results;
}

$laravelRoot = findLaravelRoot();
$folderResults = $laravelRoot ? scanAllFolders($laravelRoot) : [];

// Cek juga di folder public hosting (lokasi script)
$publicHostingImages = [
    'images'   => __DIR__ . '/images',
    'uploads'  => __DIR__ . '/uploads',
    'storage'  => __DIR__ . '/storage',
    'foto'     => __DIR__ . '/foto',
    'galeri'   => __DIR__ . '/galeri',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cek Lokasi Foto</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 24px; color: #333; }
  .wrap { max-width: 820px; margin: 0 auto; }
  h1 { font-size: 1.4rem; color: #2c3e50; margin-bottom: 4px; }
  .sub { color: #888; font-size: 0.87rem; margin-bottom: 20px; }
  .card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
  .card h2 { font-size: 0.93rem; font-weight: 600; color: #555; border-bottom: 2px solid #f0f2f5; padding-bottom: 8px; margin-bottom: 14px; }
  table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
  th { background: #f8f9fa; padding: 8px 10px; text-align: left; color: #666; font-weight: 600; border-bottom: 2px solid #eee; }
  td { padding: 8px 10px; border-bottom: 1px solid #f5f5f5; vertical-align: top; }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: #fafafa; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 0.76rem; font-weight: 600; }
  .ok   { background: #d4edda; color: #155724; }
  .err  { background: #f8d7da; color: #721c24; }
  .warn { background: #fff3cd; color: #856404; }
  code  { background: #f4f4f4; padding: 1px 5px; border-radius: 3px; font-size: 0.8rem; font-family: monospace; word-break: break-all; }
  .warn-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 0.86rem; color: #856404; }
  .highlight { background: #d4edda !important; font-weight: 600; }
  .sample { color: #888; font-size: 0.78rem; margin-top: 3px; }
</style>
</head>
<body>
<div class="wrap">

  <h1>🔍 Cek Lokasi Foto di Hosting</h1>
  <p class="sub">Scan semua kemungkinan lokasi foto di server</p>

  <div class="warn-box">⚠️ Hapus file ini setelah selesai!</div>

  <!-- Info Dasar -->
  <div class="card">
    <h2>📌 Informasi Server</h2>
    <table>
      <tr><th>Keterangan</th><th>Nilai</th></tr>
      <tr><td>Lokasi Script</td><td><code><?= htmlspecialchars(__FILE__) ?></code></td></tr>
      <tr><td>Folder Public Hosting</td><td><code><?= htmlspecialchars(__DIR__) ?></code></td></tr>
      <tr><td>Laravel Root</td><td>
        <?php if ($laravelRoot): ?>
          <span class="badge ok">✅ Ditemukan</span><br><code><?= htmlspecialchars($laravelRoot) ?></code>
        <?php else: ?>
          <span class="badge err">❌ Tidak Ditemukan</span>
        <?php endif; ?>
      </td></tr>
      <tr><td>PHP Version</td><td><?= PHP_VERSION ?></td></tr>
    </table>
  </div>

  <!-- Scan Folder di Laravel Root -->
  <?php if ($laravelRoot): ?>
  <div class="card">
    <h2>📁 Scan Folder di Laravel Root</h2>
    <p style="font-size:0.83rem;color:#888;margin-bottom:12px">
      Folder yang di-highlight hijau = ada fotonya (kemungkinan lokasi foto yang benar)
    </p>
    <table>
      <tr>
        <th>Folder</th>
        <th>Status</th>
        <th>Jumlah Foto</th>
        <th>Contoh File</th>
      </tr>
      <?php foreach ($folderResults as $r): ?>
      <tr class="<?= $r['count'] > 0 ? 'highlight' : '' ?>">
        <td><code><?= htmlspecialchars($r['label']) ?></code></td>
        <td>
          <?php if ($r['exists'] && $r['count'] > 0): ?>
            <span class="badge ok">✅ Ada foto</span>
          <?php elseif ($r['exists']): ?>
            <span class="badge warn">📁 Folder ada, kosong</span>
          <?php else: ?>
            <span class="badge err">❌ Tidak ada</span>
          <?php endif; ?>
        </td>
        <td><?= $r['count'] > 0 ? '<strong>' . $r['count'] . ' file</strong>' : '-' ?></td>
        <td>
          <?php foreach ($r['sample'] as $s): ?>
            <div class="sample"><?= htmlspecialchars($s) ?></div>
          <?php endforeach; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
  <?php endif; ?>

  <!-- Scan Folder di Public Hosting -->
  <div class="card">
    <h2>🌐 Scan Folder di Public Hosting (<?= htmlspecialchars(basename(__DIR__)) ?>)</h2>
    <table>
      <tr>
        <th>Folder</th>
        <th>Status</th>
        <th>Jumlah Foto</th>
        <th>Contoh File</th>
      </tr>
      <?php foreach ($publicHostingImages as $label => $path): ?>
      <?php
        $exists = is_dir($path);
        $files  = $exists ? scanImages($path) : [];
        $count  = count($files);
        $sample = array_slice(array_map('basename', $files), 0, 3);
      ?>
      <tr class="<?= $count > 0 ? 'highlight' : '' ?>">
        <td><code><?= htmlspecialchars($label) ?></code><br><span style="font-size:0.75rem;color:#aaa"><?= htmlspecialchars($path) ?></span></td>
        <td>
          <?php if ($exists && $count > 0): ?>
            <span class="badge ok">✅ Ada foto</span>
          <?php elseif ($exists): ?>
            <span class="badge warn">📁 Folder ada, kosong</span>
          <?php else: ?>
            <span class="badge err">❌ Tidak ada</span>
          <?php endif; ?>
        </td>
        <td><?= $count > 0 ? '<strong>' . $count . ' file</strong>' : '-' ?></td>
        <td>
          <?php foreach ($sample as $s): ?>
            <div class="sample"><?= htmlspecialchars($s) ?></div>
          <?php endforeach; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <!-- Kesimpulan -->
  <div class="card">
    <h2>💡 Kesimpulan</h2>
    <?php
      $found = array_filter($folderResults, fn($r) => $r['count'] > 0);
    ?>
    <?php if (!empty($found)): ?>
      <p style="font-size:0.87rem;color:#155724;margin-bottom:10px">✅ Foto ditemukan di lokasi berikut:</p>
      <ul style="font-size:0.87rem;padding-left:20px;line-height:2">
        <?php foreach ($found as $r): ?>
          <li><code><?= htmlspecialchars($r['label']) ?></code> — <strong><?= $r['count'] ?> foto</strong><br>
          <span style="color:#888;font-size:0.8rem"><?= htmlspecialchars($r['path']) ?></span></li>
        <?php endforeach; ?>
      </ul>
      <p style="font-size:0.85rem;color:#555;margin-top:12px">
        Screenshot hasil ini dan kirim ke developer untuk dibuatkan script fix yang tepat.
      </p>
    <?php else: ?>
      <p style="font-size:0.87rem;color:#721c24">
        ❌ Tidak ada foto ditemukan di semua lokasi yang dicek.<br>
        Kemungkinan foto belum pernah diupload, atau disimpan di lokasi yang tidak standar.
      </p>
    <?php endif; ?>
  </div>

  <div style="text-align:center;color:#ccc;font-size:0.78rem;padding:12px 0">
    cek_lokasi_foto.php — Hapus setelah selesai!
  </div>

</div>
</body>
</html>
