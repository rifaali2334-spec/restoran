@echo off
echo ========================================
echo   SETUP DATABASE LOCAL
echo ========================================
echo.

echo LANGKAH-LANGKAH:
echo.
echo 1. Buka phpMyAdmin: http://localhost/phpmyadmin
echo 2. Klik "New" untuk buat database baru
echo 3. Nama database: healthy
echo 4. Collation: utf8mb4_unicode_ci
echo 5. Klik "Create"
echo.
echo Setelah database dibuat, tekan Enter untuk lanjut...
pause
echo.

echo [1/3] Clear cache...
php artisan config:clear
php artisan cache:clear
echo.

echo [2/3] Jalankan migration...
php artisan migrate
if errorlevel 1 (
    echo.
    echo ========================================
    echo   ERROR!
    echo ========================================
    echo.
    echo Kemungkinan:
    echo 1. Database "healthy" belum dibuat
    echo 2. MySQL belum jalan (start XAMPP)
    echo 3. Username/password salah di .env
    echo.
    echo Cek dan coba lagi!
    pause
    exit /b 1
)
echo.

echo [3/3] Jalankan seeder (opsional)...
php artisan db:seed --class=AdminSeeder
echo.

echo ========================================
echo   SELESAI!
echo ========================================
echo.
echo Database local sudah siap!
echo.
echo Login Admin:
echo Email: admin@admin.com
echo Password: admin123
echo.
echo Jalankan server: php artisan serve
echo Lalu buka: http://localhost:8000
echo.
pause
