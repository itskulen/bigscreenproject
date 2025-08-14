<?php
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

$id = $_POST['id'];
$desc = htmlspecialchars(trim($_POST['description']));
$subformEmbed = trim($_POST['subform_embed']);
$subformEmbed = $subformEmbed === '' ? null : htmlspecialchars($subformEmbed); // Konversi string kosong ke NULL
$deadline = htmlspecialchars(trim($_POST['deadline']));
$priority = htmlspecialchars(trim($_POST['priority']));
$quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) && $_POST['quantity'] > 0 ? $_POST['quantity'] : 1; // Default ke 1 jika tidak valid

// Debugging untuk memastikan nilai deadline dikirim
if (!empty($deadline) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
    die('Invalid date format.');
}

if (empty($desc)) {
    die('Description is required.');
}

if (empty($priority)) {
    die('Priority is required.');
}

$stmt = $pdo->prepare('SELECT * FROM gallery WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch();

// Get existing images (handle both old string format and new JSON format)
$existingProjectImages = [];
$existingMaterialImages = [];

if (!empty($row['project_image'])) {
    $decoded = json_decode($row['project_image'], true);
    $existingProjectImages = is_array($decoded) ? $decoded : [$row['project_image']];
}

if (!empty($row['material_image'])) {
    $decoded = json_decode($row['material_image'], true);
    $existingMaterialImages = is_array($decoded) ? $decoded : [$row['material_image']];
}

// Handle new file uploads
$projectImages = $existingProjectImages;
$materialImages = $existingMaterialImages;

if (isset($_FILES['project_image']) && !empty($_FILES['project_image']['name'][0])) {
    $projectImages = []; // Replace all existing images
    for ($i = 0; $i < count($_FILES['project_image']['name']); $i++) {
        if ($_FILES['project_image']['error'][$i] === UPLOAD_ERR_OK) {
            // Generate shorter filename: timestamp + sanitized name
            $timestamp = time() . substr(microtime(), 2, 3);
            $cleanName = sanitizeFileName($_FILES['project_image']['name'][$i]);
            $projectImage = $timestamp . '_' . $cleanName;

            if (move_uploaded_file($_FILES['project_image']['tmp_name'][$i], "uploads/projects/$projectImage")) {
                $projectImages[] = $projectImage;
            }
        }
    }
}

if (isset($_FILES['material_image']) && !empty($_FILES['material_image']['name'][0])) {
    $materialImages = []; // Replace all existing images
    for ($i = 0; $i < count($_FILES['material_image']['name']); $i++) {
        if ($_FILES['material_image']['error'][$i] === UPLOAD_ERR_OK) {
            // Generate shorter filename: timestamp + sanitized name
            $timestamp = time() . substr(microtime(), 2, 3);
            $cleanName = sanitizeFileName($_FILES['material_image']['name'][$i]);
            $materialImage = $timestamp . '_' . $cleanName;

            if (move_uploaded_file($_FILES['material_image']['tmp_name'][$i], "uploads/materials/$materialImage")) {
                $materialImages[] = $materialImage;
            }
        }
    }
}

// Convert to JSON for database
$projectImagesJson = !empty($projectImages) ? json_encode($projectImages) : null;
$materialImagesJson = !empty($materialImages) ? json_encode($materialImages) : null;

$sql = "UPDATE gallery
        SET project_image = ?, material_image = ?, description = ?, deadline = ?, quantity = ?, priority = ?, subform_embed = ?
        WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$projectImagesJson, $materialImagesJson, $desc, $deadline, $quantity, $priority, $subformEmbed, $id]);

// Feedback untuk pengguna
if ($stmt->rowCount() > 0) {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Project successfully updated!',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'mascot_admin.php';
        });
    </script>";
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No changes were made.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'mascot_admin.php';
        });
    </script>";
}
exit();