@echo off
echo ========================================
echo Fix Database Healthy - Corrupt Tables
echo ========================================
echo.

echo Step 1: Stopping Apache and MySQL...
cd C:\xampp
xampp_stop.exe
timeout /t 3 /nobreak >nul

echo.
echo Step 2: Deleting corrupt database files...
cd C:\xampp\mysql\data\healthy
del /Q *.ibd
del /Q *.frm
del /Q db.opt

echo.
echo Step 3: Starting Apache and MySQL...
cd C:\xampp
xampp_start.exe
timeout /t 5 /nobreak >nul

echo.
echo Step 4: Recreating database...
cd C:\xampp\mysql\bin
mysql -u root -e "DROP DATABASE IF EXISTS healthy; CREATE DATABASE healthy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo.
echo Step 5: Running migrations...
cd /d "c:\Users\Moch.Naufal zaky\healthy"
php artisan migrate

echo.
echo ========================================
echo Done! Database has been fixed.
echo ========================================
pause
