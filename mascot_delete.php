<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil nama file gambar dulu supaya bisa dihapus dari folder
    $stmt = $pdo->prepare("SELECT project_image, material_image FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        // Hapus file gambarnya
        @unlink("uploads/projects/" . $data['project_image']);
        @unlink("uploads/materials/" . $data['material_image']);

        // Hapus data dari database
        $deleteStmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        $deleteStmt->execute([$id]);

        header("Location: mascot_admin.php"); // Redirect kembali ke halaman admin
        exit;
    } else {
        echo "Data tidak ditemukan.";
    }
} else {
    echo "ID tidak valid.";
}