# ⚠️ PENTING: PERBEDAAN INDEX.PHP LOCAL vs HOSTING

## 🔴 MASALAH
File `public/index.php` berbeda untuk LOCAL dan HOSTING karena struktur folder berbeda.

---

## 📁 STRUKTUR FOLDER

### LOCAL:
```
healthy/
├── app/
├── bootstrap/
├── public/
│   └── index.php  ← Di sini
├── storage/
└── vendor/
```

### HOSTING:
```
public_html/  (atau www/)
├── index.php  ← Di sini
├── .htaccess
└── healthy/
    ├── app/
    ├── bootstrap/
    ├── storage/
    └── vendor/
```

---

## 🔧 SOLUSI

### UNTUK LOCAL (Sekarang):
File `public/index.php` sudah saya set untuk LOCAL:
```php
require __DIR__.'/../vendor/autoload.php';
```

### UNTUK HOSTING (Nanti saat deploy):
Gunakan file `public/index_hosting.php`:
```php
require __DIR__.'/healthy/vendor/autoload.php';
```

---

## 🚀 CARA DEPLOY KE HOSTING

### STEP 1: Sebelum Upload
File `public/index.php` sekarang untuk LOCAL, jangan diupload!

### STEP 2: Saat Upload ke Hosting
1. **JANGAN upload** `public/index.php` yang sekarang
2. **Upload** `public/index_hosting.php` 
3. **Rename** `index_hosting.php` → `index.php` di hosting

### STEP 3: Atau Ganti Manual
Sebelum upload, ganti isi `public/index.php` dengan isi `public/index_hosting.php`

---

## 📋 CHECKLIST DEPLOY

- [ ] Backup `public/index.php` yang sekarang (untuk local)
- [ ] Copy isi `public/index_hosting.php`
- [ ] Paste ke `public/index.php`
- [ ] Upload ke hosting
- [ ] Setelah deploy, kembalikan `public/index.php` untuk local

---

## 💡 TIPS

**CARA MUDAH:**
Jangan upload `public/index.php` sama sekali! File ini sudah ada di hosting dan sudah benar.

Yang perlu diupload hanya:
- ✅ `app/Http/Controllers/AdminController.php`
- ✅ `resources/views/...`
- ✅ `database/migrations/...`
- ✅ Script helper

**JANGAN upload:**
- ❌ `public/index.php` (biarkan yang di hosting)
- ❌ `.env` (biarkan yang di hosting)
- ❌ `vendor/` (biarkan yang di hosting)

---

## 🔄 ALTERNATIF: Gunakan Git

Jika pakai Git, tambahkan ke `.gitignore`:
```
public/index.php
```

Lalu buat 2 branch:
- `local` - untuk development
- `production` - untuk hosting

---

**Dibuat:** 26 Februari 2026  
**Versi:** 1.0
