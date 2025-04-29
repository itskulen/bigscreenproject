<?php
include 'db.php';

function sanitizeFileName($filename)
{
    return preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($filename));
}

$id = $_POST['id'];
$desc = htmlspecialchars(trim($_POST['description']));
$deadline = $_POST['deadline'];
$quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) && $_POST['quantity'] > 0
    ? $_POST['quantity']
    : 1; // Default ke 1 jika tidak valid
    
// Debugging untuk memastikan nilai deadline dikirim
if (empty($deadline)) {
    die("Deadline is empty.");
}

// Validasi format tanggal
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
    die("Invalid date format.");
}

$stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

$project = $row['project_image'];
$material = $row['material_image'];

if ($_FILES['project_image']['name']) {
    $project = sanitizeFileName($_FILES['project_image']['name']);
    move_uploaded_file($_FILES['project_image']['tmp_name'], "uploads/projects/$project");
}
if ($_FILES['material_image']['name']) {
    $material = sanitizeFileName($_FILES['material_image']['name']);
    move_uploaded_file($_FILES['material_image']['tmp_name'], "uploads/materials/$material");
}

$sql = "UPDATE gallery 
        SET project_image = ?, material_image = ?, description = ?, deadline = ?, quantity = ?, priority = ? 
        WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$project, $material, $desc, $deadline, $quantity, $priority, $id]);

header("Location: mascot_admin.php");
exit;