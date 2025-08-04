-- Script untuk memindahkan Urgent dari status ke priority
-- Backup data terlebih dahulu sebelum menjalankan script ini

-- 1. Update semua project yang memiliki status 'Urgent' menjadi priority 'Urgent' dan status 'In Progress'
UPDATE gallery 
SET priority = 'Urgent', project_status = 'In Progress' 
WHERE project_status = 'Urgent';

-- 2. Verifikasi perubahan dengan query ini (jalankan setelah update)
-- SELECT id, project_name, project_status, priority FROM gallery WHERE priority = 'Urgent' OR project_status = 'Urgent';

-- 3. Optional: Jika ingin melihat semua data setelah perubahan
-- SELECT id, project_name, project_status, priority FROM gallery ORDER BY createAt DESC;
