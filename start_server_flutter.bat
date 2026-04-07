@echo off
cls
echo ============================================
echo   LARAVEL API SERVER - FLUTTER INTEGRATION
echo ============================================
echo.

echo [1/3] Checking Database...
php test_api_direct.php
echo.

echo ============================================
echo [2/3] Starting Laravel Server...
echo ============================================
echo.
echo Server akan berjalan di: http://127.0.0.1:8000
echo.
echo PENTING! Gunakan URL ini di Flutter:
echo.
echo   - Android Emulator : http://10.0.2.2:8000/api/dashboard/statistics
echo   - iOS Simulator    : http://127.0.0.1:8000/api/dashboard/statistics  
echo   - Real Device      : http://[IP_KOMPUTER]:8000/api/dashboard/statistics
echo.
echo ============================================
echo [3/3] Test API di browser:
echo   http://127.0.0.1:8000/api/dashboard/statistics
echo ============================================
echo.
echo Tekan Ctrl+C untuk stop server
echo.

php artisan serve
