<?php
/**
 * SCRIPT FIX FOTO - KHUSUS UNTUK PROYEK INI
 * ===========================================
 * Masalah: Foto disimpan di public/images/ tapi tidak muncul di hosting
 * karena folder public Laravel dan folder public hosting terpisah.
 *
 * CARA PAKAI:
 * 1. Upload file ini ke folder PUBLIC website di hosting
 *    (folder yang bisa diakses browser, contoh: public_html/aditya/)
 * 2. Akses via browser: https://domainmu.com/fix_foto_teman.php
 * 3. Klik "Copy Foto Sekarang"
 * 4. HAPUS file ini setelah selesai!
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

$action = $_GET['action'] ?? 'diagnosa';
$copyResults = [];

// ---- Helper ----

function findLaravelRoot(): ?string
{
    // Naik dari lokasi script sampai ketemu artisan + composer.json
    $dir = __DIR__;
    for ($i = 0; $i < 6; $i++) {
        if (file_exists($dir . '/artisan') && file_exists($dir . '/composer.json')) {
            return $dir;
        }
        $parent = dirname($dir);
        if ($parent === $dir) break;
        $dir = $parent;
    }

    // Cari di sibling folder (hosting split)
    $siblings = glob(dirname(__DIR__) . '/*', GLOB_ONLYDIR);
    foreach ($siblings as $sibling) {
        if (file_exists($sibling . '/artisan') && file_exists($sibling . '/composer.json')) {
            return $sibling;
        }
    }

    return null;
}

function scanImages(string $dir): array
{
    if (!is_dir($dir)) return [];
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array(strtolower($file->getExtension()), ['jpg','jpeg','png','gif','webp','svg'])) {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

function copyImages(string $src, string $dst): array
{
    $log = [];
    if (!is_dir($src)) {
        return [['status' => 'error', 'msg' => "Folder source tidak ditemukan: $src"]];
    }
    if (!is_dir($dst) && !mkdir($dst, 0755, true)) {
        return [['status' => 'error', 'msg' => "Gagal buat folder: $dst"]];
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $subPath = $iterator->getSubPathname();
        $dstPath = $dst . '/' . $subPath;

        if ($item->isDir()) {
            if (!is_dir($dstPath)) mkdir($dstPath, 0755, true);
            continue;
        }

        $ext = strtolower($item->getExtension());
        if (!in_array($ext, ['jpg','jpeg','png','gif','webp','svg'])) continue;

        if (file_exists($dstPath) && filemtime($item->getPathname()) <= filemtime($dstPath)) {
            $log[] = ['status' => 'skip', 'msg' => "Skip (sudah ada): $subPath"];
            continue;
        }

        if (copy($item->getPathname(), $dstPath)) {
            $log[] = ['status' => 'ok', 'msg' => "Disalin: $subPath"];
        } else {
            $log[] = ['status' => 'error', 'msg' => "Gagal salin: $subPath"];
        }
    }

    return $log;
}

// ---- Diagnosa ----

$laravelRoot   = findLaravelRoot();
$publicDir     = __DIR__;                                          // folder public hosting (lokasi script)
$srcImages     = $laravelRoot ? $laravelRoot . '/storage/app/public' : null; // sumber foto di Laravel
$dstImages     = $publicDir . '/storage';                                      // tujuan di folder public hosting

$srcFiles  = $srcImages ? scanImages($srcImages) : [];
$dstFiles  = scanImages($dstImages);
$missing   = count($srcFiles) - count($dstFiles);

// ---- Action ----

if ($action === 'copy' && $laravelRoot) {
    $copyResults = copyImages($srcImages, $dstImages);
    $action = 'result';
}

// ---- HTML ----
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fix Foto Hosting</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 24px; color: #333; }
  .wrap { max-width: 780px; margin: 0 auto; }
  h1 { font-size: 1.5rem; color: #e74c3c; margin-bottom: 4px; }
  .sub { color: #888; font-size: 0.88rem; margin-bottom: 24px; }
  .card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
  .card h2 { font-size: 0.95rem; font-weight: 600; color: #555; border-bottom: 2px solid #f0f2f5; padding-bottom: 8px; margin-bottom: 14px; }
  .row { display: flex; justify-content: space-between; align-items: flex-start; padding: 7px 0; border-bottom: 1px solid #f8f8f8; font-size: 0.87rem; gap: 12px; }
  .row:last-child { border-bottom: none; }
  .lbl { color: #999; min-width: 180px; flex-shrink: 0; }
  .val { color: #333; word-break: break-all; }
  .badge { display: inline-block; padding: 2px 9px; border-radius: 12px; font-size: 0.78rem; font-weight: 600; }
  .ok { background: #d4edda; color: #155724; }
  .err { background: #f8d7da; color: #721c24; }
  .warn { background: #fff3cd; color: #856404; }
  .info { background: #d1ecf1; color: #0c5460; }
  code { background: #f4f4f4; padding: 2px 6px; border-radius: 4px; font-size: 0.82rem; font-family: monospace; }
  .btn { display: inline-block; padding: 10px 22px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; text-decoration: none; border: none; cursor: pointer; margin-right: 8px; }
  .btn-red { background: #e74c3c; color: #fff; }
  .btn-blue { background: #3498db; color: #fff; }
  .btn:hover { opacity: 0.85; }
  .log { list-style: none; max-height: 320px; overflow-y: auto; font-size: 0.82rem; border: 1px solid #eee; border-radius: 6px; }
  .log li { padding: 5px 10px; border-bottom: 1px solid #f5f5f5; }
  .log li:last-child { border-bottom: none; }
  .l-ok { color: #155724; background: #f0fff4; }
  .l-skip { color: #856404; background: #fffdf0; }
  .l-error { color: #721c24; background: #fff5f5; }
  .warn-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 12px 16px; margin-bottom: 18px; font-size: 0.87rem; color: #856404; }
  .note { background: #f8f9fa; border-radius: 6px; padding: 12px; font-size: 0.83rem; color: #666; line-height: 1.6; margin-top: 14px; }
</style>
</head>
<body>
<div class="wrap">

  <h1>🔧 Fix Foto Hosting</h1>
  <p class="sub">Perbaikan foto tidak muncul setelah upload dari admin</p>

  <div class="warn-box">
    ⚠️ <strong>PENTING:</strong> Hapus file ini setelah selesai! Jangan biarkan ada di server.
  </div>

  <?php if ($action === 'result'): ?>

  <!-- Hasil Copy -->
  <div class="card">
    <h2>📋 Hasil Copy Foto</h2>
    <?php
      $ok   = count(array_filter($copyResults, fn($r) => $r['status'] === 'ok'));
      $skip = count(array_filter($copyResults, fn($r) => $r['status'] === 'skip'));
      $err  = count(array_filter($copyResults, fn($r) => $r['status'] === 'error'));
    ?>
    <div class="row"><span class="lbl">Berhasil disalin</span><span class="val"><span class="badge ok"><?= $ok ?> file</span></span></div>
    <div class="row"><span class="lbl">Dilewati (sudah ada)</span><span class="val"><span class="badge warn"><?= $skip ?> file</span></span></div>
    <div class="row"><span class="lbl">Gagal</span><span class="val"><span class="badge <?= $err > 0 ? 'err' : 'ok' ?>"><?= $err ?> file</span></span></div>

    <?php if (!empty($copyResults)): ?>
    <br>
    <ul class="log">
      <?php foreach ($copyResults as $r): ?>
      <li class="l-<?= $r['status'] ?>"><?= htmlspecialchars($r['msg']) ?></li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <br>
    <?php if ($ok > 0): ?>
    <div style="background:#d4edda;border-radius:6px;padding:12px;font-size:0.87rem;color:#155724;margin-bottom:12px">
      ✅ <strong><?= $ok ?> foto berhasil disalin.</strong> Coba refresh halaman website kamu sekarang.
    </div>
    <?php endif; ?>
    <?php if ($err > 0): ?>
    <div style="background:#f8d7da;border-radius:6px;padding:12px;font-size:0.87rem;color:#721c24;margin-bottom:12px">
      ❌ <strong><?= $err ?> foto gagal disalin.</strong> Kemungkinan masalah permission folder. Hubungi hosting provider.
    </div>
    <?php endif; ?>

    <a href="?" class="btn btn-blue">← Kembali ke Diagnosa</a>
  </div>

  <?php else: ?>

  <!-- Diagnosa -->
  <div class="card">
    <h2>🔍 Informasi Server</h2>
    <div class="row"><span class="lbl">Lokasi Script</span><span class="val"><code><?= htmlspecialchars(__FILE__) ?></code></span></div>
    <div class="row"><span class="lbl">Folder Public Hosting</span><span class="val"><code><?= htmlspecialchars($publicDir) ?></code></span></div>
    <div class="row"><span class="lbl">PHP Version</span><span class="val"><?= PHP_VERSION ?></span></div>
  </div>

  <div class="card">
    <h2>📁 Status Folder</h2>

    <div class="row">
      <span class="lbl">Laravel Root</span>
      <span class="val">
        <span class="badge <?= $laravelRoot ? 'ok' : 'err' ?>"><?= $laravelRoot ? '✅ Ditemukan' : '❌ Tidak Ditemukan' ?></span>
        <?php if ($laravelRoot): ?><br><code><?= htmlspecialchars($laravelRoot) ?></code><?php endif; ?>
      </span>
    </div>

    <div class="row">
      <span class="lbl">Sumber foto <code>storage/app/public</code></span>
      <span class="val">
        <span class="badge <?= $srcImages && is_dir($srcImages) ? 'ok' : 'err' ?>">
          <?= $srcImages && is_dir($srcImages) ? '✅ Ada' : '❌ Tidak Ada' ?>
        </span>
        <?php if ($srcImages): ?><br><code><?= htmlspecialchars($srcImages) ?></code><?php endif; ?>
      </span>
    </div>

    <div class="row">
      <span class="lbl">Total foto di sumber</span>
      <span class="val"><span class="badge <?= count($srcFiles) > 0 ? 'ok' : 'warn' ?>"><?= count($srcFiles) ?> file</span></span>
    </div>

    <div class="row">
      <span class="lbl">Tujuan <code>storage</code> di hosting</span>
      <span class="val">
        <span class="badge <?= is_dir($dstImages) ? 'ok' : 'warn' ?>"><?= is_dir($dstImages) ? '✅ Ada' : '⚠️ Belum Ada' ?></span>
        <br><code><?= htmlspecialchars($dstImages) ?></code>
      </span>
    </div>

    <div class="row">
      <span class="lbl">Foto sudah di hosting</span>
      <span class="val"><span class="badge <?= count($dstFiles) > 0 ? 'ok' : 'warn' ?>"><?= count($dstFiles) ?> file</span></span>
    </div>

    <div class="row">
      <span class="lbl">Foto belum tersalin</span>
      <span class="val">
        <span class="badge <?= $missing > 0 ? 'err' : 'ok' ?>">
          <?= max(0, $missing) ?> file <?= $missing > 0 ? '← ini penyebab foto tidak muncul' : '' ?>
        </span>
      </span>
    </div>
  </div>

  <div class="card">
    <h2>🛠️ Aksi Perbaikan</h2>

    <?php if (!$laravelRoot): ?>
    <div style="color:#721c24;background:#f8d7da;padding:12px;border-radius:6px;font-size:0.87rem">
      ❌ Laravel root tidak ditemukan. Pastikan script ini diupload ke folder public website (bukan folder Laravel).
    </div>
    <?php elseif (!$srcImages || !is_dir($srcImages)): ?>
    <div style="color:#721c24;background:#f8d7da;padding:12px;border-radius:6px;font-size:0.87rem">
      ❌ Folder <code>storage/app/public</code> tidak ditemukan di Laravel root. Pastikan sudah ada foto yang diupload lewat admin.
    </div>
    <?php else: ?>
    <p style="font-size:0.87rem;color:#666;margin-bottom:14px">
      Script akan menyalin semua foto dari <code>storage/app/public</code> di Laravel ke folder <code>storage</code> di public hosting.<br>
      File yang sudah ada akan dilewati (tidak ditimpa).
    </p>
    <a href="?action=copy" class="btn btn-red"
       onclick="return confirm('Copy semua foto ke folder public hosting?')">
      📋 Copy Foto Sekarang
    </a>
    <a href="?" class="btn btn-blue">🔄 Refresh</a>

    <div class="note">
      <strong>Catatan:</strong> Script ini perlu dijalankan ulang setiap kali ada foto baru yang tidak muncul.<br>
      Untuk solusi permanen, minta developer pasang auto-sync di controller atau aktifkan symlink di hosting.
    </div>
    <?php endif; ?>
  </div>

  <?php endif; ?>

  <div style="text-align:center;color:#ccc;font-size:0.78rem;padding:16px 0">
    fix_foto_teman.php — Hapus file ini setelah selesai!
  </div>

</div>
</body>
</html>
