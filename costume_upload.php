<?php
session_start();
include 'db.php';
include 'middleware.php';
checkUserRole('costume'); // Hanya costume_manager yang bisa mengakses

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_name = $_POST['project_name'];
    $project_status = $_POST['project_status'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    // Upload file
    $project_image = $_FILES['project_image']['name'];
    $material_image = $_FILES['material_image']['name'];

    move_uploaded_file($_FILES['project_image']['tmp_name'], "uploads/projects/$project_image");
    move_uploaded_file($_FILES['material_image']['tmp_name'], "uploads/materials/$material_image");

    // Simpan ke database dengan kategori costume
    $stmt = $pdo->prepare("INSERT INTO gallery (project_name, project_status, project_image, material_image, description, deadline, category) 
                           VALUES (?, ?, ?, ?, ?, ?, 'costume')");
    $stmt->execute([$project_name, $project_status, $project_image, $material_image, $description, $deadline]);

    header('Location: costume_admin.php');
    exit;
}