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

    // Validasi subformEmbed
    if (!empty($subformEmbed) && !filter_var($subformEmbed, FILTER_VALIDATE_URL)) {
        $_SESSION['message'] = 'Invalid Google Slide URL.';
        $_SESSION['message_type'] = 'danger';
        header('Location: mascot_admin.php#alertMessage');
        exit();
    }

    // Validasi quantity
    if (empty($quantity) || !is_numeric($quantity) || intval($quantity) <= 0) {
        $_SESSION['message'] = 'Quantity must be a positive number.';
        $_SESSION['message_type'] = 'danger';
        header('Location: mascot_admin.php#alertMessage');
        exit();
    }

    if (empty($_POST['deadline'])) {
        $_SESSION['message'] = 'Deadline is required.';
        $_SESSION['message_type'] = 'danger';
        header('Location: mascot_admin.php');
        exit();
    }

    // Validasi deadline
    if (!empty($deadline) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
        $_SESSION['message'] = 'Invalid deadline format.';
        $_SESSION['message_type'] = 'danger';
        header('Location: mascot_admin.php#alertMessage');
        exit();
    }

    // Validasi file upload - Multiple files
    $projectImages = [];
    if (isset($_FILES['project_image']) && !empty($_FILES['project_image']['name'][0])) {
        for ($i = 0; $i < count($_FILES['project_image']['name']); $i++) {
            if ($_FILES['project_image']['error'][$i] === UPLOAD_ERR_OK) {
                $projectImage = uniqid() . '_' . basename($_FILES['project_image']['name'][$i]);
                if (move_uploaded_file($_FILES['project_image']['tmp_name'][$i], "uploads/projects/$projectImage")) {
                    $projectImages[] = $projectImage;
                }
            }
        }
    }

    $materialImages = [];
    if (isset($_FILES['material_image']) && !empty($_FILES['material_image']['name'][0])) {
        for ($i = 0; $i < count($_FILES['material_image']['name']); $i++) {
            if ($_FILES['material_image']['error'][$i] === UPLOAD_ERR_OK) {
                $materialImage = uniqid() . '_' . basename($_FILES['material_image']['name'][$i]);
                if (move_uploaded_file($_FILES['material_image']['tmp_name'][$i], "uploads/materials/$materialImage")) {
                    $materialImages[] = $materialImage;
                }
            }
        }
    }

    // Convert arrays to JSON for database storage
    $projectImagesJson = !empty($projectImages) ? json_encode($projectImages) : null;
    $materialImagesJson = !empty($materialImages) ? json_encode($materialImages) : null;

    // Simpan data ke database
    $stmt = $pdo->prepare("INSERT INTO gallery (project_name, project_status, priority, quantity, project_image, material_image, description, deadline, category, subform_embed)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'mascot', ?)");
    $success = $stmt->execute([$projectName, $projectStatus, $priority, $quantity, $projectImagesJson, $materialImagesJson, $description, $deadline, $subformEmbed]);
    if ($success) {
        $_SESSION['message'] = 'Project successfully uploaded!';
        $_SESSION['message_type'] = 'success';
        header('Location: mascot_admin.php#alertMessage'); // Arahkan ke bagian alert message
        exit();
    } else {
        $_SESSION['message'] = 'Failed to upload project.';
        $_SESSION['message_type'] = 'error';
        header('Location: mascot_admin.php#alertMessage'); // Arahkan ke bagian alert message
        exit();
    }
}
