<?php
include 'db.php';
include 'middleware.php';
checkUserRole('mascot'); // Hanya mascot admin yang bisa mengakses

if (!isset($_GET['id'])) {
    echo 'ID tidak ditemukan.';
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ? AND category = 'mascot'");
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

    // Handle multiple file uploads
    $projectImages = [];
    $materialImages = [];

    // Get existing images
    if (!empty($data['project_image'])) {
        $decoded = json_decode($data['project_image'], true);
        $projectImages = is_array($decoded) ? $decoded : [$data['project_image']];
    }

    if (!empty($data['material_image'])) {
        $decoded = json_decode($data['material_image'], true);
        $materialImages = is_array($decoded) ? $decoded : [$data['material_image']];
    }

    // Handle new project images
    if (isset($_FILES['project_image']) && !empty($_FILES['project_image']['name'][0])) {
        $projectImages = []; // Replace all existing
        for ($i = 0; $i < count($_FILES['project_image']['name']); $i++) {
            if ($_FILES['project_image']['error'][$i] === UPLOAD_ERR_OK) {
                $projectImage = uniqid() . '_' . $_FILES['project_image']['name'][$i];
                if (move_uploaded_file($_FILES['project_image']['tmp_name'][$i], "uploads/projects/$projectImage")) {
                    $projectImages[] = $projectImage;
                }
            }
        }
    }

    // Handle new material images
    if (isset($_FILES['material_image']) && !empty($_FILES['material_image']['name'][0])) {
        $materialImages = []; // Replace all existing
        for ($i = 0; $i < count($_FILES['material_image']['name']); $i++) {
            if ($_FILES['material_image']['error'][$i] === UPLOAD_ERR_OK) {
                $materialImage = uniqid() . '_' . $_FILES['material_image']['name'][$i];
                if (move_uploaded_file($_FILES['material_image']['tmp_name'][$i], "uploads/materials/$materialImage")) {
                    $materialImages[] = $materialImage;
                }
            }
        }
    }

    // Convert to JSON
    $projectImagesJson = !empty($projectImages) ? json_encode($projectImages) : null;
    $materialImagesJson = !empty($materialImages) ? json_encode($materialImages) : null;

    if (!empty($subformEmbed) && !filter_var($subformEmbed, FILTER_VALIDATE_URL)) {
        $_SESSION['message'] = 'Invalid Google Slide URL.';
        $_SESSION['message_type'] = 'danger';
        header('Location: mascot_admin.php');
        exit();
    }

    $update = $pdo->prepare("UPDATE gallery SET project_name = ?, project_status = ?, priority = ?, quantity = ?, description = ?, deadline = ?, project_image = ?, material_image = ?, subform_embed = ? WHERE id = ? AND category = 'mascot'");
    $update->execute([$projectName, $projectStatus, $priority, $quantity, $description, $deadline, $projectImagesJson, $materialImagesJson, $subformEmbed, $id]);

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
        window.location.href = 'mascot_admin.php';
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
        <link rel="icon" type="image/x-icon" href="favicon.ico">
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
                    <div class="mb-3">
                        <label for="project_status" class="form-label">Project Status</label>
                        <select name="project_status" id="project_status" class="form-select" required>
                            <option value="Upcoming" <?= $data['project_status'] === 'Upcoming' ? 'selected' : '' ?>>
                                Upcoming</option>
                            <option value="Urgent" <?= $data['project_status'] === 'Urgent' ? 'selected' : '' ?>>
                                Urgent</option>
                            <option value="In Progress"
                                <?= $data['project_status'] === 'In Progress' ? 'selected' : '' ?>>
                                In Progress</option>
                            <option value="Revision" <?= $data['project_status'] === 'Revision' ? 'selected' : '' ?>>
                                Revision</option>
                            <option value="Completed" <?= $data['project_status'] === 'Completed' ? 'selected' : '' ?>>
                                Completed</option>
                            <option value="Archived" <?= $data['project_status'] === 'Archived' ? 'selected' : '' ?>>
                                Archived</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select name="priority" id="priority" class="form-select" required>
                            <option value="High" <?= $data['priority'] === 'High' ? 'selected' : '' ?>>High
                            </option>
                            <option value="Medium" <?= $data['priority'] === 'Medium' ? 'selected' : '' ?>>Medium
                            </option>
                            <option value="Normal" <?= $data['priority'] === 'Normal' ? 'selected' : '' ?>>Normal
                            </option>
                            <option value="Low" <?= $data['priority'] === 'Low' ? 'selected' : '' ?>>Low
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantity">Quantity:</label>
                        <input type="number" class="form-control w-auto" id="quantity" name="quantity"
                            value="<?= htmlspecialchars($data['quantity']) ?>" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="deadline" class="form-label">Deadline</label>
                        <input type="date" name="deadline" id="deadline" class="form-control"
                            value="<?= htmlspecialchars($data['deadline'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4" required><?= htmlspecialchars($data['description']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="project_image" class="form-label">Project Images (Kosongkan jika tidak
                            diubah)</label>
                        <input type="file" name="project_image[]" id="project_image" class="form-control" multiple
                            accept="image/*">
                        <small class="text-muted">Pilih beberapa gambar untuk mengganti semua gambar project yang
                            ada</small>

                        <div class="mt-3">
                            <label class="form-label">Current Project Images:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                            $projectImages = [];
                            if (!empty($data['project_image'])) {
                                $decoded = json_decode($data['project_image'], true);
                                $projectImages = is_array($decoded) ? $decoded : [$data['project_image']];
                            }
                            
                            foreach ($projectImages as $index => $image): ?>
                                <div class="position-relative">
                                    <img src="uploads/projects/<?= htmlspecialchars($image) ?>" width="120"
                                        class="border rounded">
                                    <span
                                        class="badge bg-primary position-absolute top-0 start-0"><?= $index + 1 ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div id="project_image_preview" class="mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label for="material_image" class="form-label">Submission Notes (Kosongkan jika tidak
                            diubah)</label>
                        <input type="file" name="material_image[]" id="material_image" class="form-control"
                            multiple accept="image/*">
                        <small class="text-muted">Pilih beberapa gambar untuk mengganti semua gambar material yang
                            ada</small>

                        <div class="mt-3">
                            <label class="form-label">Current Material Images:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                            $materialImages = [];
                            if (!empty($data['material_image'])) {
                                $decoded = json_decode($data['material_image'], true);
                                $materialImages = is_array($decoded) ? $decoded : [$data['material_image']];
                            }
                            
                            foreach ($materialImages as $index => $image): ?>
                                <div class="position-relative">
                                    <img src="uploads/materials/<?= htmlspecialchars($image) ?>" width="120"
                                        class="border rounded">
                                    <span
                                        class="badge bg-primary position-absolute top-0 start-0"><?= $index + 1 ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div id="material_image_preview" class="mt-2"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="mascot_admin.php" class="btn btn-secondary">Kembali</a>
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
            // Fungsi untuk preview multiple images
            function previewMultipleImages(input, previewContainerId) {
                const previewContainer = document.getElementById(previewContainerId);
                previewContainer.innerHTML = '';

                if (input.files && input.files.length > 0) {
                    for (let i = 0; i < input.files.length; i++) {
                        const file = input.files[i];
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const imgWrapper = document.createElement('div');
                            imgWrapper.className = 'd-inline-block position-relative me-2 mb-2';

                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.width = '120px';
                            img.style.height = '120px';
                            img.style.objectFit = 'cover';
                            img.className = 'border rounded';

                            const badge = document.createElement('span');
                            badge.className = 'badge bg-success position-absolute top-0 start-0';
                            badge.textContent = 'New ' + (i + 1);

                            imgWrapper.appendChild(img);
                            imgWrapper.appendChild(badge);
                            previewContainer.appendChild(imgWrapper);
                        };

                        reader.readAsDataURL(file);
                    }
                }
            }

            // Fungsi untuk memperbarui preview gambar (backward compatibility)
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
                previewMultipleImages(this, 'project_image_preview');
            });

            // Event listener untuk Material Image
            document.getElementById('material_image').addEventListener('change', function() {
                previewMultipleImages(this, 'material_image_preview');
            });
        </script>
    </body>

</html>
