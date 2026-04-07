@echo off
echo ========================================
echo   FIX SEMUA FOTO - TESTING DI LOCAL
echo ========================================
echo.

echo [1/5] Menjalankan Migration...
php artisan migrate
if errorlevel 1 (
    echo GAGAL! Cek error di atas.
    pause
    exit /b 1
)
echo.

echo [2/5] Membuat Symbolic Link Storage...
php artisan storage:link
if errorlevel 1 (
    echo WARNING: Symbolic link mungkin sudah ada.
)
echo.

echo [3/5] Clear Cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo [4/5] Membuat Folder Gallery di Storage...
if not exist "storage\app\public\gallery" (
    mkdir "storage\app\public\gallery"
    echo Folder gallery berhasil dibuat!
) else (
    echo Folder gallery sudah ada.
)
echo.

echo [5/5] Memindahkan Foto Gallery Lama (jika ada)...
if exist "public\images\gallery\*.*" (
    echo Ditemukan foto di public\images\gallery\
    echo Memindahkan ke storage\app\public\gallery\...
    xcopy "public\images\gallery\*.*" "storage\app\public\gallery\" /Y /I
    echo Foto berhasil dipindahkan!
) else (
    echo Tidak ada foto lama untuk dipindahkan.
)
echo.

echo ========================================
echo   SELESAI!
echo ========================================
echo.
echo LANGKAH SELANJUTNYA:
echo.
echo 1. Jalankan server: php artisan serve
echo 2. Buka browser: http://localhost:8000
echo 3. Login ke admin panel
echo 4. Test tambah foto di:
echo    - Gallery (menu Galleries)
echo    - News (menu News)
echo    - Content (menu Contents)
echo    - Tentang (menu Tentang)
echo 5. Cek apakah foto muncul di:
echo    - Admin panel
echo    - Halaman publik (home, galeri, berita, tentang)
echo.
echo Jika semua foto muncul, siap deploy ke hosting!
echo Ikuti panduan di PANDUAN_FIX_GALLERY.md
echo.
pause
