<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = $_POST['project_name'];
    $projectStatus = $_POST['project_status'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    // Validasi file upload
    $projectImage = null;
    if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] === UPLOAD_ERR_OK) {
        $projectImage = uniqid() . "_" . basename($_FILES['project_image']['name']);
        move_uploaded_file($_FILES['project_image']['tmp_name'], "uploads/projects/$projectImage");
    }

    $materialImage = null;
    if (isset($_FILES['material_image']) && $_FILES['material_image']['error'] === UPLOAD_ERR_OK) {
        $materialImage = uniqid() . "_" . basename($_FILES['material_image']['name']);
        move_uploaded_file($_FILES['material_image']['tmp_name'], "uploads/materials/$materialImage");
    }

    // Simpan data ke database
    $stmt = $pdo->prepare("INSERT INTO gallery (project_name, project_status, quantity, project_image, material_image, description, deadline, category) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, 'mascot')");
    $success = $stmt->execute([$projectName, $projectStatus, $quantity, $projectImage, $materialImage, $description, $deadline]);

    // Set session flash message
    if ($success) {
        $_SESSION['message'] = "Project successfully uploaded!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to upload project.";
        $_SESSION['message_type'] = "danger";
    }

    // Redirect kembali ke halaman admin
    header("Location: mascot_admin.php");
    exit;
}