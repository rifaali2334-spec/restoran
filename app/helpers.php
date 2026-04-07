<?php

if (!function_exists('storage_asset')) {
    /**
     * Generate asset URL for storage files
     * Handles different paths for local vs production
     */
    function storage_asset($path)
    {
        // Jika di production/hosting, akses langsung ke folder healthy
        if (config('app.env') === 'production') {
            return asset('healthy/storage/app/public/' . $path);
        }
        
        // Jika di local, pakai cara biasa
        return asset('storage/' . $path);
    }
}
