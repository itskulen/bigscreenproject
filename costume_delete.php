<?php
include 'db.php';
include 'middleware.php';
checkUserRole('costume'); // Hanya costume_manager yang bisa mengakses

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$id = $_GET['id'];

// Hapus hanya jika kategori adalah 'costume'
$stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ? AND category = 'costume'");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    echo "Data berhasil dihapus.";
} else {
    echo "Data tidak ditemukan atau Anda tidak memiliki akses.";
}

header("Location: costume_admin.php");
exit;