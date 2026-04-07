<?php
/**
 * SCRIPT FIX FOTO HOSTING UNIVERSAL
 * ===================================
 * Script ini untuk mengatasi masalah foto tidak muncul di hosting
 * setelah upload/update data dari panel admin Laravel.
 *
 * CARA PAKAI:
 * 1. Upload file ini ke folder PUBLIC website kamu di hosting
 *    (folder yang bisa diakses browser, biasanya public_html/namafolder/)
 * 2. Akses via browser: https://domainmu.com/fix_foto_hosting_universal.php
 * 3. Ikuti instruksi yang muncul
 * 4. HAPUS file ini setelah selesai (demi keamanan!)
 *
 * MASALAH YANG DISELESAIKAN:
 * - Foto tidak muncul setelah upload dari admin
 * - Storage symlink tidak berfungsi di hosting shared
 * - Struktur folder hosting berbeda dari standar Laravel
 */

// ============================================================
// KONFIGURASI - SESUAIKAN DENGAN HOSTING KAMU
// ============================================================

// Nama subfolder project kamu di public_html (kosongkan jika langsung di public_html)
// Contoh: 'myapp', 'naufal', 'project' — atau '' jika langsung di public_html
define('PROJECT_SUBFOLDER', '');

// ============================================================
// JANGAN UBAH KODE DI BAWAH INI
// ============================================================

error_reporting(E_ALL);
ini_set('display_errors', 0); // Sembunyikan error PHP mentah

$action = $_GET['action'] ?? 'diagnosa';
$results = [];

// ---- Helper Functions ----

function findLaravelRoot(): ?string
{
    // Cari folder yang berisi artisan + composer.json (tanda root Laravel)
    $candidates = [];

    // Dari lokasi script ini, naik ke atas
    $dir = __DIR__;
    for ($i = 0; $i < 6; $i++) {
        if (file_exists($dir . '/artisan') && file_exists($dir . '/composer.json')) {
            $candidates[] = $dir;
        }
        $parent = dirname($dir);
        if ($parent === $dir) break;
        $dir = $parent;
    }

    // Cari juga di sibling folder (struktur hosting split)
    $publicDir = __DIR__;
    $parentDir = dirname($publicDir);
    $siblings = glob($parentDir . '/*', GLOB_ONLYDIR);
    foreach ($siblings as $sibling) {
        if (file_exists($sibling . '/artisan') && file_exists($sibling . '/composer.json')) {
            if (!in_array($sibling, $candidates)) {
                $candidates[] = $sibling;
            }
        }
    }

    // Cari di /home/*/domains/*/
    $homeDirs = glob('/home/*/domains/*/public_html', GLOB_ONLYDIR);
    foreach ($homeDirs as $homeDir) {
        $parentOfPublic = dirname($homeDir);
        $subfolders = glob($parentOfPublic . '/*', GLOB_ONLYDIR);
        foreach ($subfolders as $sub) {
            if (file_exists($sub . '/artisan') && file_exists($sub . '/composer.json')) {
                if (!in_array($sub, $candidates)) {
                    $candidates[] = $sub;
                }
            }
        }
    }

    return $candidates[0] ?? null;
}

function findStoragePublicPath(string $laravelRoot): string
{
    return $laravelRoot . '/storage/app/public';
}

function findPublicStoragePath(): string
{
    // Folder public/storage yang seharusnya jadi symlink atau folder nyata
    return __DIR__ . '/storage';
}

function scanUploadedFiles(string $storagePath): array
{
    $files = [];
    if (!is_dir($storagePath)) return $files;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($storagePath, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $files[] = $file->getPathname();
            }
        }
    }
    return $files;
}

function copyFilesRecursive(string $src, string $dst): array
{
    $log = [];
    if (!is_dir($src)) {
        return [['status' => 'error', 'msg' => "Source tidak ditemukan: $src"]];
    }

    if (!is_dir($dst)) {
        if (!mkdir($dst, 0755, true)) {
            return [['status' => 'error', 'msg' => "Gagal buat folder: $dst"]];
        }
        $log[] = ['status' => 'ok', 'msg' => "Folder dibuat: $dst"];
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $subPath = $iterator->getSubPathname();
        $dstPath = $dst . '/' . $subPath;

        if ($item->isDir()) {
            if (!is_dir($dstPath)) {
                mkdir($dstPath, 0755, true);
            }
        } else {
            $ext = strtolower($item->getExtension());
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx'])) {
                if (!file_exists($dstPath) || filemtime($item->getPathname()) > filemtime($dstPath)) {
                    if (copy($item->getPathname(), $dstPath)) {
                        $log[] = ['status' => 'ok', 'msg' => "Disalin: $subPath"];
                    } else {
                        $log[] = ['status' => 'error', 'msg' => "Gagal salin: $subPath"];
                    }
                } else {
                    $log[] = ['status' => 'skip', 'msg' => "Sudah ada (skip): $subPath"];
                }
            }
        }
    }

    return $log;
}

function tryCreateSymlink(string $target, string $link): array
{
    if (is_link($link)) {
        return ['status' => 'exists', 'msg' => "Symlink sudah ada: $link → " . readlink($link)];
    }
    if (is_dir($link)) {
        return ['status' => 'exists', 'msg' => "Folder sudah ada (bukan symlink): $link"];
    }
    if (@symlink($target, $link)) {
        return ['status' => 'ok', 'msg' => "Symlink berhasil dibuat: $link → $target"];
    }
    return ['status' => 'error', 'msg' => "Symlink gagal (hosting mungkin tidak support). Gunakan Copy Files."];
}

// ---- Diagnosa ----

function runDiagnosa(): array
{
    $info = [];

    $info['script_location'] = __FILE__;
    $info['public_dir'] = __DIR__;
    $info['php_version'] = PHP_VERSION;
    $info['server_software'] = $_SERVER['SERVER_SOFTWARE'] ?? 'unknown';

    $laravelRoot = findLaravelRoot();
    $info['laravel_root'] = $laravelRoot ?? '❌ TIDAK DITEMUKAN';
    $info['laravel_root_found'] = $laravelRoot !== null;

    if ($laravelRoot) {
        $storagePath = findStoragePublicPath($laravelRoot);
        $info['storage_app_public'] = $storagePath;
        $info['storage_app_public_exists'] = is_dir($storagePath);

        $uploadedFiles = scanUploadedFiles($storagePath);
        $info['total_uploaded_files'] = count($uploadedFiles);
        $info['sample_files'] = array_slice($uploadedFiles, 0, 5);
    }

    $publicStorage = findPublicStoragePath();
    $info['public_storage_path'] = $publicStorage;
    $info['public_storage_is_symlink'] = is_link($publicStorage);
    $info['public_storage_is_dir'] = is_dir($publicStorage);
    $info['public_storage_exists'] = is_link($publicStorage) || is_dir($publicStorage);

    if (is_dir($publicStorage)) {
        $filesInPublicStorage = scanUploadedFiles($publicStorage);
        $info['files_in_public_storage'] = count($filesInPublicStorage);
    } else {
        $info['files_in_public_storage'] = 0;
    }

    // Cek apakah foto bisa diakses via URL
    $info['base_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
        . '://' . ($_SERVER['HTTP_HOST'] ?? 'unknown')
        . dirname($_SERVER['REQUEST_URI'] ?? '/');

    // Diagnosa masalah
    $problems = [];
    $solutions = [];

    if (!$info['laravel_root_found']) {
        $problems[] = '❌ Root Laravel tidak ditemukan otomatis';
        $solutions[] = 'Pastikan script ini ada di folder public website (bukan di dalam folder Laravel)';
    }

    if ($info['laravel_root_found'] && !$info['storage_app_public_exists']) {
        $problems[] = '❌ Folder storage/app/public tidak ada di Laravel root';
        $solutions[] = 'Jalankan: php artisan storage:link di server, atau buat folder storage/app/public secara manual';
    }

    if (!$info['public_storage_exists']) {
        $problems[] = '❌ Folder public/storage tidak ada (symlink atau folder)';
        $solutions[] = 'Gunakan tombol "Buat Symlink" atau "Copy Files" di bawah';
    } elseif ($info['public_storage_is_dir'] && !$info['public_storage_is_symlink']) {
        if ($info['files_in_public_storage'] === 0 && isset($info['total_uploaded_files']) && $info['total_uploaded_files'] > 0) {
            $problems[] = '⚠️ Folder public/storage ada tapi kosong, padahal ada ' . $info['total_uploaded_files'] . ' file di storage/app/public';
            $solutions[] = 'Gunakan tombol "Copy Files" untuk menyalin foto ke public/storage';
        }
    }

    if (empty($problems)) {
        $problems[] = '✅ Tidak ada masalah terdeteksi';
        if (isset($info['total_uploaded_files']) && $info['total_uploaded_files'] > 0 && $info['files_in_public_storage'] > 0) {
            $solutions[] = 'Foto sudah ada dan seharusnya bisa diakses. Coba hard refresh browser (Ctrl+Shift+R)';
        }
    }

    $info['problems'] = $problems;
    $info['solutions'] = $solutions;

    return $info;
}

// ---- Action Handler ----

$message = '';
$messageType = '';

if ($action === 'fix_symlink') {
    $laravelRoot = findLaravelRoot();
    if (!$laravelRoot) {
        $message = 'Laravel root tidak ditemukan. Tidak bisa buat symlink.';
        $messageType = 'error';
    } else {
        $target = findStoragePublicPath($laravelRoot);
        $link = findPublicStoragePath();
        $result = tryCreateSymlink($target, $link);
        $message = $result['msg'];
        $messageType = $result['status'] === 'ok' ? 'success' : ($result['status'] === 'exists' ? 'info' : 'error');
    }
    $action = 'diagnosa';
}

if ($action === 'fix_copy') {
    $laravelRoot = findLaravelRoot();
    if (!$laravelRoot) {
        $message = 'Laravel root tidak ditemukan. Tidak bisa copy files.';
        $messageType = 'error';
        $action = 'diagnosa';
    } else {
        $src = findStoragePublicPath($laravelRoot);
        $dst = findPublicStoragePath();
        $results = copyFilesRecursive($src, $dst);
        $action = 'show_copy_result';
    }
}

$diagnosa = runDiagnosa();

// ---- HTML Output ----
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fix Foto Hosting - Laravel</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; color: #333; padding: 20px; }
  .container { max-width: 860px; margin: 0 auto; }
  h1 { color: #e74c3c; margin-bottom: 4px; font-size: 1.6rem; }
  .subtitle { color: #666; margin-bottom: 24px; font-size: 0.9rem; }
  .card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
  .card h2 { font-size: 1rem; font-weight: 600; margin-bottom: 14px; color: #444; border-bottom: 2px solid #f0f2f5; padding-bottom: 8px; }
  .row { display: flex; justify-content: space-between; align-items: flex-start; padding: 7px 0; border-bottom: 1px solid #f5f5f5; font-size: 0.88rem; gap: 10px; }
  .row:last-child { border-bottom: none; }
  .label { color: #888; min-width: 200px; flex-shrink: 0; }
  .value { color: #333; word-break: break-all; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.78rem; font-weight: 600; }
  .badge-ok { background: #d4edda; color: #155724; }
  .badge-error { background: #f8d7da; color: #721c24; }
  .badge-warn { background: #fff3cd; color: #856404; }
  .badge-info { background: #d1ecf1; color: #0c5460; }
  .problem-list { list-style: none; }
  .problem-list li { padding: 6px 0; font-size: 0.9rem; border-bottom: 1px solid #f5f5f5; }
  .problem-list li:last-child { border-bottom: none; }
  .solution-list { list-style: none; }
  .solution-list li { padding: 6px 0 6px 20px; font-size: 0.88rem; color: #555; position: relative; }
  .solution-list li::before { content: '→'; position: absolute; left: 0; color: #3498db; }
  .btn { display: inline-block; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; cursor: pointer; border: none; margin-right: 10px; margin-bottom: 8px; }
  .btn-primary { background: #3498db; color: #fff; }
  .btn-success { background: #27ae60; color: #fff; }
  .btn-warning { background: #f39c12; color: #fff; }
  .btn-danger { background: #e74c3c; color: #fff; }
  .btn:hover { opacity: 0.88; }
  .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 0.9rem; }
  .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
  .alert-error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
  .alert-info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
  .log-list { list-style: none; max-height: 300px; overflow-y: auto; font-size: 0.82rem; }
  .log-list li { padding: 4px 8px; border-bottom: 1px solid #f5f5f5; }
  .log-ok { color: #155724; background: #f0fff4; }
  .log-error { color: #721c24; background: #fff5f5; }
  .log-skip { color: #856404; background: #fffdf0; }
  .warning-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; font-size: 0.88rem; color: #856404; }
  code { background: #f4f4f4; padding: 2px 6px; border-radius: 4px; font-size: 0.85rem; }
  .path-display { font-family: monospace; font-size: 0.82rem; background: #f8f9fa; padding: 2px 6px; border-radius: 4px; }
</style>
</head>
<body>
<div class="container">

  <h1>🔧 Fix Foto Hosting - Laravel</h1>
  <p class="subtitle">Script diagnosa & perbaikan foto tidak muncul di hosting</p>

  <div class="warning-box">
    ⚠️ <strong>PENTING:</strong> Hapus file ini setelah selesai digunakan! Jangan biarkan file ini ada di server production.
  </div>

  <?php if ($message): ?>
  <div class="alert alert-<?= $messageType === 'success' ? 'success' : ($messageType === 'info' ? 'info' : 'error') ?>">
    <?= htmlspecialchars($message) ?>
  </div>
  <?php endif; ?>

  <?php if ($action === 'show_copy_result'): ?>
  <!-- Hasil Copy Files -->
  <div class="card">
    <h2>📋 Hasil Copy Files</h2>
    <?php
      $ok = count(array_filter($results, fn($r) => $r['status'] === 'ok'));
      $skip = count(array_filter($results, fn($r) => $r['status'] === 'skip'));
      $err = count(array_filter($results, fn($r) => $r['status'] === 'error'));
    ?>
    <div class="row">
      <span class="label">Berhasil disalin</span>
      <span class="value"><span class="badge badge-ok"><?= $ok ?> file</span></span>
    </div>
    <div class="row">
      <span class="label">Dilewati (sudah ada)</span>
      <span class="value"><span class="badge badge-warn"><?= $skip ?> file</span></span>
    </div>
    <div class="row">
      <span class="label">Gagal</span>
      <span class="value"><span class="badge badge-error"><?= $err ?> file</span></span>
    </div>
    <?php if (!empty($results)): ?>
    <br>
    <ul class="log-list">
      <?php foreach ($results as $r): ?>
      <li class="log-<?= $r['status'] ?>"><?= htmlspecialchars($r['msg']) ?></li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <br>
    <a href="?" class="btn btn-primary">← Kembali ke Diagnosa</a>
  </div>

  <?php else: ?>
  <!-- Diagnosa -->

  <div class="card">
    <h2>🔍 Informasi Server</h2>
    <div class="row">
      <span class="label">Lokasi Script</span>
      <span class="value path-display"><?= htmlspecialchars($diagnosa['script_location']) ?></span>
    </div>
    <div class="row">
      <span class="label">Folder Public</span>
      <span class="value path-display"><?= htmlspecialchars($diagnosa['public_dir']) ?></span>
    </div>
    <div class="row">
      <span class="label">PHP Version</span>
      <span class="value"><?= htmlspecialchars($diagnosa['php_version']) ?></span>
    </div>
    <div class="row">
      <span class="label">Server</span>
      <span class="value"><?= htmlspecialchars($diagnosa['server_software']) ?></span>
    </div>
    <div class="row">
      <span class="label">Base URL</span>
      <span class="value"><?= htmlspecialchars($diagnosa['base_url']) ?></span>
    </div>
  </div>

  <div class="card">
    <h2>📁 Status Folder Laravel</h2>
    <div class="row">
      <span class="label">Laravel Root</span>
      <span class="value">
        <span class="badge <?= $diagnosa['laravel_root_found'] ? 'badge-ok' : 'badge-error' ?>">
          <?= $diagnosa['laravel_root_found'] ? '✅ Ditemukan' : '❌ Tidak Ditemukan' ?>
        </span>
        <?php if ($diagnosa['laravel_root_found']): ?>
        <br><span class="path-display"><?= htmlspecialchars($diagnosa['laravel_root']) ?></span>
        <?php endif; ?>
      </span>
    </div>

    <?php if ($diagnosa['laravel_root_found']): ?>
    <div class="row">
      <span class="label">storage/app/public</span>
      <span class="value">
        <span class="badge <?= $diagnosa['storage_app_public_exists'] ? 'badge-ok' : 'badge-error' ?>">
          <?= $diagnosa['storage_app_public_exists'] ? '✅ Ada' : '❌ Tidak Ada' ?>
        </span>
        <br><span class="path-display"><?= htmlspecialchars($diagnosa['storage_app_public']) ?></span>
      </span>
    </div>
    <div class="row">
      <span class="label">File foto di storage</span>
      <span class="value">
        <span class="badge <?= $diagnosa['total_uploaded_files'] > 0 ? 'badge-ok' : 'badge-warn' ?>">
          <?= $diagnosa['total_uploaded_files'] ?> file
        </span>
        <?php if (!empty($diagnosa['sample_files'])): ?>
        <br>
        <?php foreach ($diagnosa['sample_files'] as $f): ?>
          <small class="path-display"><?= htmlspecialchars(basename($f)) ?></small><br>
        <?php endforeach; ?>
        <?php if ($diagnosa['total_uploaded_files'] > 5): ?>
          <small style="color:#888">... dan <?= $diagnosa['total_uploaded_files'] - 5 ?> file lainnya</small>
        <?php endif; ?>
        <?php endif; ?>
      </span>
    </div>
    <?php endif; ?>

    <div class="row">
      <span class="label">public/storage</span>
      <span class="value">
        <?php if ($diagnosa['public_storage_is_symlink']): ?>
          <span class="badge badge-ok">✅ Symlink aktif</span>
        <?php elseif ($diagnosa['public_storage_is_dir']): ?>
          <span class="badge badge-info">📁 Folder biasa (bukan symlink)</span>
        <?php else: ?>
          <span class="badge badge-error">❌ Tidak ada</span>
        <?php endif; ?>
        <br><span class="path-display"><?= htmlspecialchars($diagnosa['public_storage_path']) ?></span>
      </span>
    </div>
    <div class="row">
      <span class="label">File foto di public/storage</span>
      <span class="value">
        <span class="badge <?= $diagnosa['files_in_public_storage'] > 0 ? 'badge-ok' : 'badge-warn' ?>">
          <?= $diagnosa['files_in_public_storage'] ?> file
        </span>
      </span>
    </div>
  </div>

  <div class="card">
    <h2>⚠️ Masalah Terdeteksi</h2>
    <ul class="problem-list">
      <?php foreach ($diagnosa['problems'] as $p): ?>
      <li><?= htmlspecialchars($p) ?></li>
      <?php endforeach; ?>
    </ul>
    <?php if (!empty($diagnosa['solutions'])): ?>
    <br>
    <strong style="font-size:0.88rem;color:#555">Solusi yang disarankan:</strong>
    <ul class="solution-list">
      <?php foreach ($diagnosa['solutions'] as $s): ?>
      <li><?= htmlspecialchars($s) ?></li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2>🛠️ Aksi Perbaikan</h2>
    <p style="font-size:0.88rem;color:#666;margin-bottom:14px">
      Pilih salah satu metode di bawah. Coba <strong>Buat Symlink</strong> dulu, jika gagal gunakan <strong>Copy Files</strong>.
    </p>

    <a href="?action=fix_symlink" class="btn btn-success"
       onclick="return confirm('Buat symlink public/storage → storage/app/public?')">
      🔗 Buat Symlink
    </a>

    <a href="?action=fix_copy" class="btn btn-warning"
       onclick="return confirm('Copy semua foto dari storage/app/public ke public/storage? Ini aman, file yang sudah ada akan dilewati.')">
      📋 Copy Files (Tanpa Symlink)
    </a>

    <a href="?" class="btn btn-primary">🔄 Refresh Diagnosa</a>

    <br><br>
    <div style="background:#f8f9fa;border-radius:6px;padding:12px;font-size:0.83rem;color:#555">
      <strong>Kapan pakai apa?</strong><br>
      • <strong>Buat Symlink</strong>: Foto otomatis muncul setiap kali upload baru. Tapi tidak semua hosting support.<br>
      • <strong>Copy Files</strong>: Lebih kompatibel. Tapi perlu dijalankan ulang setiap kali ada foto baru yang tidak muncul.
    </div>
  </div>

  <div class="card">
    <h2>📖 Penjelasan Masalah</h2>
    <div style="font-size:0.88rem;color:#555;line-height:1.7">
      <p>Laravel menyimpan file upload di <code>storage/app/public/</code>, tapi website hanya bisa akses file dari folder <code>public/</code>.</p>
      <br>
      <p>Normalnya, Laravel membuat <strong>symlink</strong> dari <code>public/storage</code> ke <code>storage/app/public</code> via perintah <code>php artisan storage:link</code>.</p>
      <br>
      <p>Di hosting shared, symlink sering tidak berfungsi atau tidak dibuat. Solusinya adalah <strong>copy file</strong> dari storage ke public/storage secara manual (itulah fungsi script ini).</p>
      <br>
      <p>Jika hosting kamu memiliki struktur folder yang berbeda (misal Laravel root dan public folder terpisah), script ini akan mencoba mendeteksinya secara otomatis.</p>
    </div>
  </div>

  <?php endif; ?>

  <div style="text-align:center;color:#aaa;font-size:0.8rem;margin-top:20px;padding-bottom:20px">
    fix_foto_hosting_universal.php — Hapus file ini setelah selesai digunakan!
  </div>

</div>
</body>
</html>
