# ✅ CHECKLIST DEPLOY - FIX FOTO HOSTING

## 📦 FILE YANG HARUS DIUPLOAD (7 files)

```
1. ✅ config/filesystems.php
2. ✅ app/Models/Gallery.php
3. ✅ app/Models/News.php
4. ✅ app/Models/Content.php
5. ✅ app/Models/Tentang.php
6. ✅ fix_storage_link_hosting.php
7. ✅ copy_storage_files_hosting.php
```

---

## 🚀 LANGKAH DEPLOY (5 STEPS)

### STEP 1: Upload File ✅
- [ ] Upload 7 file di atas ke hosting via FTP/File Manager

### STEP 2: Buat Symbolic Link ✅
- [ ] Akses: `https://naufal.baknus.26.cyberwarrior.co.id/fix_storage_link_hosting.php`
- [ ] Jika berhasil: Lanjut ke STEP 3
- [ ] Jika gagal: Akses `copy_storage_files_hosting.php` (alternatif)

### STEP 3: Set Permission ✅
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/storage
```

### STEP 4: Clear Cache ✅
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### STEP 5: Testing ✅
- [ ] Buka homepage → Cek foto hero, cards, news, gallery
- [ ] Buka /galeri → Cek carousel dan grid
- [ ] Buka /berita → Cek foto berita
- [ ] Buka /tentang → Cek foto visi/misi
- [ ] Login admin → Upload foto baru → Cek muncul atau tidak

---

## 🔒 KEAMANAN (WAJIB!)

### Setelah Berhasil, HAPUS File Ini:
- [ ] ❌ fix_storage_link_hosting.php
- [ ] ❌ copy_storage_files_hosting.php

---

## 🎯 HASIL YANG DIHARAPKAN

### ✅ Foto Lama
- Semua foto yang sudah ada sebelumnya muncul di website

### ✅ Foto Baru
- Admin bisa upload foto baru
- Foto langsung muncul di website
- Tidak perlu manual copy/sync lagi

### ✅ URL Foto
- Format: `/storage/gallery/namafile.jpg`
- BUKAN: `/healthy/storage/app/public/...`

---

## 🆘 TROUBLESHOOTING CEPAT

### Foto masih tidak muncul?

**1. Cek symbolic link:**
```bash
ls -la public/storage
```
Harus ada link ke `../storage/app/public`

**2. Cek file ada:**
```bash
ls -la storage/app/public/gallery/
```
Harus ada file foto

**3. Cek permission:**
```bash
ls -la storage/
```
Harus 755 atau 775

**4. Cek URL di browser:**
```
https://naufal.baknus.26.cyberwarrior.co.id/storage/gallery/namafile.jpg
```
- ✅ Muncul = OK
- ❌ 404 = Symlink belum dibuat
- ❌ 403 = Permission salah

**5. Clear cache lagi:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 📞 KONTAK

Jika masih bermasalah setelah ikuti semua langkah:
1. Screenshot error yang muncul
2. Cek `storage/logs/laravel.log`
3. Cek error log di cPanel

---

**Estimasi Waktu Deploy:** 10-15 menit  
**Tingkat Kesulitan:** ⭐⭐ (Mudah)  
**Status:** ✅ Siap Deploy
