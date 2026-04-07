<?php

namespace App\Traits;

trait AutoSyncFoto
{
    public function syncFotoKePublic(): void
    {
        $source = storage_path('app/public');
        $target = $this->detectPublicStorage();

        if (!$target || !is_dir($source)) return;

        $this->copyRekursif($source, $target);
    }

    private function detectPublicStorage(): ?string
    {
        $publicPath = public_path();

        // Cek apakah public folder berbeda dari Laravel root (hosting split)
        $candidates = [
            $publicPath . '/storage',                          // symlink normal
            dirname($publicPath) . '/aditya/storage',         // hosting split: public_html/aditya
            dirname($publicPath) . '/public/storage',         // hosting split: public_html/public
        ];

        foreach ($candidates as $path) {
            if (is_dir($path)) return $path;
        }

        // Fallback: buat folder storage di public
        @mkdir($publicPath . '/storage', 0755, true);
        return $publicPath . '/storage';
    }

    private function copyRekursif(string $src, string $dst): void
    {
        if (!is_dir($dst)) @mkdir($dst, 0755, true);

        foreach (scandir($src) as $item) {
            if ($item === '.' || $item === '..') continue;

            $srcPath = $src . '/' . $item;
            $dstPath = $dst . '/' . $item;

            if (is_dir($srcPath)) {
                $this->copyRekursif($srcPath, $dstPath);
            } elseif (!file_exists($dstPath) || filemtime($srcPath) > filemtime($dstPath)) {
                @copy($srcPath, $dstPath);
            }
        }
    }
}
