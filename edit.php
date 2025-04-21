<?php
include 'db.php';

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $projectName = $_POST['project_name'];
    $projectStatus = $_POST['project_status'];
    $description = $_POST['description'];

    // Cek apakah user upload gambar baru atau tidak
    $projectImage = $data['project_image'];
    if ($_FILES['project_image']['name']) {
        $projectImage = uniqid() . "_" . $_FILES['project_image']['name'];
        move_uploaded_file($_FILES['project_image']['tmp_name'], "uploads/projects/$projectImage");
    }

    $materialImage = $data['material_image'];
    if ($_FILES['material_image']['name']) {
        $materialImage = uniqid() . "_" . $_FILES['material_image']['name'];
        move_uploaded_file($_FILES['material_image']['tmp_name'], "uploads/materials/$materialImage");
    }

    $update = $pdo->prepare("UPDATE gallery SET project_name = ?, project_status = ?, description = ?, project_image = ?, material_image = ? WHERE id = ?");
    $update->execute([$projectName, $projectStatus, $description, $projectImage, $materialImage, $id]);

    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Project</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Project</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Project Name</label>
            <input type="text" name="project_name" class="form-control" value="<?= htmlspecialchars($data['project_name']) ?>" required>
        </div>
        <div class="mb-3">
    <label for="project_status" class="form-label">Project Status</label>
    <select name="project_status" id="project_status" class="form-select" required>
      <option value="">-- Select Status --</option>
      <option value="Not Started">Not Started</option>
      <option value="In Progress">In Progress</option>
      <option value="Revision">Revision</option>
      <option value="Completed">Completed</option>
    </select>
  </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($data['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Deadline</label>
            <input type="date" textarea name="deadline" class="form-control"  required><?= htmlspecialchars($data['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Project Image (Kosongkan jika tidak diubah)</label><br>
            <img src="uploads/projects/<?= $data['project_image'] ?>" width="100"><br>
            <input type="file" name="project_image" class="form-control-file">
        </div>
        <div class="form-group">
            <label>Material Image (Kosongkan jika tidak diubah)</label><br>
            <img src="uploads/materials/<?= $data['material_image'] ?>" width="100"><br>
            <input type="file" name="material_image" class="form-control-file">
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="admin.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
