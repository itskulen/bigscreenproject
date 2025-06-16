<?php
include 'db.php';

function sanitizeFileName($filename)
{
    return preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($filename));
}

$id = $_POST['id'];
$desc = htmlspecialchars(trim($_POST['description']));
$subformEmbed = trim($_POST['subform_embed']);
$subformEmbed = $subformEmbed === "" ? null : htmlspecialchars($subformEmbed); // Konversi string kosong ke NULL
$deadline = htmlspecialchars(trim($_POST['deadline']));
$priority = htmlspecialchars(trim($_POST['priority']));
$quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) && $_POST['quantity'] > 0
    ? $_POST['quantity']
    : 1; // Default ke 1 jika tidak valid

// Debugging untuk memastikan nilai deadline dikirim
if (!empty($deadline) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
    die("Invalid date format.");
}

if (empty($desc)) {
    die("Description is required.");
}

if (empty($priority)) {
    die("Priority is required.");
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
        SET project_image = ?, material_image = ?, description = ?, deadline = ?, quantity = ?, priority = ?, subform_embed = ?
        WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$project, $material, $desc, $deadline, $quantity, $priority, $subformEmbed, $id]);

// Feedback untuk pengguna
if ($stmt->rowCount() > 0) {
    echo "<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Project successfully updated!',
        confirmButtonText: 'OK'
    }).then(() => {
        window.location.href = 'costume_admin.php';
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
        window.location.href = 'costume_admin.php';
    });
    </script>";
}
exit;