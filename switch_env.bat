@echo off
echo ========================================
echo   SWITCH ENVIRONMENT
echo ========================================
echo.
echo Pilih environment:
echo 1. LOCAL (development)
echo 2. HOSTING (production)
echo.
set /p choice="Pilih (1/2): "

if "%choice%"=="1" (
    copy .env.local .env
    echo.
    echo ✅ Switched to LOCAL environment
    echo Database: healthy
    echo URL: http://localhost:8000
) else if "%choice%"=="2" (
    copy .env.hosting .env
    echo.
    echo ✅ Switched to HOSTING environment
    echo Database: aff100_db_baknus_26_naufal
    echo URL: https://naufal.baknus.26.cyberwarrior.co.id
) else (
    echo.
    echo ❌ Pilihan tidak valid!
)

echo.
echo Jangan lupa clear cache:
echo php artisan config:clear
echo.
pause
