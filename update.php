<?php
include 'db.php';

function sanitizeFileName($filename) {
    return preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($filename));
}

$id = $_POST['id'];
$desc = $_POST['description'];
$deadline = $_POST['deadline'];

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

$sql = "UPDATE gallery SET project_image = ?, material_image = ?, description = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$project, $material, $desc, $id]);

header("Location: admin.php");
exit;
?>