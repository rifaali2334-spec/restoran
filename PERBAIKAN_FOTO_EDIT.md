# Perbaikan Foto Tidak Muncul Saat Edit

## Masalah
Saat edit foto di halaman admin gallery, foto tidak muncul di modal edit.

## Penyebab
Path foto yang digunakan tidak konsisten antara local dan hosting:
- Local: foto di `storage/app/public/gallery/`
- Hosting: foto di `public/images/gallery/`

## Solusi yang Diterapkan

### 1. Menambahkan Accessor di Model Gallery
File: `app/Models/Gallery.php`

Ditambahkan method `getImageUrlAttribute()` yang:
- Cek file di `public/images/gallery/` terlebih dahulu (untuk hosting)
- Jika tidak ada, fallback ke `storage/` (untuk local)

```php
public function getImageUrlAttribute()
{
    if (!$this->image) {
        return null;
    }
    
    $basename = basename($this->image);
    
    // Cek di public/images/gallery terlebih dahulu (untuk hosting)
    if (file_exists(public_path('images/gallery/' . $basename))) {
        return asset('images/gallery/' . $basename);
    }
    
    // Fallback ke storage
    return asset('storage/' . $this->image);
}
```

### 2. Update View Admin Galleries
File: `resources/views/admin/galleries.blade.php`

Menggunakan accessor `$gallery->image_url` untuk menampilkan foto:
```blade
<img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}">
```

### 3. Update View Galeri Publik
File: `resources/views/galeri.blade.php`

Menggunakan accessor untuk carousel dan grid gallery:
```blade
<img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}">
```

### 4. Update View Index (Homepage)
File: `resources/views/index.blade.php`

Menggunakan accessor untuk gallery di homepage:
```blade
<img src="{{ $galleryPosition1->image_url }}" alt="{{ $galleryPosition1->title }}">
```

## Cara Kerja
1. Saat foto diakses, accessor `image_url` akan dipanggil
2. Accessor cek apakah file ada di `public/images/gallery/` (hosting)
3. Jika ada, return path hosting
4. Jika tidak ada, return path storage (local)
5. Foto akan muncul dengan benar di local maupun hosting

## Testing
1. Local: Foto akan diambil dari `storage/app/public/gallery/`
2. Hosting: Foto akan diambil dari `public/images/gallery/`
3. Edit foto di admin akan menampilkan preview dengan benar
4. Foto di halaman publik akan muncul dengan benar

## Catatan
- Tidak perlu symbolic link lagi
- Foto akan otomatis sync ke `public/images/gallery/` saat upload/update (via method `syncFotoToPublic()` di AdminController)
- Accessor membuat kode lebih clean dan maintainable
