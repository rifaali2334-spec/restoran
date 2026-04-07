# 📸 PANDUAN FIX SEMUA FOTO DI HOSTING

## 🔍 MASALAH
Semua foto yang ditambahkan admin (Gallery, News, Content, Tentang) tidak muncul di website hosting, padaun text-nya muncul.

## 🎯 PENYEBAB
1. **Path foto tidak konsisten** - Ada yang pakai `healthy/storage/app/public/` ada yang pakai `storage/`
2. **Symbolic link belum dibuat** - Link dari `public/storage` ke `storage/app/public` belum ada
3. **Gallery pakai path berbeda** - Gallery disimpan ke `public/images/gallery/` bukan ke storage

## ✅ SOLUSI
1. Menyamakan SEMUA path foto ke `storage/` (standar Laravel)
2. Memindahkan gallery dari `public/images/gallery/` ke `storage/app/public/gallery/`
3. Membuat symbolic link storage di hosting

---

## 📋 LANGKAH-LANGKAH DEPLOY KE HOSTING

### STEP 1: Upload File yang Sudah Diubah

Upload file-file berikut ke hosting (overwrite yang lama):

**1. Controller:**
   - `app/Http/Controllers/AdminController.php`

**2. Views - Halaman Publik:**
   - `resources/views/index.blade.php`
   - `resources/views/galeri.blade.php`
   - `resources/views/berita.blade.php`
   - `resources/views/berita-detail.blade.php`
   - `resources/views/tentang.blade.php`

**3. Views - Admin Panel:**
   - `resources/views/admin/galleries.blade.php`
   - `resources/views/admin/contents.blade.php`

**4. Migration:**
   - `database/migrations/2026_02_26_000001_update_gallery_paths.php`

**5. Script Helper:**
   - `move_gallery_images.php` (upload ke root folder)

---

### STEP 2: Jalankan Migration

Akses terminal/SSH hosting atau gunakan cPanel Terminal, lalu jalankan:

```bash
cd /path/to/your/project
php artisan migrate
```

Ini akan update path foto gallery yang sudah ada di database.

---

### STEP 3: Pindahkan Foto Gallery yang Sudah Ada

**OPSI A: Via Browser (Mudah)**
1. Akses: `http://domain-anda.com/move_gallery_images.php`
2. Script akan otomatis memindahkan foto dari `public/images/gallery/` ke `storage/app/public/gallery/`
3. Setelah selesai, **HAPUS file `move_gallery_images.php`** untuk keamanan

**OPSI B: Via FTP/File Manager (Manual)**
1. Buka File Manager di cPanel
2. Copy semua foto dari `public/images/gallery/`
3. Paste ke `storage/app/public/gallery/`
4. Jika folder `gallery` belum ada, buat dulu

---

### STEP 4: Pastikan Symbolic Link Storage Ada

**OPSI A: Via Terminal/SSH**
```bash
cd /path/to/your/project
php artisan storage:link
```

**OPSI B: Via Script (Jika tidak ada akses terminal)**
1. File `create_storage_link.php` sudah ada di root
2. Akses: `http://domain-anda.com/create_storage_link.php`
3. Setelah selesai, **HAPUS file tersebut**

---

### STEP 5: Cek Permission Folder

Pastikan folder storage punya permission yang benar:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

Atau via File Manager cPanel:
- Klik kanan folder `storage` → Change Permissions → Set ke 775
- Klik kanan folder `bootstrap/cache` → Change Permissions → Set ke 775

---

### STEP 6: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

Atau akses file `clear_cache.php` yang sudah ada:
```
http://domain-anda.com/clear_cache.php
```

---

## 🧪 TESTING

### 1. Test Gallery
- Login ke Admin Panel
- Klik menu "Galleries"
- Tambah gallery baru dengan foto
- Cek apakah foto muncul di:
  - Admin gallery list
  - Halaman galeri publik (`/galeri`)
  - Halaman home (jika dipublish)

### 2. Test News/Berita
- Klik menu "News"
- Tambah berita baru dengan foto
- Cek apakah foto muncul di:
  - Admin news list
  - Halaman berita (`/berita`)
  - Halaman home (jika dipublish)

### 3. Test Content (Cards, Hero)
- Klik menu "Contents"
- Edit hero image atau tambah card dengan foto
- Cek apakah foto muncul di halaman home

### 4. Test Tentang
- Klik menu "Tentang"
- Update foto visi/misi
- Cek apakah foto muncul di halaman tentang (`/tentang`)

---

## 📝 CATATAN PENTING

### Untuk Foto Gallery yang Baru Ditambahkan:
✅ Akan otomatis tersimpan di `storage/app/public/gallery/`
✅ Akan langsung muncul di website (jika symbolic link sudah dibuat)

### Untuk Foto Gallery yang Lama:
✅ Path di database sudah diupdate oleh migration
✅ File foto sudah dipindahkan oleh script `move_gallery_images.php`
✅ Akan muncul setelah symbolic link dibuat

### Struktur Folder Setelah Fix:
```
project/
├── public/
│   ├── storage@ (symbolic link ke ../storage/app/public)
│   └── images/
│       └── gallery/ (bisa dihapus setelah foto dipindahkan)
├── storage/
│   └── app/
│       └── public/
│           ├── gallery/ (LOKASI BARU - foto gallery)
│           ├── news/ (foto berita)
│           ├── contents/ (foto content)
│           └── tentang/ (foto tentang)
```

---

## 🚨 TROUBLESHOOTING

### Foto Masih Tidak Muncul?

**1. Cek Symbolic Link:**
```bash
ls -la public/storage
```
Harus ada link ke `../storage/app/public`

**2. Cek Foto Ada di Storage:**
```bash
ls -la storage/app/public/gallery/
```
Harus ada file foto di sini

**3. Cek Permission:**
```bash
ls -la storage/
```
Harus 775 atau 777

**4. Cek Path di Database:**
```sql
SELECT id, image FROM galleries LIMIT 5;
```
Path harus format: `gallery/namafile.jpg` (bukan `namafile.jpg` saja)

**5. Cek .htaccess:**
File `public/.htaccess` harus ada dan benar

---

## 🔒 KEAMANAN

Setelah selesai deploy, **HAPUS file-file berikut:**
- ❌ `move_gallery_images.php`
- ❌ `create_storage_link.php` (jika sudah dijalankan)
- ❌ `clear_cache.php` (opsional, tapi lebih aman dihapus)

---

## 📞 BANTUAN

Jika masih ada masalah:
1. Cek error log: `storage/logs/laravel.log`
2. Cek error log hosting (biasanya di cPanel → Error Log)
3. Pastikan PHP version minimal 8.1
4. Pastikan extension PHP yang dibutuhkan sudah aktif (GD, fileinfo)

---

## ✨ HASIL AKHIR

Setelah semua langkah selesai:
- ✅ Admin bisa tambah gallery, news, content, tentang dengan foto
- ✅ Semua foto muncul di admin panel
- ✅ Semua foto muncul di halaman publik (home, galeri, berita, tentang)
- ✅ Semua foto menggunakan sistem storage yang sama (`storage/`)
- ✅ Path foto konsisten di semua halaman
- ✅ Mudah di-backup (tinggal backup folder storage)
- ✅ Sesuai best practice Laravel

---

**Dibuat:** 26 Februari 2026
**Versi:** 1.0
