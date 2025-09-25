<?php
session_start();
include 'db.php';

function sanitizeFileName($filename)
{
    // Remove extension
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $name = pathinfo($filename, PATHINFO_FILENAME);

    // Clean filename - remove special characters and limit length
    $clean = preg_replace('/[^a-zA-Z0-9]/', '_', $name);
    $clean = substr($clean, 0, 20); // Limit to 20 characters

    return $clean . '.' . $extension;
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
    $type = trim($_POST['type'] ?? '');

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
    if ($type === '') {
        $errors[] = 'Category is required.';
    }

    // Jika ada error, tampilkan alert di atas form
    if ($errors) {
        $_SESSION['message'] = implode('<br>', $errors);
        $_SESSION['message_type'] = 'danger';
        header('Location: mascot_admin.php#alertMessage');
        exit();
    }

    // Proses upload file gambar project
    $projectImages = [];
    if (isset($_FILES['project_image']) && !empty($_FILES['project_image']['name'][0])) {
        foreach ($_FILES['project_image']['name'] as $i => $name) {
            if ($_FILES['project_image']['error'][$i] === UPLOAD_ERR_OK) {
                // Generate shorter filename: timestamp + sanitized name
                $timestamp = time() . substr(microtime(), 2, 3); // More unique but shorter
                $cleanName = sanitizeFileName($name);
                $projectImage = $timestamp . '_' . $cleanName;

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
                // Generate shorter filename: timestamp + sanitized name
                $timestamp = time() . substr(microtime(), 2, 3); // More unique but shorter
                $cleanName = sanitizeFileName($name);
                $materialImage = $timestamp . '_' . $cleanName;

                if (move_uploaded_file($_FILES['material_image']['tmp_name'][$i], "uploads/materials/$materialImage")) {
                    $materialImages[] = $materialImage;
                }
            }
        }
    }

    // Simpan ke database
    $projectImagesJson = $projectImages ? json_encode($projectImages) : null;
    $materialImagesJson = $materialImages ? json_encode($materialImages) : null;

    // Debug: Log data length untuk troubleshooting
    error_log('Mascot Upload Debug - Project Images JSON length: ' . strlen($projectImagesJson ?? ''));
    error_log('Mascot Upload Debug - Material Images JSON length: ' . strlen($materialImagesJson ?? ''));

    $stmt = $pdo->prepare("INSERT INTO gallery
    (project_name, project_status, priority, quantity, project_image, material_image, description, deadline, category, type, subform_embed)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'mascot', ?, ?)");
    $success = $stmt->execute([$projectName, $projectStatus, $priority, $quantity, $projectImagesJson, $materialImagesJson, $description, $deadline === '' ? null : $deadline, $type, $subformEmbed]);
    // Hapus data lama dari session jika sukses
    unset($_SESSION['old']);

    if ($success) {
        $_SESSION['message'] = 'Project successfully uploaded!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to upload project.';
        $_SESSION['message_type'] = 'danger';
    }
    header('Location: mascot_admin.php#alertMessage');
    exit();
}
