<?php
include 'db.php';

function cleanFileName($filename) {
    return preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($filename));
}

$projectName = $_POST['project_name'];
$projectStatus = $_POST['project_status'];
$desc = $_POST['description'];
$deadline = $_POST['deadline'];


$projectImg = cleanFileName($_FILES['project_image']['name']);
$materialImg = cleanFileName($_FILES['material_image']['name']);

move_uploaded_file($_FILES['project_image']['tmp_name'], "uploads/projects/$projectImg");
move_uploaded_file($_FILES['material_image']['tmp_name'], "uploads/materials/$materialImg");

$stmt = $pdo->prepare("INSERT INTO gallery (project_name, project_status, project_image, material_image, description, deadline) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$projectName, $projectStatus, $projectImg, $materialImg, $desc, $deadline]);

header("Location: admin.php");
exit;
?>
