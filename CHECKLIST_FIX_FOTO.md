# ✅ CHECKLIST FIX FOTO - SEBELUM & SESUDAH DEPLOY

## 📋 SEBELUM DEPLOY (DI LOCAL)

### 1. Testing di Local
- [ ] Jalankan `fix_foto_local.bat`
- [ ] Migration berhasil dijalankan
- [ ] Symbolic link berhasil dibuat
- [ ] Folder gallery sudah ada di storage
- [ ] Foto lama berhasil dipindahkan (jika ada)

### 2. Test Tambah Foto Baru
- [ ] Login ke admin panel (http://localhost:8000/admin)
- [ ] Test Gallery:
  - [ ] Tambah gallery baru dengan foto
  - [ ] Foto muncul di admin gallery list
  - [ ] Foto muncul di halaman galeri (/galeri)
  - [ ] Foto muncul di home (jika dipublish)
- [ ] Test News:
  - [ ] Tambah berita baru dengan foto
  - [ ] Foto muncul di admin news list
  - [ ] Foto muncul di halaman berita (/berita)
  - [ ] Foto muncul di home (jika dipublish)
- [ ] Test Content:
  - [ ] Edit hero image
  - [ ] Tambah/edit card dengan foto
  - [ ] Foto muncul di home
- [ ] Test Tentang:
  - [ ] Update foto visi/misi
  - [ ] Foto muncul di halaman tentang (/tentang)

### 3. Cek Path Foto
- [ ] Buka browser DevTools (F12)
- [ ] Cek URL foto, harus format: `/storage/gallery/namafile.jpg`
- [ ] BUKAN: `/healthy/storage/app/public/...`
- [ ] BUKAN: `/images/gallery/...`

### 4. Backup
- [ ] Backup database lokal
- [ ] Backup folder storage lokal
- [ ] Backup file yang akan diupload

---

## 🚀 SAAT DEPLOY KE HOSTING

### 1. Upload File
- [ ] Upload `app/Http/Controllers/AdminController.php`
- [ ] Upload `resources/views/index.blade.php`
- [ ] Upload `resources/views/galeri.blade.php`
- [ ] Upload `resources/views/admin/galleries.blade.php`
- [ ] Upload `resources/views/admin/contents.blade.php`
- [ ] Upload `database/migrations/2026_02_26_000001_update_gallery_paths.php`
- [ ] Upload `move_gallery_images.php` ke root folder

### 2. Jalankan Migration
- [ ] Akses terminal/SSH hosting
- [ ] Jalankan: `php artisan migrate`
- [ ] Cek tidak ada error

### 3. Pindahkan Foto Gallery
**OPSI A: Via Browser**
- [ ] Akses: `http://domain.com/move_gallery_images.php`
- [ ] Tunggu sampai selesai
- [ ] Cek berapa foto yang berhasil dipindahkan
- [ ] **HAPUS file `move_gallery_images.php`**

**OPSI B: Via FTP/File Manager**
- [ ] Buka File Manager cPanel
- [ ] Copy semua foto dari `public/images/gallery/`
- [ ] Paste ke `storage/app/public/gallery/`
- [ ] Cek semua foto sudah ada

### 4. Buat Symbolic Link
**OPSI A: Via Terminal**
- [ ] Jalankan: `php artisan storage:link`
- [ ] Cek tidak ada error

**OPSI B: Via Script**
- [ ] Akses: `http://domain.com/create_storage_link.php`
- [ ] Cek berhasil
- [ ] **HAPUS file `create_storage_link.php`**

### 5. Set Permission
- [ ] Set permission folder `storage` ke 775
- [ ] Set permission folder `bootstrap/cache` ke 775
- [ ] Cek via terminal: `ls -la storage/`

### 6. Clear Cache
- [ ] Jalankan: `php artisan cache:clear`
- [ ] Jalankan: `php artisan config:clear`
- [ ] Jalankan: `php artisan view:clear`
- [ ] Atau akses: `http://domain.com/clear_cache.php`

---

## 🧪 SESUDAH DEPLOY (TESTING DI HOSTING)

### 1. Cek Foto Lama
- [ ] Buka halaman home
- [ ] Cek foto hero muncul
- [ ] Cek foto cards muncul
- [ ] Cek foto news muncul
- [ ] Cek foto gallery muncul
- [ ] Buka halaman galeri
- [ ] Cek carousel muncul
- [ ] Cek gallery grid muncul
- [ ] Buka halaman berita
- [ ] Cek foto berita muncul
- [ ] Buka halaman tentang
- [ ] Cek foto visi/misi muncul

### 2. Test Tambah Foto Baru
- [ ] Login ke admin panel
- [ ] Test Gallery:
  - [ ] Tambah gallery baru dengan foto
  - [ ] Foto muncul di admin
  - [ ] Foto muncul di halaman galeri
  - [ ] Foto muncul di home (jika dipublish)
- [ ] Test News:
  - [ ] Tambah berita baru dengan foto
  - [ ] Foto muncul di admin
  - [ ] Foto muncul di halaman berita
  - [ ] Foto muncul di home (jika dipublish)
- [ ] Test Content:
  - [ ] Edit hero image
  - [ ] Foto muncul di home
- [ ] Test Tentang:
  - [ ] Update foto visi/misi
  - [ ] Foto muncul di halaman tentang

### 3. Cek Path Foto di Hosting
- [ ] Buka browser DevTools (F12)
- [ ] Cek URL foto, harus format: `/storage/gallery/namafile.jpg`
- [ ] Cek foto bisa diakses langsung: `http://domain.com/storage/gallery/namafile.jpg`

### 4. Cek Database
- [ ] Login ke phpMyAdmin
- [ ] Buka tabel `galleries`
- [ ] Cek kolom `image`, harus format: `gallery/namafile.jpg`
- [ ] BUKAN: `namafile.jpg` saja

### 5. Cek Folder Storage
- [ ] Buka File Manager cPanel
- [ ] Cek folder `storage/app/public/gallery/`
- [ ] Pastikan semua foto ada di sini
- [ ] Cek folder `public/storage/` (symbolic link)
- [ ] Harus ada link ke `../storage/app/public`

---

## 🔒 KEAMANAN (WAJIB!)

### Hapus File Berbahaya
- [ ] **HAPUS** `move_gallery_images.php`
- [ ] **HAPUS** `create_storage_link.php`
- [ ] **HAPUS** `clear_cache.php` (opsional)
- [ ] **HAPUS** `PANDUAN_FIX_GALLERY.md` (opsional)
- [ ] **HAPUS** `RINGKASAN_FIX_FOTO.md` (opsional)

### Cek Permission
- [ ] File `.env` permission 600
- [ ] Folder `storage` permission 775
- [ ] Folder `bootstrap/cache` permission 775

---

## 🚨 TROUBLESHOOTING

### Jika Foto Masih Tidak Muncul

#### 1. Cek Symbolic Link
```bash
ls -la public/storage
```
- [ ] Ada link ke `../storage/app/public`
- [ ] Jika tidak ada, jalankan lagi: `php artisan storage:link`

#### 2. Cek Foto Ada di Storage
```bash
ls -la storage/app/public/gallery/
```
- [ ] Ada file foto di sini
- [ ] Jika tidak ada, pindahkan manual dari `public/images/gallery/`

#### 3. Cek Permission
```bash
ls -la storage/
```
- [ ] Permission 775 atau 777
- [ ] Jika tidak, jalankan: `chmod -R 775 storage`

#### 4. Cek Path di Database
```sql
SELECT id, image FROM galleries LIMIT 5;
```
- [ ] Format: `gallery/namafile.jpg`
- [ ] Jika masih `namafile.jpg`, jalankan migration lagi

#### 5. Clear Cache Lagi
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```
- [ ] Tidak ada error
- [ ] Refresh browser dengan Ctrl+F5

#### 6. Cek Error Log
- [ ] Buka `storage/logs/laravel.log`
- [ ] Cek error terbaru
- [ ] Buka cPanel → Error Log
- [ ] Cek error PHP

---

## ✅ KONFIRMASI AKHIR

### Semua Foto Muncul?
- [ ] ✅ Foto gallery muncul di semua halaman
- [ ] ✅ Foto news muncul di semua halaman
- [ ] ✅ Foto content muncul di home
- [ ] ✅ Foto tentang muncul di halaman tentang
- [ ] ✅ Admin bisa tambah foto baru
- [ ] ✅ Foto baru langsung muncul
- [ ] ✅ Path foto konsisten (`/storage/...`)
- [ ] ✅ File berbahaya sudah dihapus

### Jika Semua Checklist ✅
**SELAMAT! Fix foto berhasil! 🎉**

Website sekarang:
- ✅ Semua foto muncul dengan benar
- ✅ Admin bisa tambah foto tanpa masalah
- ✅ Path foto konsisten dan sesuai standar Laravel
- ✅ Mudah di-maintain dan di-backup

---

**Dibuat:** 26 Februari 2026  
**Versi:** 1.0  
**Status:** Ready to Use
