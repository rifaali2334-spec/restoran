# 🏠 PANDUAN SETUP LOCAL DEVELOPMENT

## ❌ MASALAH
Error database karena `.env` masih pakai konfigurasi HOSTING, bukan LOCAL.

---

## ✅ SOLUSI YANG SUDAH DILAKUKAN

Saya sudah buatkan 3 file `.env`:

1. **`.env`** - Sekarang untuk LOCAL ✅
2. **`.env.local`** - Backup untuk LOCAL
3. **`.env.hosting`** - Backup untuk HOSTING

---

## 🚀 LANGKAH-LANGKAH SETUP LOCAL

### STEP 1: Start XAMPP
1. Buka XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**

### STEP 2: Buat Database
1. Buka browser: `http://localhost/phpmyadmin`
2. Klik **"New"** (di sidebar kiri)
3. Database name: **`healthy`**
4. Collation: **`utf8mb4_unicode_ci`**
5. Klik **"Create"**

### STEP 3: Setup Database
Double click file ini:
```
setup_database_local.bat
```

Script akan otomatis:
- ✅ Clear cache
- ✅ Jalankan migration (buat tabel)
- ✅ Jalankan seeder (buat admin)

### STEP 4: Jalankan Server
```bash
php artisan serve
```

### STEP 5: Buka Browser
```
http://localhost:8000
```

### STEP 6: Login Admin
```
Email: admin@admin.com
Password: admin123
```

---

## 📋 KONFIGURASI DATABASE

### LOCAL (.env):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=healthy
DB_USERNAME=root
DB_PASSWORD=
```

### HOSTING (.env.hosting):
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=aff100_db_baknus_26_naufal
DB_USERNAME=aff100_db_baknus_26_naufal
DB_PASSWORD=xcJGTVvgRJRmaxqCuWfe
```

---

## 🔄 SWITCH ENVIRONMENT

Jika mau ganti environment, jalankan:
```
switch_env.bat
```

Pilih:
- **1** = LOCAL (development)
- **2** = HOSTING (production)

---

## 🧪 TESTING

Setelah setup selesai, test:

### 1. Test Login Admin
- Buka: `http://localhost:8000/admin`
- Login dengan: `admin@admin.com` / `admin123`

### 2. Test Tambah Foto
- Klik menu **"Galleries"**
- Klik **"Tambah Gallery"**
- Upload foto
- Simpan
- Cek apakah foto muncul

### 3. Test Halaman Publik
- Buka: `http://localhost:8000`
- Cek halaman home
- Cek halaman galeri: `http://localhost:8000/galeri`
- Cek halaman berita: `http://localhost:8000/berita`

---

## 🚨 TROUBLESHOOTING

### Error: Access denied for user 'root'@'localhost'

**Solusi:**
1. Cek MySQL sudah jalan di XAMPP
2. Cek username/password di `.env`:
   ```env
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Jika pakai password, isi `DB_PASSWORD`

### Error: Database 'healthy' doesn't exist

**Solusi:**
1. Buat database dulu di phpMyAdmin
2. Nama harus: **`healthy`**

### Error: SQLSTATE[HY000] [2002] No connection

**Solusi:**
1. Start MySQL di XAMPP
2. Tunggu sampai hijau
3. Coba lagi

### Error: Nothing to migrate

**Solusi:**
Sudah OK! Artinya tabel sudah ada.

---

## 📁 FILE PENTING

### File Environment:
- `.env` - Environment aktif (sekarang LOCAL)
- `.env.local` - Backup LOCAL
- `.env.hosting` - Backup HOSTING
- `.env.example` - Template

### Script Helper:
- `setup_database_local.bat` - Setup database local
- `switch_env.bat` - Ganti environment
- `fix_foto_local.bat` - Fix foto di local

---

## ⚠️ PENTING!

### Jangan Upload ke Hosting:
- ❌ `.env` (biarkan yang di hosting)
- ❌ `.env.local` (hanya untuk local)
- ❌ `database/database.sqlite` (jika ada)

### Jangan Commit ke Git:
Tambahkan ke `.gitignore`:
```
.env
.env.local
.env.hosting
.env.backup
```

---

## 🎯 HASIL AKHIR

Setelah setup selesai:
- ✅ Database local siap
- ✅ Admin bisa login
- ✅ Bisa tambah foto
- ✅ Foto muncul di website
- ✅ Siap development

---

## 📞 BANTUAN

### Jika masih error:

**1. Cek MySQL jalan:**
```bash
mysql -u root -p
```

**2. Cek database ada:**
```sql
SHOW DATABASES;
```

**3. Cek tabel ada:**
```sql
USE healthy;
SHOW TABLES;
```

**4. Clear cache:**
```bash
php artisan config:clear
php artisan cache:clear
```

---

**Dibuat:** 26 Februari 2026  
**Versi:** 1.0  
**Status:** Ready to Use
