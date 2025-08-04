# Perubahan Priority dari Status ke Priority

## Ringkasan Perubahan

Telah berhasil memindahkan "Urgent" dari status ke priority dengan struktur baru:

### Priority Baru:

- **Urgent** (Merah) - #dc3545
- **High** (Kuning) - #ffc107
- **Normal** (Biru) - #007bff
- **Low** (Abu-abu) - #6c757d

### Status Baru (tanpa Urgent):

- **Upcoming** (Biru) - #31D2F2
- **In Progress** (Kuning) - #FFCA2C
- **Revision** (Orange/Purple) - #fd7e14/#6610f2
- **Completed** (Hijau) - #198754
- **Archived** (Abu-abu) - #d1d5db

## File yang Telah Diubah:

### 1. Database

- `database_priority_update.sql` - Script SQL untuk update data existing

### 2. Mascot Files:

- `mascot_index.php` - Updated getPriorityClass, status_counts, dropdown priority filter (removed redundant buttons), conditional subform display
- `mascot_admin.php` - Updated form dropdowns dan table dropdowns
- `mascot_edit.php` - Updated form dropdowns

### 3. Costume Files:

- `costume_index.php` - Updated getPriorityClass, status_counts, dropdown priority filter (removed redundant buttons), conditional subform display
- `costume_admin.php` - Updated form dropdowns dan table dropdowns
- `costume_edit.php` - Updated form dropdowns

## UI/UX Decisions:

- **Removed redundant priority filter buttons** - Priority filtering hanya menggunakan dropdown untuk efisiensi resource dan menghindari duplikasi UI
- **Status filter tetap menggunakan buttons** - Karena lebih visual dan intuitif untuk status
- **Priority filter hanya dropdown** - Lebih clean dan tidak menggunakan resource berlebihan
- **Conditional subform display** - Project name hanya clickable jika ada subform_embed, menampilkan icon slides hanya jika ada subform

## Langkah Selanjutnya:

1. **Jalankan script SQL** `database_priority_update.sql` untuk mengupdate data existing
2. **Test semua functionality** - form upload, edit, filter dropdown
3. **Update file upload** (`mascot_upload.php`, `costume_upload.php`) jika diperlukan
4. **Update file update** (`mascot_update.php`, `costume_update.php`) jika diperlukan

## Note:

- Semua project yang sebelumnya memiliki status "Urgent" akan dipindah ke priority "Urgent" dengan status "In Progress"
- Priority filter sekarang hanya menggunakan dropdown (lebih efisien)
- Status filter tetap menggunakan buttons dengan count yang benar
- Dropdown priority sudah include "Urgent" sebagai pilihan pertama
- Warna coding konsisten di seluruh aplikasi
