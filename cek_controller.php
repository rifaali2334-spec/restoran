<?php
/**
 * SCRIPT CEK CONTROLLER
 * =====================
 * Upload ke folder public hosting, akses via browser.
 * HAPUS setelah selesai!
 */

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

function getControllers(string $laravelRoot): array
{
    $controllerPath = $laravelRoot . '/app/Http/Controllers';
    if (!is_dir($controllerPath)) return [];

    $results = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($controllerPath, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $results[] = [
                'path'    => $file->getPathname(),
                'name'    => $file->getFilename(),
                'content' => file_get_contents($file->getPathname()),
            ];
        }
    }
    return $results;
}

$laravelRoot = findLaravelRoot();
$controllers = $laravelRoot ? getControllers($laravelRoot) : [];

// Filter hanya controller yang ada upload foto
$uploadKeywords = ['->store(', '->move(', 'hasFile(', 'storeAs(', 'store(\'', 'move(public_path'];
$filtered = array_filter($controllers, function($c) use ($uploadKeywords) {
    foreach ($uploadKeywords as $kw) {
        if (str_contains($c['content'], $kw)) return true;
    }
    return false;
});
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cek Controller</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 24px; color: #333; }
  .wrap { max-width: 900px; margin: 0 auto; }
  h1 { font-size: 1.4rem; color: #2c3e50; margin-bottom: 4px; }
  .sub { color: #888; font-size: 0.87rem; margin-bottom: 20px; }
  .card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
  .card h2 { font-size: 0.95rem; font-weight: 600; color: #555; border-bottom: 2px solid #f0f2f5; padding-bottom: 8px; margin-bottom: 14px; }
  .badge { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: 0.76rem; font-weight: 600; }
  .ok  { background: #d4edda; color: #155724; }
  .err { background: #f8d7da; color: #721c24; }
  .warn-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 0.86rem; color: #856404; }
  pre { background: #1e1e1e; color: #d4d4d4; padding: 16px; border-radius: 6px; overflow-x: auto; font-size: 0.78rem; line-height: 1.6; white-space: pre-wrap; word-break: break-word; max-height: 500px; overflow-y: auto; }
  .file-label { font-size: 0.82rem; color: #888; margin-bottom: 6px; font-family: monospace; }
  .controller-name { font-size: 1rem; font-weight: 600; color: #2c3e50; margin-bottom: 4px; }
</style>
</head>
<body>
<div class="wrap">

  <h1>🔍 Cek Controller - Upload Foto</h1>
  <p class="sub">Menampilkan controller yang handle upload foto</p>

  <div class="warn-box">⚠️ Hapus file ini setelah selesai!</div>

  <div class="card">
    <h2>📌 Info</h2>
    <p style="font-size:0.87rem">
      Laravel Root: 
      <?php if ($laravelRoot): ?>
        <span class="badge ok">✅ <?= htmlspecialchars($laravelRoot) ?></span>
      <?php else: ?>
        <span class="badge err">❌ Tidak ditemukan</span>
      <?php endif; ?>
    </p>
    <p style="font-size:0.87rem;margin-top:8px">
      Total controller ditemukan: <strong><?= count($controllers) ?></strong> &nbsp;|&nbsp;
      Controller dengan upload foto: <strong><?= count($filtered) ?></strong>
    </p>
  </div>

  <?php if (empty($filtered)): ?>
  <div class="card">
    <p style="color:#721c24;font-size:0.87rem">❌ Tidak ada controller yang mengandung kode upload foto.</p>
  </div>
  <?php else: ?>
    <?php foreach ($filtered as $c): ?>
    <div class="card">
      <div class="controller-name">📄 <?= htmlspecialchars($c['name']) ?></div>
      <div class="file-label"><?= htmlspecialchars($c['path']) ?></div>
      <br>
      <pre><?= htmlspecialchars($c['content']) ?></pre>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <div style="text-align:center;color:#ccc;font-size:0.78rem;padding:12px 0">
    cek_controller.php — Hapus setelah selesai!
  </div>

</div>
</body>
</html>
