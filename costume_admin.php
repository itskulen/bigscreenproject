<?php
session_start();
include 'db.php';
include 'middleware.php';
checkUserRole('costume'); // Hanya costume_manager yang bisa mengakses

$sql = "SELECT project_name, project_status, project_image, material_image, description, deadline 
        FROM gallery WHERE category = 'costume'";
$result = $pdo->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Costume - Project</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .drop-zone {
            border: 2px dashed #007bff;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            margin-bottom: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .drop-zone:hover {
            background: #e9ecef;
        }

        .container {
            margin-top: 30px;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }

        .top-right {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="top-left">
            <a href="logout.php">
                <button type="button" class="btn btn-danger">Logout</button>
            </a>
            <a href="costume_index.php" class="ml-2" target="_blank">
                <button type="button" class="btn btn-success">View Costume Projects</button>
            </a>
        </div>

        <h2 class="text-center">Upload Project Costume</h2>
        <form id="uploadForm" action="costume_upload.php" method="POST" enctype="multipart/form-data" novalidate>
            <div class="form-group">
                <label for="project_name">Project Name:</label>
                <input type="text" class="form-control" id="project_name" name="project_name" required>
                <div class="error" id="project_name_error"></div>
            </div>

            <div class="form-group">
                <label for="project_status" class="form-label">Project Status</label>
                <select class="form-select" name="project_status" id="project_status"
                    aria-label="Default select example" required>
                    <option selected>Select Status</option>
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Revision">Revision</option>
                    <option value="Completed">Completed</option>
                </select>
                <div class="error" id="project_status_error"></div>
            </div>

            <div class="form-group">
                <label>Project Image:</label>
                <div class="drop-zone" onclick="document.getElementById('project_image').click();">
                    Click or Drag to Upload Project Image
                </div>
                <input type="file" id="project_image" name="project_image" accept="image/*" style="display:none;"
                    required>
                <img id="project_image_preview" src="#" alt="Project Image Preview"
                    style="display:none; margin-top:10px; max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                <div class="error" id="project_image_error"></div>
            </div>

            <div class="form-group">
                <label>Material Image:</label>
                <div class="drop-zone" onclick="document.getElementById('material_image').click();">
                    Click or Drag to Upload Material Image
                </div>
                <input type="file" id="material_image" name="material_image" accept="image/*" style="display:none;"
                    required>
                <img id="material_image_preview" src="#" alt="Material Image Preview"
                    style="display:none; margin-top:10px; max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                <div class="error" id="material_image_error"></div>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                <div class="error" id="description_error"></div>
            </div>

            <div class="form-group">
                <label for="deadline">Deadline:</label>
                <input type="date" class="form-control" id="deadline" name="deadline" required>
                <div class="error" id="deadline_error"></div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Upload</button>
        </form>
    </div>
    <hr>
    <div class="container mt-5">
        <h2>Daftar Project</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Status</th>
                    <th>Project Image</th>
                    <th>Material Image</th>
                    <th>Description</th>
                    <th>Deadline</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM gallery WHERE category = 'costume' ORDER BY id DESC");
                while ($row = $stmt->fetch()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['project_name']) ?></td>
                        <td>
                            <select class="form-select" onchange="updateStatus(<?= $row['id'] ?>, this.value, 'costume')">
                                <option value="Not Started"
                                    <?= $row['project_status'] === 'Not Started' ? 'selected' : '' ?>>Not Started</option>
                                <option value="In Progress"
                                    <?= $row['project_status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="Revision" <?= $row['project_status'] === 'Revision' ? 'selected' : '' ?>>
                                    Revision</option>
                                <option value="Completed" <?= $row['project_status'] === 'Completed' ? 'selected' : '' ?>>
                                    Completed</option>
                            </select>
                        </td>
                        <td>
                            <img src="uploads/projects/<?= $row['project_image'] ?>" width="100"><br>
                        </td>
                        <td>
                            <img src="uploads/materials/<?= $row['material_image'] ?>" width="100"><br>
                        </td>
                        <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['deadline'])) ?></td>
                        <td>
                            <a href="costume_edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="costume_delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Fungsi untuk menampilkan preview gambar
        function previewImage(input, previewId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(previewId);
                    preview.src = e.target.result;
                    preview.style.display = "block";
                };
                reader.readAsDataURL(file);
            }
        }

        // Event listener untuk Project Image
        document.getElementById('project_image').addEventListener('change', function() {
            previewImage(this, 'project_image_preview');
        });

        // Event listener untuk Material Image
        document.getElementById('material_image').addEventListener('change', function() {
            previewImage(this, 'material_image_preview');
        });

        // Validasi Form
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            let isValid = true;

            // Validasi Project Name
            const projectName = document.getElementById('project_name');
            if (!projectName.value.trim()) {
                isValid = false;
                document.getElementById('project_name_error').textContent = "Project Name is required.";
            } else {
                document.getElementById('project_name_error').textContent = "";
            }

            // Validasi Project Status
            const projectStatus = document.getElementById('project_status');
            if (!projectStatus.value) {
                isValid = false;
                document.getElementById('project_status_error').textContent = "Project Status is required.";
            } else {
                document.getElementById('project_status_error').textContent = "";
            }
        });

        function updateStatus(id, status, category) {
            fetch('update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: id,
                        status: status,
                        category: category
                    }), // Tambahkan kategori
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Status updated successfully!');
                    } else {
                        alert('Failed to update status.');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>