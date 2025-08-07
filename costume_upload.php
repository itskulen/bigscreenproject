<?php
session_start();
include 'db.php';

function sanitizeFileName($filename)
{
    return preg_replace('/[^a-zA-Z0-9.-]/', '_', basename($filename));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil semua input
    $projectName = trim($_POST['project_name'] ?? '');
    $projectStatus = trim($_POST['project_status'] ?? '');
    $priority = trim($_POST['priority'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $subformEmbed = trim($_POST['subform_embed'] ?? '');

    // Simpan data lama agar form tidak kosong saat error
    $_SESSION['old'] = $_POST;

    // Kumpulan error
    $errors = [];

    // Validasi server-side
    if ($projectName === '') {
        $errors[] = 'Project Name is required.';
    }
    if ($projectStatus === '') {
        $errors[] = 'Project Status is required.';
    }
    if ($priority === '') {
        $errors[] = 'Priority is required.';
    }
    if ($quantity === '' || !is_numeric($quantity) || intval($quantity) <= 0) {
        $errors[] = 'Quantity must be a positive number.';
    }
    if ($deadline !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
        $errors[] = 'Invalid deadline format.';
    }
    if ($subformEmbed !== '' && !filter_var($subformEmbed, FILTER_VALIDATE_URL)) {
        $errors[] = 'Invalid Google Slide URL.';
    }

    // Jika ada error, tampilkan alert di atas form
    if ($errors) {
        $_SESSION['message'] = implode('<br>', $errors);
        $_SESSION['message_type'] = 'danger';
        header('Location: costume_admin.php#alertMessage');
        exit();
    }

    // Proses upload file gambar project
    $projectImages = [];
    if (isset($_FILES['project_image']) && !empty($_FILES['project_image']['name'][0])) {
        foreach ($_FILES['project_image']['name'] as $i => $name) {
            if ($_FILES['project_image']['error'][$i] === UPLOAD_ERR_OK) {
                $projectImage = uniqid() . '_' . sanitizeFileName($name);
                if (move_uploaded_file($_FILES['project_image']['tmp_name'][$i], "uploads/projects/$projectImage")) {
                    $projectImages[] = $projectImage;
                }
            }
        }
    }

    // Proses upload file material
    $materialImages = [];
    if (isset($_FILES['material_image']) && !empty($_FILES['material_image']['name'][0])) {
        foreach ($_FILES['material_image']['name'] as $i => $name) {
            if ($_FILES['material_image']['error'][$i] === UPLOAD_ERR_OK) {
                $materialImage = uniqid() . '_' . sanitizeFileName($name);
                if (move_uploaded_file($_FILES['material_image']['tmp_name'][$i], "uploads/materials/$materialImage")) {
                    $materialImages[] = $materialImage;
                }
            }
        }
    }

    // Simpan ke database
    $projectImagesJson = $projectImages ? json_encode($projectImages) : null;
    $materialImagesJson = $materialImages ? json_encode($materialImages) : null;

    $stmt = $pdo->prepare("INSERT INTO gallery
    (project_name, project_status, priority, quantity, project_image, material_image, description, deadline, category, subform_embed)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'costume', ?)");
    $success = $stmt->execute([$projectName, $projectStatus, $priority, $quantity, $projectImagesJson, $materialImagesJson, $description, $deadline === '' ? null : $deadline, $subformEmbed]);

    // Hapus data lama dari session jika sukses
    unset($_SESSION['old']);

    if ($success) {
        $_SESSION['message'] = 'Project successfully uploaded!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to upload project.';
        $_SESSION['message_type'] = 'danger';
    }
    header('Location: costume_admin.php#alertMessage');
    exit();
}
