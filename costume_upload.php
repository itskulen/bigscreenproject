<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = $_POST['project_name'];
    $projectStatus = $_POST['project_status'];
    $priority = $_POST['priority'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $subformEmbed = !empty($_POST['subform_embed']) ? $_POST['subform_embed'] : null;

    if (empty($projectName)) {
        $_SESSION['message'] = 'Project Name is required.';
        $_SESSION['message_type'] = 'danger';
        header('Location: costume_admin.php');
        exit;
    }

    // Validasi subformEmbed
    if (!empty($subformEmbed) && !filter_var($subformEmbed, FILTER_VALIDATE_URL)) {
        $_SESSION['message'] = "Invalid Google Slide URL.";
        $_SESSION['message_type'] = "danger";
        header("Location: costume_admin.php#alertMessage");
        exit;
    }

    if (empty($projectStatus) || $projectStatus === 'Select Status') {
        $_SESSION['message'] = 'Project Status is required.';
        $_SESSION['message_type'] = 'danger';
        header('Location: costume_admin.php');
        exit;
    }

    if (empty($priority) || $priority === 'Select Priority') {
        $_SESSION['message'] = 'Priority is required.';
        $_SESSION['message_type'] = 'danger';
        header('Location: costume_admin.php');
        exit;
    }

    // Validasi quantity
    if (empty($quantity) || !is_numeric($quantity) || intval($quantity) <= 0) {
        $_SESSION['message'] = "Quantity must be a positive number.";
        $_SESSION['message_type'] = "danger";
        header("Location: costume_admin.php#alertMessage");
        exit;
    }

    if (empty($_POST['deadline'])) {
        $_SESSION['message'] = 'Deadline is required.';
        $_SESSION['message_type'] = 'danger';
        header('Location: costume_admin.php');
        exit;
    }

    // Validasi deadline
    if (!empty($deadline) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
        $_SESSION['message'] = "Invalid deadline format.";
        $_SESSION['message_type'] = "danger";
        header("Location: costume_admin.php#alertMessage");
        exit;
    }

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
    $stmt = $pdo->prepare("INSERT INTO gallery (project_name, project_status, priority, quantity, project_image, material_image, description, deadline, category, subform_embed) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'costume', ?)");
    $success = $stmt->execute([$projectName, $projectStatus, $priority, $quantity, $projectImage, $materialImage, $description, $deadline, $subformEmbed]);
    if ($success) {
        $_SESSION['message'] = "Project successfully uploaded!";
        $_SESSION['message_type'] = "success";
        header("Location: costume_admin.php#alertMessage"); // Arahkan ke bagian alert message
        exit;
    } else {
        $_SESSION['message'] = "Failed to upload project.";
        $_SESSION['message_type'] = "error";
        header("Location: costume_admin.php#alertMessage"); // Arahkan ke bagian alert message
        exit;
    }
}