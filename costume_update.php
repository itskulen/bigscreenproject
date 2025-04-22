<?php
include 'db.php';
include 'middleware.php';
checkUserRole('costume'); // Hanya costume_manager yang bisa mengakses

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $projectName = $_POST['project_name'];
    $projectStatus = $_POST['project_status'];
    $description = $_POST['description'];

    // Ambil data lama
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ? AND category = 'costume'");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if (!$data) {
        echo "Data tidak ditemukan atau Anda tidak memiliki akses.";
        exit;
    }

    // Cek apakah user upload gambar baru atau tidak
    $projectImage = $data['project_image'];
    if ($_FILES['project_image']['name']) {
        $projectImage = uniqid() . "_" . $_FILES['project_image']['name'];
        move_uploaded_file($_FILES['project_image']['tmp_name'], "uploads/projects/$projectImage");
    }

    $materialImage = $data['material_image'];
    if ($_FILES['material_image']['name']) {
        $materialImage = uniqid() . "_" . $_FILES['material_image']['name'];
        move_uploaded_file($_FILES['material_image']['tmp_name'], "uploads/materials/$materialImage");
    }

    $update = $pdo->prepare("UPDATE gallery SET project_name = ?, project_status = ?, description = ?, project_image = ?, material_image = ? WHERE id = ? AND category = 'costume'");
    $update->execute([$projectName, $projectStatus, $description, $projectImage, $materialImage, $id]);

    header("Location: costume_admin.php");
    exit;
}