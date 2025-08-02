<?php
include 'db.php';
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil nama file gambar dulu supaya bisa dihapus dari folder
    $stmt = $pdo->prepare('SELECT project_image, material_image FROM gallery WHERE id = ?');
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        // Hapus file project images (support both old and new format)
        if (!empty($data['project_image'])) {
            $projectImages = json_decode($data['project_image'], true);
            if (is_array($projectImages)) {
                foreach ($projectImages as $image) {
                    @unlink('uploads/projects/' . $image);
                }
            } else {
                @unlink('uploads/projects/' . $data['project_image']);
            }
        }

        // Hapus file material images (support both old and new format)
        if (!empty($data['material_image'])) {
            $materialImages = json_decode($data['material_image'], true);
            if (is_array($materialImages)) {
                foreach ($materialImages as $image) {
                    @unlink('uploads/materials/' . $image);
                }
            } else {
                @unlink('uploads/materials/' . $data['material_image']);
            }
        }

        // Hapus data dari database
        $deleteStmt = $pdo->prepare('DELETE FROM gallery WHERE id = ?');
        $success = $deleteStmt->execute([$id]);

        if ($success) {
            $_SESSION['message'] = 'Project successfully deleted!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to delete project.';
            $_SESSION['message_type'] = 'error';
        }

        // Redirect ke halaman admin
        header('Location: mascot_admin.php#alertMessage');
        exit();
    } else {
        $_SESSION['message'] = 'Data not found.';
        $_SESSION['message_type'] = 'error';
        header('Location: mascot_admin.php#alertMessage');
        exit();
    }
} else {
    $_SESSION['message'] = 'Invalid ID.';
    $_SESSION['message_type'] = 'error';
    header('Location: mascot_admin.php#alertMessage');
    exit();
}
