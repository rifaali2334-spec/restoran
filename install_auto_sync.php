<?php
/**
 * SCRIPT AUTO INSTALL SYNC FOTO
 * ==============================
 * Script ini otomatis:
 * 1. Membuat file AutoSyncFoto.php (trait)
 * 2. Memasang trait ke semua controller yang handle upload foto
 * 3. Menambahkan syncFotoKePublic() setelah setiap upload
 *
 * CARA PAKAI:
 * 1. Upload file ini ke folder public hosting (public_html/aditya/)
 * 2. Akses via browser
 * 3. Klik "Install Auto Sync"
 * 4. HAPUS file ini setelah selesai!
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

$action = $_GET['action'] ?? 'preview';
$logs   = [];

// ---- Isi trait AutoSyncFoto ----
$traitContent = <<<'PHP'
<?php

namespace App\Http\Controllers;

trait AutoSyncFoto
{
    protected function syncFotoKePublic(): void
    {
        try {
            $src = storage_path('app/public');
            $dst = $this->detectPublicStorage();
            if (!$dst) return;
            if (!is_dir($dst)) mkdir($dst, 0755, true);
            $this->copyRekursif($src, $dst);
        } catch (\Throwable $e) {
            \Log::error('AutoSyncFoto error: ' . $e->getMessage());
        }
    }

    private function detectPublicStorage(): ?string
    {
        if (is_dir(public_path('storage'))) {
            return public_path('storage');
        }
        $parent = dirname(base_path());
        foreach (glob($parent . '/*', GLOB_ONLYDIR) as $sibling) {
            if (realpath($sibling) === realpath(base_path())) continue;
            if (is_writable($sibling)) {
                $candidate = $sibling . '/storage';
                if (!is_dir($candidate)) mkdir($candidate, 0755, true);
                return $candidate;
            }
        }
        $fallback = public_path('storage');
        if (!is_dir($fallback)) mkdir($fallback, 0755, true);
        return $fallback;
    }

    private function copyRekursif(string $src, string $dst): void
    {
        if (!is_dir($src)) return;
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($items as $item) {
            $dest = $dst . '/' . $items->getSubPathname();
            if ($item->isDir()) {
                if (!is_dir($dest)) mkdir($dest, 0755, true);
            } else {
                copy($item->getPathname(), $dest);
            }
        }
    }
}
PHP;

// ---- Helper ----

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

function getUploadControllers(string $laravelRoot): array
{
    $controllerPath = $laravelRoot . '/app/Http/Controllers';
    if (!is_dir($controllerPath)) return [];

    $results = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($controllerPath, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') continue;
        $content = file_get_contents($file->getPathname());
        // Skip trait itu sendiri kalau sudah ada
        if (str_contains($content, 'trait AutoSyncFoto')) continue;
        // Hanya controller yang ada upload foto
        if (!str_contains($content, "->store(") && !str_contains($content, 'hasFile(')) continue;
        // Hanya yang extends Controller (bukan trait/interface)
        if (!str_contains($content, 'extends Controller')) continue;

        $results[] = [
            'path'    => $file->getPathname(),
            'name'    => $file->getFilename(),
            'content' => $content,
        ];
    }
    return $results;
}

function patchController(string $content): array
{
    $changed = false;
    $notes   = [];

    // 1. Tambah "use AutoSyncFoto;" setelah baris class ... extends Controller {
    if (!str_contains($content, 'use AutoSyncFoto;')) {
        $content = preg_replace(
            '/(class\s+\w+\s+extends\s+Controller\s*\{)/',
            "$1\n    use AutoSyncFoto;",
            $content,
            1,
            $count
        );
        if ($count > 0) {
            $changed = true;
            $notes[] = '✅ Trait AutoSyncFoto dipasang';
        }
    } else {
        $notes[] = '⏭️ Trait sudah ada, dilewati';
    }

    // 2. Tambah syncFotoKePublic() sebelum return redirect() yang ada setelah upload
    //    Hanya di method store() dan update()
    $pattern = '/(if\s*\(\s*\$request->hasFile[^}]+?->store\([^)]+\)[^;]*;[^}]*?)([ \t]*return\s+redirect\(\))/s';
    $replacement = '$1' . "\n        \$this->syncFotoKePublic();\n" . '        $2';

    $newContent = preg_replace($pattern, $replacement, $content, -1, $count);
    if ($count > 0 && $newContent !== $content) {
        $content = $newContent;
        $changed = true;
        $notes[] = "✅ syncFotoKePublic() ditambahkan ($count titik)";
    }

    // Fallback: cari semua ->store( lalu cari return redirect() terdekat setelahnya
    if ($count === 0) {
        // Cari blok yang ada ->store( dan belum ada syncFotoKePublic sebelum return redirect
        $content = preg_replace_callback(
            '/(->store\(\'[^\']+\',\s*\'public\'\);)(.*?)([ \t]*return\s+redirect\(\))/s',
            function ($m) use (&$changed, &$notes) {
                if (str_contains($m[2], 'syncFotoKePublic')) return $m[0];
                $changed = true;
                $notes[] = '✅ syncFotoKePublic() ditambahkan (fallback)';
                return $m[1] . $m[2] . "\n        \$this->syncFotoKePublic();\n        " . ltrim($m[3]);
            },
            $content
        );
    }

    return ['content' => $content, 'changed' => $changed, 'notes' => $notes];
}

// ---- Jalankan ----

$laravelRoot = findLaravelRoot();
$controllers = $laravelRoot ? getUploadControllers($laravelRoot) : [];
$traitPath   = $laravelRoot ? $laravelRoot . '/app/Http/Controllers/AutoSyncFoto.php' : null;

if ($action === 'install' && $laravelRoot) {

    // 1. Buat file trait
    if (file_put_contents($traitPath, $traitContent)) {
        $logs[] = ['status' => 'ok', 'msg' => 'File AutoSyncFoto.php berhasil dibuat'];
    } else {
        $logs[] = ['status' => 'error', 'msg' => 'Gagal membuat AutoSyncFoto.php — cek permission folder'];
    }

    // 2. Patch semua controller
    foreach ($controllers as $c) {
        $result = patchController($c['content']);

        if (!$result['changed']) {
            $logs[] = ['status' => 'skip', 'msg' => $c['name'] . ' — tidak ada perubahan'];
            continue;
        }

        // Backup dulu
        file_put_contents($c['path'] . '.bak', $c['content']);

        if (file_put_contents($c['path'], $result['content'])) {
            foreach ($result['notes'] as $note) {
                $logs[] = ['status' => 'ok', 'msg' => $c['name'] . ' — ' . $note];
            }
        } else {
            $logs[] = ['status' => 'error', 'msg' => $c['name'] . ' — Gagal ditulis, cek permission'];
        }
    }

    $action = 'result';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Install Auto Sync Foto</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 24px; color: #333; }
  .wrap { max-width: 820px; margin: 0 auto; }
  h1 { font-size: 1.4rem; color: #2c3e50; margin-bottom: 4px; }
  .sub { color: #888; font-size: 0.87rem; margin-bottom: 20px; }
  .card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
  .card h2 { font-size: 0.93rem; font-weight: 600; color: #555; border-bottom: 2px solid #f0f2f5; padding-bottom: 8px; margin-bottom: 14px; }
  .badge { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: 0.76rem; font-weight: 600; }
  .ok   { background: #d4edda; color: #155724; }
  .err  { background: #f8d7da; color: #721c24; }
  .warn { background: #fff3cd; color: #856404; }
  .warn-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 0.86rem; color: #856404; }
  .btn { display: inline-block; padding: 10px 22px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; text-decoration: none; border: none; cursor: pointer; margin-right: 8px; }
  .btn-green { background: #27ae60; color: #fff; }
  .btn-blue  { background: #3498db; color: #fff; }
  .btn:hover { opacity: 0.85; }
  .log { list-style: none; font-size: 0.84rem; }
  .log li { padding: 6px 10px; border-bottom: 1px solid #f5f5f5; }
  .log li:last-child { border-bottom: none; }
  .l-ok    { color: #155724; background: #f0fff4; }
  .l-error { color: #721c24; background: #fff5f5; }
  .l-skip  { color: #856404; background: #fffdf0; }
  code { background: #f4f4f4; padding: 1px 5px; border-radius: 3px; font-size: 0.81rem; font-family: monospace; }
  table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
  th { background: #f8f9fa; padding: 8px 10px; text-align: left; color: #666; font-weight: 600; border-bottom: 2px solid #eee; }
  td { padding: 8px 10px; border-bottom: 1px solid #f5f5f5; }
</style>
</head>
<body>
<div class="wrap">

  <h1>⚙️ Install Auto Sync Foto</h1>
  <p class="sub">Otomatis pasang auto-sync ke semua controller yang handle upload foto</p>

  <div class="warn-box">⚠️ Hapus file ini setelah selesai! File controller lama akan di-backup (.bak)</div>

  <?php if ($action === 'result'): ?>

  <!-- Hasil Install -->
  <div class="card">
    <h2>📋 Hasil Install</h2>
    <?php
      $ok   = count(array_filter($logs, fn($l) => $l['status'] === 'ok'));
      $err  = count(array_filter($logs, fn($l) => $l['status'] === 'error'));
      $skip = count(array_filter($logs, fn($l) => $l['status'] === 'skip'));
    ?>
    <div style="margin-bottom:14px;font-size:0.87rem">
      <span class="badge ok"><?= $ok ?> berhasil</span>&nbsp;
      <span class="badge warn"><?= $skip ?> dilewati</span>&nbsp;
      <span class="badge err"><?= $err ?> gagal</span>
    </div>
    <ul class="log">
      <?php foreach ($logs as $l): ?>
      <li class="l-<?= $l['status'] ?>"><?= htmlspecialchars($l['msg']) ?></li>
      <?php endforeach; ?>
    </ul>
    <br>
    <?php if ($err === 0): ?>
    <div style="background:#d4edda;border-radius:6px;padding:12px;font-size:0.87rem;color:#155724">
      ✅ <strong>Install berhasil!</strong> Sekarang setiap kali admin upload foto, foto akan otomatis muncul di website.
    </div>
    <?php else: ?>
    <div style="background:#f8d7da;border-radius:6px;padding:12px;font-size:0.87rem;color:#721c24">
      ❌ Ada <?= $err ?> file yang gagal. Kemungkinan masalah permission. Coba ubah permission folder <code>app/Http/Controllers</code> menjadi 755.
    </div>
    <?php endif; ?>
    <br>
    <a href="?" class="btn btn-blue">← Kembali</a>
  </div>

  <?php else: ?>

  <!-- Preview -->
  <div class="card">
    <h2>📌 Info Server</h2>
    <table>
      <tr><th>Keterangan</th><th>Nilai</th></tr>
      <tr>
        <td>Laravel Root</td>
        <td>
          <?php if ($laravelRoot): ?>
            <span class="badge ok">✅ Ditemukan</span> <code><?= htmlspecialchars($laravelRoot) ?></code>
          <?php else: ?>
            <span class="badge err">❌ Tidak ditemukan</span>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <td>File AutoSyncFoto.php</td>
        <td>
          <?php if ($traitPath && file_exists($traitPath)): ?>
            <span class="badge warn">⚠️ Sudah ada (akan ditimpa)</span>
          <?php else: ?>
            <span class="badge ok">✅ Akan dibuat baru</span>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <td>Controller yang akan diubah</td>
        <td><strong><?= count($controllers) ?> controller</strong></td>
      </tr>
    </table>
  </div>

  <?php if (!empty($controllers)): ?>
  <div class="card">
    <h2>📄 Controller yang Akan Diubah</h2>
    <table>
      <tr><th>File</th><th>Path</th></tr>
      <?php foreach ($controllers as $c): ?>
      <tr>
        <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
        <td><code style="font-size:0.78rem"><?= htmlspecialchars(str_replace($laravelRoot, '', $c['path'])) ?></code></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <div style="background:#f8f9fa;border-radius:6px;padding:10px;font-size:0.82rem;color:#666;margin-top:12px">
      💾 File asli akan di-backup dengan ekstensi <code>.bak</code> sebelum diubah.
    </div>
  </div>
  <?php endif; ?>

  <div class="card">
    <h2>🛠️ Aksi</h2>
    <?php if (!$laravelRoot): ?>
    <div style="color:#721c24;background:#f8d7da;padding:12px;border-radius:6px;font-size:0.87rem">
      ❌ Laravel root tidak ditemukan. Pastikan script diupload ke folder public hosting.
    </div>
    <?php elseif (empty($controllers)): ?>
    <div style="color:#856404;background:#fff3cd;padding:12px;border-radius:6px;font-size:0.87rem">
      ⚠️ Tidak ada controller yang perlu diubah. Mungkin sudah terpasang sebelumnya.
    </div>
    <?php else: ?>
    <p style="font-size:0.87rem;color:#666;margin-bottom:14px">
      Script akan membuat <code>AutoSyncFoto.php</code> dan memasangnya ke <strong><?= count($controllers) ?> controller</strong> secara otomatis.
    </p>
    <a href="?action=install" class="btn btn-green"
       onclick="return confirm('Install auto-sync ke <?= count($controllers) ?> controller sekarang?')">
      ⚙️ Install Auto Sync Sekarang
    </a>
    <a href="?" class="btn btn-blue">🔄 Refresh</a>
    <?php endif; ?>
  </div>

  <?php endif; ?>

  <div style="text-align:center;color:#ccc;font-size:0.78rem;padding:12px 0">
    install_auto_sync.php — Hapus setelah selesai!
  </div>

</div>
</body>
</html>
