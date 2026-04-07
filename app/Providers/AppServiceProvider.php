<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto-create storage symlink jika belum ada
        $publicStorage = public_path('storage');
        $storagePublic = storage_path('app/public');
        
        if (!file_exists($publicStorage) && is_dir($storagePublic)) {
            try {
                symlink($storagePublic, $publicStorage);
            } catch (\Exception $e) {
                // Jika symlink gagal (hosting tidak support), copy manual
                if (!is_dir($publicStorage)) {
                    mkdir($publicStorage, 0755, true);
                }
            }
        }
    }
}
