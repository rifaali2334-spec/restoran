# Troubleshooting Flutter - Laravel API

## Masalah: Flutter tidak menampilkan data dari database

### Solusi yang sudah diterapkan:

1. ✅ **CORS Configuration** - Ditambahkan untuk mengizinkan Flutter mengakses API
2. ✅ **Test Script** - File `test_api.bat` untuk mengecek koneksi

---

## Langkah-langkah Troubleshooting:

### 1. Pastikan Server Laravel Berjalan
```bash
php artisan serve
```
Server akan berjalan di: `http://127.0.0.1:8000`

### 2. Test API Endpoint
Buka browser atau Postman, akses:
```
http://127.0.0.1:8000/api/dashboard/statistics
```

Harusnya return JSON seperti:
```json
{
  "success": true,
  "data": {
    "stats": {
      "total_news": 0,
      "total_galleries": 0,
      "total_messages": 0,
      "total_contents": 0
    },
    "chart_data": [...]
  }
}
```

### 3. Cek Data di Database
Jalankan:
```bash
test_api.bat
```

Atau manual:
```bash
php artisan tinker
>>> App\Models\News::count()
>>> App\Models\Gallery::count()
```

### 4. Pastikan URL di Flutter Benar

Di Flutter, pastikan URL API menggunakan:
- **Android Emulator**: `http://10.0.2.2:8000/api/dashboard/statistics`
- **iOS Simulator**: `http://127.0.0.1:8000/api/dashboard/statistics`
- **Real Device**: `http://[IP_KOMPUTER]:8000/api/dashboard/statistics`

Contoh kode Flutter:
```dart
final response = await http.get(
  Uri.parse('http://10.0.2.2:8000/api/dashboard/statistics'),
);
```

### 5. Cek Internet Permission (Android)

File: `android/app/src/main/AndroidManifest.xml`
```xml
<uses-permission android:name="android.permission.INTERNET"/>
```

### 6. Allow HTTP (bukan HTTPS)

**Android** - File: `android/app/src/main/AndroidManifest.xml`
```xml
<application
    android:usesCleartextTraffic="true"
    ...>
```

**iOS** - File: `ios/Runner/Info.plist`
```xml
<key>NSAppTransportSecurity</key>
<dict>
    <key>NSAllowsArbitraryLoads</key>
    <true/>
</dict>
```

### 7. Debug Response di Flutter

Tambahkan print untuk debug:
```dart
try {
  final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/dashboard/statistics'));
  print('Status Code: ${response.statusCode}');
  print('Response Body: ${response.body}');
  
  if (response.statusCode == 200) {
    final data = json.decode(response.body);
    print('Data: $data');
  }
} catch (e) {
  print('Error: $e');
}
```

---

## Checklist Debugging:

- [ ] Server Laravel sudah jalan (`php artisan serve`)
- [ ] API endpoint bisa diakses di browser
- [ ] Database ada datanya (cek dengan tinker)
- [ ] URL di Flutter sudah benar (10.0.2.2 untuk emulator)
- [ ] Internet permission sudah ditambahkan
- [ ] Cleartext traffic diizinkan
- [ ] Tidak ada error di console Flutter
- [ ] Response status code 200

---

## Cara Menambah Data Test (jika database kosong):

```bash
php artisan tinker
```

```php
// Tambah News
App\Models\News::create([
    'title' => 'Test News',
    'content' => 'Test content',
    'is_published' => true
]);

// Tambah Gallery
App\Models\Gallery::create([
    'title' => 'Test Gallery',
    'image' => 'test.jpg'
]);

// Tambah Content
App\Models\Content::create([
    'title' => 'Test Content',
    'body' => 'Test body'
]);
```

---

## Jika Masih Bermasalah:

1. Cek log Laravel: `storage/logs/laravel.log`
2. Cek console Flutter untuk error message
3. Pastikan tidak ada firewall yang memblokir port 8000
4. Coba restart server Laravel dan rebuild Flutter app
