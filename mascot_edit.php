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
    $type = $_POST['type'] ?? null;
    $subformEmbed = $_POST['subform_embed'] ?? null;
    $projectStatus = $_POST['project_status'];
    $priority = $_POST['priority'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    // Handle multiple file uploads
    $projectImages = [];
    $materialImages = [];

    if ($deadline === '') {
        $deadline = null;
    }

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

    $update = $pdo->prepare("UPDATE gallery SET project_name = ?, project_status = ?, priority = ?, quantity = ?, description = ?, deadline = ?, project_image = ?, material_image = ?, type = ?, subform_embed = ? WHERE id = ? AND category = 'mascot'");
    $update->execute([$projectName, $projectStatus, $priority, $quantity, $description, $deadline, $projectImagesJson, $materialImagesJson, $type, $subformEmbed, $id]);

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
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                margin: 0;
                padding: 20px;
            }

            .main-container {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border-radius: 15px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
                padding: 0;
                border: 1px solid rgba(255, 255, 255, 0.2);
                overflow: hidden;
                max-width: 800px;
                margin: 0 auto;
            }

            .header-section {
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                color: white;
                padding: 20px;
                text-align: center;
                position: relative;
                overflow: hidden;
            }

            .floating-buttons {
                position: absolute;
                top: 15px;
                right: 15px;
                display: flex;
                gap: 8px;
                z-index: 10;
            }

            .floating-btn {
                background: rgba(255, 255, 255, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.3);
                color: white;
                padding: 8px 12px;
                border-radius: 8px;
                text-decoration: none;
                font-size: 0.9rem;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .floating-btn:hover {
                background: rgba(255, 255, 255, 0.25);
                transform: translateY(-2px);
                color: white;
                text-decoration: none;
            }

            /* Responsive Breakpoints */
            @media (max-width: 768px) {
                .main-container {
                    margin: 10px;
                    border-radius: 10px;
                    max-width: none;
                }

                .header-section {
                    padding: 15px;
                }

                .header-section h2 {
                    font-size: 1.3rem;
                }

                .floating-buttons {
                    position: static;
                    justify-content: center;
                    margin-bottom: 10px;
                }

                .floating-btn {
                    font-size: 0.8rem;
                    padding: 6px 10px;
                }

                .form-container {
                    padding: 20px;
                }
            }

            @media (max-width: 576px) {
                .floating-btn {
                    flex-direction: column;
                    padding: 8px;
                    font-size: 0.7rem;
                    gap: 2px;
                }

                .floating-btn i {
                    font-size: 0.8rem;
                }

                .header-section h2 {
                    font-size: 1.1rem;
                }

                .form-container {
                    padding: 15px;
                }
            }

            @keyframes shimmer {
                0% {
                    left: -100%;
                }

                100% {
                    left: 100%;
                }
            }

            .header-section h2 {
                margin: 0;
                font-weight: 700;
                font-size: 1.6rem;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
                position: relative;
                z-index: 1;
            }

            .form-container {
                padding: 25px;
                background: transparent;
                box-shadow: none;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-label {
                font-weight: 600;
                color: #4a5568;
                margin-bottom: 8px;
                display: block;
                font-size: 15px;
            }

            .form-control,
            .form-select {
                border-radius: 12px;
                border: 2px solid #e9ecef;
                padding: 12px 16px;
                font-size: 14px;
                transition: all 0.3s ease;
                background: rgba(255, 255, 255, 0.9);
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #8b5cf6;
                box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
                background: white;
                transform: translateY(-1px);
            }

            .form-control:hover,
            .form-select:hover {
                border-color: #c4b5fd;
            }

            .current-images-section {
                background: rgba(139, 92, 246, 0.05);
                border-radius: 12px;
                padding: 20px;
                margin-top: 15px;
                border: 2px dashed rgba(139, 92, 246, 0.2);
            }

            .current-images-section .form-label {
                color: #8b5cf6;
                font-weight: 700;
                margin-bottom: 15px;
            }

            .image-preview-container {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
            }

            .image-preview-item {
                position: relative;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                background: white;
                padding: 5px;
            }

            .image-preview-item:hover {
                transform: translateY(-5px) scale(1.05);
                box-shadow: 0 15px 35px rgba(139, 92, 246, 0.2);
            }

            .image-preview-item img {
                border-radius: 8px;
                transition: all 0.3s ease;
            }

            .image-preview-item .badge {
                position: absolute;
                top: 8px;
                left: 8px;
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                color: white;
                font-weight: 600;
                padding: 4px 8px;
                border-radius: 6px;
                font-size: 12px;
                box-shadow: 0 4px 10px rgba(139, 92, 246, 0.3);
            }

            .btn-group-modern {
                display: flex;
                gap: 15px;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 2px solid rgba(139, 92, 246, 0.1);
            }

            .btn-modern {
                border-radius: 25px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                border: none;
                padding: 12px 30px;
                font-size: 16px;
                flex: 1;
            }

            .btn-modern:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            }

            .btn-modern.btn-primary {
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                color: white;
            }

            .btn-modern.btn-primary:hover {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            .btn-modern.btn-secondary {
                background: linear-gradient(135deg, #6b7280, #4b5563);
                color: white;
            }

            .btn-modern.btn-secondary:hover {
                background: linear-gradient(135deg, #4b5563, #374151);
            }

            .file-upload-section {
                position: relative;
                margin-top: 15px;
            }

            .file-info {
                background: rgba(34, 197, 94, 0.1);
                color: #059669;
                padding: 8px 12px;
                border-radius: 8px;
                font-size: 13px;
                margin-top: 5px;
                border: 1px solid rgba(34, 197, 94, 0.2);
            }

            .preview-section {
                margin-top: 20px;
                padding: 20px;
                background: rgba(249, 250, 251, 0.8);
                border-radius: 12px;
                border: 2px dashed #d1d5db;
                min-height: 120px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .preview-section.has-images {
                border-color: #10b981;
                background: rgba(16, 185, 129, 0.05);
            }

            .preview-section.empty {
                color: #9ca3af;
                font-style: italic;
            }

            @media (max-width: 768px) {
                .btn-group-modern {
                    flex-direction: column;
                }

                .image-preview-container {
                    justify-content: center;
                }

                .header-section h2 {
                    font-size: 1.5rem;
                }

                .form-container {
                    padding: 20px;
                }
            }
        </style>
    </head>

    <body>
        <div class="main-container">
            <div class="header-section">
                <div class="floating-buttons">
                    <a href="logout.php" class="floating-btn">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                    <a href="mascot_admin.php" class="floating-btn">
                        <i class="bi bi-arrow-left"></i>
                        Back
                    </a>
                </div>
                <h2><i class="bi bi-pencil-square me-2"></i>Edit Project</h2>
            </div>

            <div class="form-container">
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
                        <label for="type" class="form-label">Mascot Category</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Select Category</option>
                            <option value="compressed foam"
                                <?= ($data['type'] ?? '') == 'compressed foam' ? 'selected' : '' ?>>Compressed
                                Foam</option>
                            <option value="inflatable" <?= ($data['type'] ?? '') == 'inflatable' ? 'selected' : '' ?>>
                                Inflatable
                            </option>
                            <option value="statue" <?= ($data['type'] ?? '') == 'statue' ? 'selected' : '' ?>>
                                Statue</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="project_status" class="form-label">Project Status</label>
                                <select name="project_status" id="project_status" class="form-select" required>
                                    <option value="">Select Status</option>
                                    <option value="Upcoming"
                                        <?= $data['project_status'] === 'Upcoming' ? 'selected' : '' ?>>
                                        Upcoming</option>
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
                                    <option value="">Select Priority</option>
                                    <option value="Urgent" <?= $data['priority'] === 'Urgent' ? 'selected' : '' ?>>
                                        Urgent
                                    </option>
                                    <option value="High" <?= $data['priority'] === 'High' ? 'selected' : '' ?>>
                                        High
                                    </option>
                                    <option value="Normal" <?= $data['priority'] === 'Normal' ? 'selected' : '' ?>>
                                        Normal
                                    </option>
                                    <option value="Low" <?= $data['priority'] === 'Low' ? 'selected' : '' ?>>Low
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity:</label>
                                <input type="number" class="form-control" id="quantity" name="quantity"
                                    value="<?= htmlspecialchars($data['quantity']) ?>" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deadline" class="form-label">Deadline</label>
                                <input type="date" name="deadline" id="deadline" class="form-control"
                                    value="<?= htmlspecialchars($data['deadline'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4" required><?= htmlspecialchars($data['description']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="project_image" class="form-label">
                            <i class="bi bi-images me-2"></i>Project Images
                        </label>
                        <input type="file" name="project_image[]" id="project_image" class="form-control"
                            multiple accept="image/*">
                        <div class="file-info">
                            <i class="bi bi-info-circle me-1"></i>
                            Select multiple images to replace all existing project images
                        </div>

                        <div class="current-images-section">
                            <label class="form-label">
                                <i class="bi bi-folder-open me-2"></i>Current Project Images
                            </label>
                            <div class="image-preview-container">
                                <?php 
                            $projectImages = [];
                            if (!empty($data['project_image'])) {
                                $decoded = json_decode($data['project_image'], true);
                                $projectImages = is_array($decoded) ? $decoded : [$data['project_image']];
                            }
                            
                            foreach ($projectImages as $index => $image): ?>
                                <div class="image-preview-item">
                                    <img src="uploads/projects/<?= htmlspecialchars($image) ?>" width="120"
                                        class="border rounded" loading="lazy">
                                    <span class="badge"><?= $index + 1 ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div id="project_image_preview" class="preview-section empty">
                            <span>New images will appear here...</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="material_image" class="form-label">
                            <i class="bi bi-file-earmark-image me-2"></i>Submission Notes
                        </label>
                        <input type="file" name="material_image[]" id="material_image" class="form-control"
                            multiple accept="image/*">
                        <div class="file-info">
                            <i class="bi bi-info-circle me-1"></i>
                            Select multiple images to replace all existing Submission Notes images
                        </div>

                        <div class="current-images-section">
                            <label class="form-label">
                                <i class="bi bi-folder-open me-2"></i>Current Submission Notes Images
                            </label>
                            <div class="image-preview-container">
                                <?php 
                            $materialImages = [];
                            if (!empty($data['material_image'])) {
                                $decoded = json_decode($data['material_image'], true);
                                $materialImages = is_array($decoded) ? $decoded : [$data['material_image']];
                            }
                            
                            foreach ($materialImages as $index => $image): ?>
                                <div class="image-preview-item">
                                    <img src="uploads/materials/<?= htmlspecialchars($image) ?>" width="120"
                                        class="border rounded" loading="lazy">
                                    <span class="badge"><?= $index + 1 ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div id="material_image_preview" class="preview-section empty">
                            <span>New images will appear here...</span>
                        </div>
                    </div>

                    <div class="btn-group-modern">
                        <button type="submit" class="btn btn-modern btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Save Changes
                        </button>
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
                previewContainer.className = 'preview-section';

                if (input.files && input.files.length > 0) {
                    previewContainer.classList.add('has-images');
                    for (let i = 0; i < input.files.length; i++) {
                        const file = input.files[i];
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const imgWrapper = document.createElement('div');
                            imgWrapper.className = 'image-preview-item';

                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.width = '120px';
                            img.style.height = '120px';
                            img.style.objectFit = 'cover';
                            img.className = 'border rounded';

                            const badge = document.createElement('span');
                            badge.className = 'badge';
                            badge.textContent = 'New ' + (i + 1);
                            badge.style.background = 'linear-gradient(135deg, #10b981, #059669)';

                            imgWrapper.appendChild(img);
                            imgWrapper.appendChild(badge);
                            previewContainer.appendChild(imgWrapper);
                        };

                        reader.readAsDataURL(file);
                    }
                } else {
                    previewContainer.classList.add('empty');
                    previewContainer.innerHTML = '<span>New images will appear here...</span>';
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
