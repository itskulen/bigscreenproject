<?php
session_start();
include 'db.php';
include 'middleware.php';
include 'image_helper.php';
checkUserRole('costume'); // Hanya costume admin yang bisa mengakses
require 'vendor/autoload.php';

use Carbon\Carbon;

$sql = "SELECT project_name, project_status, priority, quantity, project_image, material_image, description, deadline,
createAt, updateAt
FROM gallery WHERE category = 'costume' ORDER BY createAt DESC";
$result = $pdo->query($sql);
?>
<!DOCTYPE html>
<html>

    <head>
        <title>Admin Costume - Project</title>
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                margin: 0;
                padding: 20px;
                min-height: 100vh;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .main-container {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border-radius: 15px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
                padding: 0;
                border: 1px solid rgba(255, 255, 255, 0.2);
                overflow: hidden;
                width: calc(100% - 40px);
                max-width: none;
                margin: 0 auto;
            }

            .header-section {
                background: linear-gradient(135deg, #667eea, #764ba2);
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

            .header-section h1 {
                margin: 0;
                font-weight: 700;
                font-size: 1.5rem;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
                position: relative;
                z-index: 1;
            }

            .form-section {
                padding: 20px;
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                border-radius: 10px;
                margin: 20px;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .table-section {
                padding: 20px;
                margin: 0 20px 20px 20px;
            }

            .table-responsive {
                border-radius: 10px;
                overflow-x: auto;
                overflow-y: visible;
                max-width: 100%;
                -webkit-overflow-scrolling: touch;
            }

            table.dataTable {
                border-collapse: separate !important;
                border-spacing: 0;
                background: white;
                border-radius: 10px;
                overflow: hidden;
                width: 100%;
                /* Use full width on desktop */
                font-size: 14px;
                /* Increased font size */
                white-space: nowrap;
            }

            /* Only apply minimum width on smaller screens */
            @media (max-width: 1199px) {
                table.dataTable {
                    min-width: 1000px;
                }
            }

            table.dataTable thead th {
                background: #667eea;
                color: white;
                border: none;
                padding: 15px;
                font-weight: 600;
                text-align: center;
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
                font-size: 14px;
                /* Increased font size */
            }

            table.dataTable tbody td {
                border: none;
                padding: 12px;
                vertical-align: middle;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                font-size: 13px;
                /* Increased font size */
            }

            table.dataTable tbody tr:hover {
                background: rgba(102, 126, 234, 0.05);
            }

            /* Responsive Breakpoints */
            @media (min-width: 1200px) {
                .table-responsive {
                    overflow-x: visible;
                    /* No horizontal scroll on large screens */
                }

                table.dataTable {
                    font-size: 14px;
                    min-width: auto;
                    /* Remove minimum width constraint */
                }

                table.dataTable thead th,
                table.dataTable tbody td {
                    padding: 14px;
                    /* More padding on desktop */
                    font-size: 14px;
                }
            }

            @media (max-width: 768px) {
                .main-container {
                    margin: 10px;
                    border-radius: 10px;
                    max-width: none;
                }

                .header-section {
                    padding: 15px;
                }

                .header-section h1 {
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

                .form-section,
                .table-section {
                    margin: 15px;
                    padding: 15px;
                }

                .table-responsive {
                    font-size: 0.8rem;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    border: 1px solid #dee2e6;
                    border-radius: 8px;
                }

                table.dataTable {
                    min-width: 800px;
                }

                table.dataTable thead th,
                table.dataTable tbody td {
                    padding: 8px 4px;
                }

                /* Mobile styling untuk image gallery */
                .image-gallery img {
                    width: 45px !important;
                    height: 45px !important;
                }

                .image-count-badge {
                    top: 2px !important;
                    right: 2px !important;
                    font-size: 0.55rem !important;
                    padding: 1px 4px !important;
                    min-width: 14px !important;
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

                .header-section h1 {
                    font-size: 1.1rem;
                }

                .form-section,
                .table-section {
                    margin: 10px;
                    padding: 10px;
                }

                /* Mobile positioning for floating buttons */
                #scrollToTopBtn {
                    bottom: 80px;
                    right: 15px;
                    width: 45px;
                    height: 45px;
                }

                .table-responsive {
                    border-radius: 6px;
                    margin: 0 -5px;
                }

                table.dataTable {
                    min-width: 700px;
                }
            }

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

            /* Perbesar lebar dropdown DataTables */
            .dataTables_length select {
                width: auto !important;
                min-width: 70px;
            }

            /* Scroll to top button - specific positioning */
            #scrollToTopBtn {
                display: none;
                position: fixed;
                bottom: 100px;
                /* Positioning above WhatsApp button */
                right: 20px;
                z-index: 1000;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                background: linear-gradient(135deg, #fbbf24, #f59e0b);
                border: none;
                color: white;
            }

            #scrollToTopBtn:hover {
                transform: translateY(-3px) scale(1.1);
                box-shadow: 0 12px 35px rgba(245, 158, 11, 0.4);
                background: linear-gradient(135deg, #f59e0b, #d97706);
            }

            .btn-success:hover {
                background-color: #28a745;
            }

            .btn-success i {
                color: white;
            }

            .top-left a {
                margin: 0;
                padding: 0;
            }

            .top-left button {
                margin-right: 5px;
            }

            .top-left a:focus,
            .top-left button:focus {
                outline: none;
            }

            .modal-dialog {
                max-width: 1200px;
                /* Atur lebar modal */
                margin: 30px auto;
                /* Tambahkan margin */
            }

            .modal-body iframe {
                height: 900px;
                /* Atur tinggi iframe */
            }

            /* Image gallery styling */
            .image-gallery {
                position: relative;
                display: inline-block;
            }

            .image-gallery img {
                transition: transform 0.2s ease;
                border: 2px solid transparent;
            }

            .image-gallery img:hover {
                transform: scale(1.05);
                border-color: #8b5cf6;
            }

            /* Badge styling - transparan dan kecil, positioned di dalam gambar */
            .image-count-badge {
                top: 4px;
                right: 4px;
                background: rgba(0, 0, 0, 0.7) !important;
                color: white !important;
                border-radius: 10px;
                padding: 2px 6px;
                font-size: 0.65rem;
                font-weight: 600;
                line-height: 1;
                min-width: 18px;
                text-align: center;
                backdrop-filter: blur(4px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                z-index: 2;
            }

            /* Hover effect untuk badge */
            .image-gallery:hover .image-count-badge {
                background: rgba(139, 92, 246, 0.85) !important;
                transform: scale(1.05);
            }

            /* Lebarkan kolom Status dan Priority */
            table#projectTable th:nth-child(2),
            /* Kolom Status */
            table#projectTable td:nth-child(2),
            table#projectTable th:nth-child(3),
            /* Kolom Priority */
            table#projectTable td:nth-child(3) {
                width: 120px;
                /* Atur lebar kolom */
            }

            table#projectTable select {
                width: 100%;
                /* Pastikan select form memenuhi lebar kolom */
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
                    <a href="costume_index.php" class="floating-btn" target="_blank">
                        <i class="bi bi-eye"></i>
                        View Projects
                    </a>
                </div>
                <h1><i class="bi bi-palette me-2"></i>Upload Project Costume</h1>
            </div>

            <div class="form-section">
                <form id="uploadForm" action="costume_upload.php" method="POST" enctype="multipart/form-data"
                    novalidate>
                    <div class="form-group">
                        <label for="project_name">Project Name:</label>
                        <input type="text" class="form-control" id="project_name" name="project_name" required>
                        <div class="error" id="project_name_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="subform_embed">Submission Form Embed Link:</label>
                        <textarea type="text" class="form-control" id="subform_embed" name="subform_embed"
                            placeholder="Link Example: https://docs.google.com/presentation/d/e/2PACX-.../edit"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_status" class="form-label">Project Status</label>
                                <select class="form-select" name="project_status" id="project_status"
                                    aria-label="Default select example" required>
                                    <option selected>Select Status</option>
                                    <option value="Upcoming">Upcoming</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Revision">Revision</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Archived">Archived</option>
                                </select>
                                <div class="error" id="project_status_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" name="priority" id="priority" required>
                                    <option selected>Select Priority</option>
                                    <option value="Urgent">Urgent</option>
                                    <option value="High">High</option>
                                    <option value="Normal">Normal</option>
                                    <option value="Low">Low</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity">Quantity:</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1"
                                    required>
                                <div class="error" id="quantity_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="deadline">Deadline:</label>
                                <input type="date" class="form-control" id="deadline" name="deadline">
                                <div class="error" id="deadline_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        <div class="error" id="description_error"></div>
                    </div>

                    <div class="form-group">
                        <label>Project Image:</label>
                        <div class="drop-zone" onclick="document.getElementById('project_image').click();">
                            Click or Drag to Upload Project Image
                        </div>
                        <input type="file" id="project_image" name="project_image" accept="image/*"
                            style="display:none;" required>
                        <img id="project_image_preview" src="#" alt="Project Image Preview"
                            style="display:none; margin-top:10px; max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                        <div class="error" id="project_image_error"></div>
                    </div>
                    <div class="form-group">
                        <label>Submission Notes:</label>
                        <div class="drop-zone" onclick="document.getElementById('material_image').click();">
                            Click or Drag to Upload Submission Notes
                        </div>
                        <input type="file" id="material_image" name="material_image" accept="image/*"
                            style="display:none;" required>
                        <img id="material_image_preview" src="#" alt="Submission Notes Preview"
                            style="display:none; margin-top:10px; max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                        <div class="error" id="material_image_error"></div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Upload</button>
                </form>
            </div>

            <div class="table-section">
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <h4 class="mb-3"><i class="bi bi-list-ul me-2"></i>Daftar Project</h4>
                <div class="table-responsive">
                    <table id="projectTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Project Name</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Quantity</th>
                                <th>Project Image</th>
                                <th>Submission Notes</th>
                                <th>Sub Form</th>
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
                                    <select class="form-select"
                                        onchange="updateStatus(<?= $row['id'] ?>, this.value, 'costume')">
                                        <option value="Upcoming"
                                            <?= $row['project_status'] === 'Upcoming' ? 'selected' : '' ?>>
                                            Upcoming
                                        </option>
                                        <option value="In Progress"
                                            <?= $row['project_status'] === 'In Progress' ? 'selected' : '' ?>>In
                                            Progress
                                        </option>
                                        <option value="Revision"
                                            <?= $row['project_status'] === 'Revision' ? 'selected' : '' ?>>
                                            Revision</option>
                                        <option value="Completed"
                                            <?= $row['project_status'] === 'Completed' ? 'selected' : '' ?>>
                                            Completed</option>
                                        <option value="Archived"
                                            <?= $row['project_status'] === 'Archived' ? 'selected' : '' ?>>
                                            Archived</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select"
                                        onchange="updatePriority(<?= $row['id'] ?>, this.value)">
                                        <option value="Urgent"
                                            <?= $row['priority'] === 'Urgent' ? 'selected' : '' ?>>Urgent
                                        </option>
                                        <option value="High"
                                            <?= $row['priority'] === 'High' ? 'selected' : '' ?>>High
                                        </option>
                                        <option value="Normal"
                                            <?= $row['priority'] === 'Normal' ? 'selected' : '' ?>>Normal
                                        </option>
                                        <option value="Low"
                                            <?= $row['priority'] === 'Low' ? 'selected' : '' ?>>Low
                                        </option>
                                    </select>
                                </td>
                                <td><?= htmlspecialchars($row['quantity']) ?></td>
                                <td><img src="uploads/projects/<?= $row['project_image'] ?>" width="100"></td>
                                <td><img src="uploads/materials/<?= $row['material_image'] ?>" width="100"></td>
                                <td>
                                    <?php if (!empty($row['subform_embed'])): ?>
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="openGoogleSlideModal('<?= htmlspecialchars($row['subform_embed']) ?>')">
                                        View
                                    </button>
                                    <?php else: ?>
                                    <span class="text-muted">No Link</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                                <td>
                                    <?= !empty($row['deadline']) ? htmlspecialchars(Carbon::parse($row['deadline'])->format('d M Y')) : '-' ?>
                                </td>
                                <td>
                                    <a href="costume_edit.php?id=<?= $row['id'] ?>"
                                        class="btn btn-warning btn-sm mb-1">Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm"
                                        onclick="confirmDelete(event, <?= $row['id'] ?>)">Delete</a>

                                    <script>
                                        function confirmDelete(event, id) {
                                            event.preventDefault();

                                            Swal.fire({
                                                title: 'Are you sure?',
                                                text: "You won't be able to revert this!",
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#d33',
                                                cancelButtonColor: '#3085d6',
                                                confirmButtonText: 'Yes, delete it!'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = `costume_delete.php?id=${id}`;
                                                }
                                            });
                                        }
                                    </script>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Scroll to Top Button -->
        <button id="scrollToTopBtn" style="display: none;">
            <i class="bi bi-arrow-up" style="color: white; font-size: 18px;"></i>
        </button>

        <!-- Floating Buttons -->
        <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
            <!-- Helpdesk Button -->
            <a href="https://wa.me/6287721988393" target="_blank" class="btn btn-success"
                style="border-radius: 50%; width: 50px; height: 50px; display: flex; justify-content: center; align-items: center; text-decoration: none; font-size: 12px; font-weight: bold; background: linear-gradient(135deg, #10b981, #059669);">
                Help!
            </a>
        </div>
        </a>
        </div>

        <!-- Modal for Google Slide -->
        <div class="modal fade" id="googleSlideModal" tabindex="-1" aria-labelledby="googleSlideModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <!-- Gunakan modal-xl untuk ukuran ekstra besar -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="googleSlideModalLabel">Google Slide</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <iframe id="googleSlideIframe" class="w-100" style="height: 600px;" frameborder="0"
                            allowfullscreen></iframe>
                        <p id="fallbackLink" style="display: none; text-align: center; margin-top: 20px;">
                            Your browser does not support embedded content.
                            <a href="#" id="googleSlideLink" target="_blank">Click here to view the slides</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <footer class="text-secondary text-center py-1 mt-4" style="background-color: rgba(0, 0, 0, 0.05);">
            <div class="mb-0">Create with ❤️ by <a class="text-primary fw-bold" href=""
                    style="text-decoration: none;">IT
                    DCM</a></div>
        </footer>

        <!-- jQuery (Required for DataTables) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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

            function openGoogleSlideModal(embedLink) {
                const embedUrl = embedLink.replace('/edit', '/embed'); // Ubah URL menjadi embed
                const iframe = document.getElementById('googleSlideIframe');
                const fallbackLink = document.getElementById('fallbackLink');
                const googleSlideLink = document.getElementById('googleSlideLink');

                iframe.src = embedUrl;
                googleSlideLink.href = embedLink;

                // Cek apakah iframe didukung
                iframe.onload = function() {
                    fallbackLink.style.display = 'none';
                };
                iframe.onerror = function() {
                    fallbackLink.style.display = 'block';
                    iframe.style.display = 'none';
                };

                const modal = new bootstrap.Modal(document.getElementById('googleSlideModal'));
                modal.show();
            }

            // Event listener untuk Project Image
            document.getElementById('project_image').addEventListener('change', function() {
                previewImage(this, 'project_image_preview');
            });

            // Event listener untuk Submission Notes
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
                if (!projectStatus.value || projectStatus.value === "Select Status") {
                    isValid = false;
                    document.getElementById('project_status_error').textContent = "Project Status is required.";
                } else {
                    document.getElementById('project_status_error').textContent = "";
                }

                // Validasi Project Priority
                const priority = document.getElementById('priority');
                if (!priority.value || priority.value === "Select Priority") {
                    isValid = false;
                    document.getElementById('priority_error').textContent = "Priority Status is required.";
                } else {
                    document.getElementById('priority_error').textContent = "";
                }

                // Validasi Quantity
                const quantity = document.getElementById('quantity');
                if (!quantity.value || isNaN(quantity.value) || parseInt(quantity.value) <= 0) {
                    isValid = false;
                    document.getElementById('quantity_error').textContent = "Quantity must be a positive number.";
                } else {
                    document.getElementById('quantity_error').textContent = "";
                }

                // Validasi Deadline
                const deadline = document.getElementById('deadline');
                if (!deadline.value) {
                    isValid = false;
                    document.getElementById('deadline_error').textContent = "Deadline is required.";
                } else {
                    document.getElementById('deadline_error').textContent = "";
                }

                // Jika ada error, cegah pengiriman form
                if (!isValid) {
                    e.preventDefault();
                    alert("Please fill out all required fields.");
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
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Status updated successfully!',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update status.',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred.',
                            confirmButtonText: 'OK'
                        });
                        console.error('Error:', error);
                    });
            }

            // Hapus alert setelah 5 detik
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                    setTimeout(() => {
                        alert.remove(); // Hapus elemen alert dari DOM
                    }, 150); // Tunggu animasi selesai (150ms)
                }
            }, 5000);

            $(document).ready(function() {
                $('#projectTable').DataTable({
                    paging: true, // Aktifkan pagination
                    searching: true, // Aktifkan pencarian
                    ordering: true, // Aktifkan pengurutan
                    info: true, // Tampilkan informasi jumlah data
                    lengthChange: true, // Pilihan jumlah data per halaman
                    pageLength: 25, // Default jumlah data per halaman
                    order: [
                        [9, 'desc']
                    ], // Urutkan berdasarkan kolom ke-9 (createAt) secara descending
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Berikutnya",
                            previous: "Sebelumnya",
                        },
                    },
                });
            });

            function updatePriority(id, priority) {
                fetch('update_priority.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: id,
                            priority: priority
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Priority updated successfully!',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update priority.',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred.',
                            confirmButtonText: 'OK'
                        });
                        console.error('Error:', error);
                    });
            }

            // Ambil elemen tombol
            const scrollToTopBtn = document.getElementById('scrollToTopBtn');

            // Tampilkan tombol saat pengguna menggulir ke bawah
            window.addEventListener('scroll', () => {
                if (window.scrollY > 200) { // Tampilkan tombol jika scroll lebih dari 200px
                    scrollToTopBtn.style.display = 'block';
                } else {
                    scrollToTopBtn.style.display = 'none';
                }
            });

            // Fungsi untuk menggulir ke atas
            scrollToTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth' // Gulir dengan animasi halus
                });
            });
        </script>
        <?php if (isset($_SESSION['message'])): ?>
        <script>
            Swal.fire({
                icon: '<?= $_SESSION['message_type'] === 'success' ? 'success' : 'error' ?>',
                title: '<?= $_SESSION['message_type'] === 'success' ? 'Success' : 'Error' ?>',
                text: '<?= $_SESSION['message'] ?>',
                confirmButtonText: 'OK'
            });
        </script>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>
    </body>

</html>
