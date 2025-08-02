# Multiple Image Upload Feature

## Overview

Fitur ini memungkinkan upload multiple images untuk project image dan submission notes, dengan slideshow menggunakan Fancybox.

## Features

- ✅ Multiple file upload untuk project images dan material images
- ✅ Preview multiple images saat upload
- ✅ Slideshow dengan Fancybox di gallery view
- ✅ Backward compatibility dengan format single image lama
- ✅ Indicator jumlah gambar di UI
- ✅ Tampilan thumbnail pertama di card dan tabel
- ✅ Auto migration script untuk data lama

## Changes Made

### Database

- Field `project_image` dan `material_image` sekarang menyimpan JSON array
- Format baru: `["image1.jpg", "image2.jpg", "image3.jpg"]`
- Format lama masih didukung untuk backward compatibility

### Files Modified

1. **mascot_admin.php**

   - Form upload diubah untuk support multiple files (`name="project_image[]"`)
   - JavaScript preview diupdate untuk multiple images
   - Tabel admin menampilkan gallery dengan Fancybox

2. **mascot_upload.php**

   - Logic upload diubah untuk handle multiple files
   - Simpan sebagai JSON array ke database

3. **mascot_edit.php**

   - Form edit support multiple file upload
   - Preview existing images dan new images
   - Update dengan multiple files

4. **mascot_update.php**

   - Handle multiple file updates
   - Support backward compatibility

5. **mascot_index.php**

   - Card view menampilkan gambar pertama dengan indicator jumlah
   - Fancybox slideshow untuk semua gambar
   - Badge indicator untuk multiple images

6. **mascot_delete.php**
   - Delete semua files (support both old and new format)

### New Files

1. **image_helper.php** - Helper functions untuk image handling
2. **migrate_images.php** - Script migrasi data lama
3. **database_v4.sql** - Dokumentasi perubahan database

## Usage

### Upload Multiple Images

1. Di form upload, pilih multiple files sekaligus
2. Preview akan menampilkan semua gambar yang dipilih
3. Gambar akan disimpan sebagai JSON array

### View Images

1. Di admin table: Click gambar untuk melihat slideshow
2. Di public index: Click gambar untuk melihat slideshow
3. Badge menunjukkan jumlah gambar jika lebih dari 1

### Edit Images

1. Di form edit, existing images ditampilkan
2. Upload new files akan replace semua existing images
3. Kosongkan jika tidak ingin mengubah

## Migration

Jalankan script migration untuk convert data lama:

```php
php migrate_images.php
```

## Backward Compatibility

- Data lama (single image) tetap bisa dibaca
- Automatic conversion saat edit/update
- Helper functions handle both format

## Technical Details

### Image Storage Format

```php
// Old format (still supported)
$project_image = "image.jpg";

// New format
$project_image = '["image1.jpg", "image2.jpg", "image3.jpg"]';
```

### Helper Functions

- `parseImageData()` - Parse JSON atau string
- `getFirstImage()` - Ambil gambar pertama
- `getImageCount()` - Hitung jumlah gambar
- `generateImageGallery()` - Generate HTML gallery

### Fancybox Gallery Groups

- Individual project: `data-fancybox="project-{id}"`
- Individual material: `data-fancybox="material-{id}"`
- All projects: `data-fancybox="all-projects"`

## CSS Classes Added

- `.image-gallery` - Container for image gallery
- `.badge` - Image count indicator
- Preview containers with scroll support
