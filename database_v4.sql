-- Update database untuk mendukung multiple images
-- Field project_image dan material_image akan menyimpan JSON array untuk multiple files

-- Tidak perlu mengubah struktur tabel karena field sudah TEXT
-- Hanya perlu update cara penyimpanan dan pembacaan data

-- Contoh format JSON yang akan disimpan:
-- project_image: ["image1.jpg", "image2.jpg", "image3.jpg"]
-- material_image: ["material1.jpg", "material2.jpg"]

-- Untuk backward compatibility, single image akan tetap bisa disimpan sebagai string biasa
-- Dan akan dikonversi ke format array saat dibaca
