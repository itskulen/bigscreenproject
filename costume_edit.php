<?php
include 'db.php';
include 'middleware.php';
checkUserRole('costume'); // Hanya costume admin yang bisa mengakses

if (!isset($_GET['id'])) {
    echo 'ID tidak ditemukan.';
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ? AND category = 'costume'");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    echo 'Data tidak ditemukan atau Anda tidak memiliki akses.';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $projectName = $_POST['project_name'];
    $subformEmbed = $_POST['subform_embed'] ?? null;
    $projectStatus = $_POST['project_status'];
    $priority = $_POST['priority'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    // Cek apakah user upload gambar baru atau tidak
    $projectImage = $data['project_image'];
    if ($_FILES['project_image']['name']) {
        $projectImage = uniqid() . '_' . $_FILES['project_image']['name'];
        move_uploaded_file($_FILES['project_image']['tmp_name'], "uploads/projects/$projectImage");
    }

    $materialImage = $data['material_image'];
    if ($_FILES['material_image']['name']) {
        $materialImage = uniqid() . '_' . $_FILES['material_image']['name'];
        move_uploaded_file($_FILES['material_image']['tmp_name'], "uploads/materials/$materialImage");
    }

    if (!empty($subformEmbed) && !filter_var($subformEmbed, FILTER_VALIDATE_URL)) {
        $_SESSION['message'] = 'Invalid Google Slide URL.';
        $_SESSION['message_type'] = 'danger';
        header('Location: costume_admin.php');
        exit();
    }

    $update = $pdo->prepare("UPDATE gallery SET project_name = ?, project_status = ?, priority = ?, quantity = ?, description = ?, deadline = ?, project_image = ?, material_image = ?, subform_embed = ? WHERE id = ? AND category = 'costume'");
    $update->execute([$projectName, $projectStatus, $priority, $quantity, $description, $deadline, $projectImage, $materialImage, $subformEmbed, $id]);

    echo "<!DOCTYPE html>
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Project successfully updated!',
        confirmButtonText: 'OK'
    }).then(() => {
        window.location.href = 'costume_admin.php';
    });
    </script>
    </body>
    </html>";
    exit();
}
?>

<!DOCTYPE html>
<html>

    <head>
        <title>Edit Project</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>
            body {
                background-color: #f8f9fa;
            }

            .form-container {
                background: #ffffff;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .form-container h2 {
                margin-bottom: 20px;
            }

            .form-container img {
                margin-top: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 5px;
            }
        </style>
    </head>

    <body>
        <div class="container my-5">
            <div class="form-container mx-auto" style="max-width: 600px;">
                <h2 class="text-center">Edit Project</h2>
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="project_name" class="form-label">Project Name</label>
                        <input type="text" name="project_name" id="project_name" class="form-control"
                            value="<?= htmlspecialchars($data['project_name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="subform_embed" class="form-label">Submission Form Embed Link</label>
                        <textarea name="subform_embed" id="subform_embed" class="form-control" placeholder="Enter Submission Form Embed Link"><?= htmlspecialchars($data['subform_embed'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="project_status" class="form-label">Project Status</label>
                                <select name="project_status" id="project_status" class="form-select" required>
                                    <option value="">-- Select Status --</option>
                                    <option value="Upcoming"
                                        <?= $data['project_status'] === 'Upcoming' ? 'selected' : '' ?>>
                                        Upcoming</option>
                                    <option value="Urgent"
                                        <?= $data['project_status'] === 'Urgent' ? 'selected' : '' ?>>
                                        Urgent</option>
                                    <option value="In Progress"
                                        <?= $data['project_status'] === 'In Progress' ? 'selected' : '' ?>>
                                        In Progress</option>
                                    <option value="Revision"
                                        <?= $data['project_status'] === 'Revision' ? 'selected' : '' ?>>
                                        Revision</option>
                                    <option value="Completed"
                                        <?= $data['project_status'] === 'Completed' ? 'selected' : '' ?>>
                                        Completed</option>
                                    <option value="Archived"
                                        <?= $data['project_status'] === 'Archived' ? 'selected' : '' ?>>
                                        Archived</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select name="priority" id="priority" class="form-select" required>
                                    <option value="High" <?= $data['priority'] === 'High' ? 'selected' : '' ?>>
                                        High</option>
                                    <option value="Medium" <?= $data['priority'] === 'Medium' ? 'selected' : '' ?>>
                                        Medium</option>
                                    <option value="Low" <?= $data['priority'] === 'Low' ? 'selected' : '' ?>>Low
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity">Quantity:</label>
                                <input type="number" class="form-control" id="quantity" name="quantity"
                                    value="<?= htmlspecialchars($data['quantity']) ?>" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deadline" class="form-label">Deadline</label>
                                <input type="date" name="deadline" id="deadline" class="form-control"
                                    value="<?= htmlspecialchars($data['deadline'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4" required><?= htmlspecialchars($data['description']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="project_image" class="form-label">Project Image (Kosongkan jika tidak
                            diubah)</label>
                        <div class="d-flex align-items-center">
                            <input type="file" name="project_image" id="project_image" class="form-control me-2">
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="resetFileInput('project_image', 'project_image_preview', 'uploads/projects/<?= $data['project_image'] ?>')">Reset</button>
                        </div>
                        <img id="project_image_preview" src="uploads/projects/<?= $data['project_image'] ?>"
                            width="150" alt="Project Image" class="mt-2 border">
                    </div>

                    <div class="mb-3">
                        <label for="material_image" class="form-label">Submission Notes (Kosongkan jika tidak
                            diubah)</label>
                        <div class="d-flex align-items-center">
                            <input type="file" name="material_image" id="material_image"
                                class="form-control me-2">
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="resetFileInput('material_image', 'material_image_preview', 'uploads/materials/<?= $data['material_image'] ?>')">Reset</button>
                        </div>
                        <img id="material_image_preview" src="uploads/materials/<?= $data['material_image'] ?>"
                            width="150" alt="Material Image" class="mt-2 border">
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="costume_admin.php" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
        <footer class="text-secondary text-center py-1 mt-4" style="background-color: rgba(0, 0, 0, 0.05);">
            <div class="mb-0">Create with ❤️ by <a class="text-primary fw-bold" href=""
                    style="text-decoration: none;">IT
                    DCM</a></div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Fungsi untuk memperbarui preview gambar
            function previewImage(input, previewId) {
                const file = input.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById(previewId).src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }

            // Fungsi untuk mereset input file dan mengembalikan preview ke gambar asli
            function resetFileInput(inputId, previewId, originalSrc) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);

                // Reset input file
                input.value = "";

                // Kembalikan preview ke gambar asli
                preview.src = originalSrc;
            }

            // Event listener untuk Project Image
            document.getElementById('project_image').addEventListener('change', function() {
                previewImage(this, 'project_image_preview');
            });

            // Event listener untuk Submission Notes
            document.getElementById('material_image').addEventListener('change', function() {
                previewImage(this, 'material_image_preview');
            });
        </script>
    </body>

</html>
