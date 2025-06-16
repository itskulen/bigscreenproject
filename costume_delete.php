<?php
include 'db.php';
session_start();

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
        $success = $deleteStmt->execute([$id]);

        if ($success) {
            $_SESSION['message'] = "Project successfully deleted!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to delete project.";
            $_SESSION['message_type'] = "error";
        }

        // Redirect ke halaman admin
        header("Location: costume_admin.php#alertMessage");
        exit;
    } else {
        $_SESSION['message'] = "Data not found.";
        $_SESSION['message_type'] = "error";
        header("Location: costume_admin.php#alertMessage");
        exit;
    }
} else {
    $_SESSION['message'] = "Invalid ID.";
    $_SESSION['message_type'] = "error";
    header("Location: costume_admin.php#alertMessage");
    exit;
}