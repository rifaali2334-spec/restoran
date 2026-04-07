@echo off
echo ========================================
echo   FIX GALLERY - TESTING DI LOCAL
echo ========================================
echo.

echo [1/4] Menjalankan Migration...
php artisan migrate
echo.

echo [2/4] Membuat Symbolic Link Storage...
php artisan storage:link
echo.

echo [3/4] Clear Cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo [4/4] Membuat Folder Gallery di Storage...
if not exist "storage\app\public\gallery" mkdir "storage\app\public\gallery"
echo.

echo ========================================
echo   SELESAI!
echo ========================================
echo.
echo Langkah selanjutnya:
echo 1. Jalankan server: php artisan serve
echo 2. Login ke admin panel
echo 3. Coba tambah gallery baru
echo 4. Cek apakah foto muncul
echo.
echo Jika foto lama tidak muncul:
echo - Copy manual foto dari public\images\gallery
echo - Paste ke storage\app\public\gallery
echo.
pause
