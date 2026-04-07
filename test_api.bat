@echo off
echo ========================================
echo Testing Laravel API Connection
echo ========================================
echo.

echo 1. Testing Database Connection...
php artisan db:show
echo.

echo 2. Checking if data exists...
php artisan tinker --execute="echo 'News: ' . App\Models\News::count(); echo PHP_EOL; echo 'Gallery: ' . App\Models\Gallery::count(); echo PHP_EOL; echo 'ContactMessage: ' . App\Models\ContactMessage::count(); echo PHP_EOL; echo 'Content: ' . App\Models\Content::count();"
echo.

echo 3. Testing API endpoint (make sure server is running)...
echo Run: php artisan serve
echo Then test: http://127.0.0.1:8000/api/dashboard/statistics
echo.

pause
