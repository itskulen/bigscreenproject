<?php
session_start();
include 'db.php';
include 'middleware.php';
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
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

    /* Perbesar lebar dropdown DataTables */
    .dataTables_length select {
        width: auto !important;
        min-width: 70px;
    }

    #scrollToTopBtn {
        display: none;
        /* Tombol disembunyikan secara default */
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    #scrollToTopBtn:hover {
        background-color: #0056b3;
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
                <label for="subform_embed">Submission Form Embed Link:</label>
                <textarea type="text" class="form-control" id="subform_embed" name="subform_embed"
                    placeholder="Link Example: https://docs.google.com/presentation/d/e/2PACX-.../edit"></textarea>
            </div>

            <div class="form-group">
                <label for="project_status" class="form-label">Project Status</label>
                <select class="form-select col-6" name="project_status" id="project_status"
                    aria-label="Default select example" required>
                    <option selected>Select Status</option>
                    <option value="Upcoming">Upcoming</option>
                    <option value="Urgent">Urgent</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Revision">Revision</option>
                    <option value="Completed">Completed</option>
                    <option value="Archived">Archived</option>
                </select>
                <div class="error" id="project_status_error"></div>
            </div>

            <div class="form-group">
                <label for="priority" class="form-label">Priority</label>
                <select class="form-select col-6" name="priority" id="priority" required>
                    <option selected>Select Priority</option>
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                </select>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control col-6" id="quantity" name="quantity" min="1" required>
                <div class="error" id="quantity_error"></div>
            </div>

            <div class="form-group">
                <label for="deadline">Deadline:</label>
                <input type="date" class="form-control w-auto" id="deadline" name="deadline" required>
                <div class="error" id="deadline_error"></div>
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
                <input type="file" id="project_image" name="project_image" accept="image/*" style="display:none;"
                    required>
                <img id="project_image_preview" src="#" alt="Project Image Preview"
                    style="display:none; margin-top:10px; max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                <div class="error" id="project_image_error"></div>
            </div>
            <div class="form-group">
                <label>Submission Notes:</label>
                <div class="drop-zone" onclick="document.getElementById('material_image').click();">
                    Click or Drag to Upload Submission Notes
                </div>
                <input type="file" id="material_image" name="material_image" accept="image/*" style="display:none;"
                    required>
                <img id="material_image_preview" src="#" alt="Submission Notes Preview"
                    style="display:none; margin-top:10px; max-width: 200px; border: 1px solid #ddd; padding: 5px;">
                <div class="error" id="material_image_error"></div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Upload</button>
        </form>
    </div>
    <hr id="alertMessage">
    <div class="container mt-3">
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>
        <h2 class="mt-2">Daftar Project</h2>
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
                        <select class="form-select" onchange="updateStatus(<?= $row['id'] ?>, this.value, 'costume')">
                            <option value="Upcoming" <?= $row['project_status'] === 'Upcoming' ? 'selected' : '' ?>>
                                Upcoming
                            </option>
                            <option value="Urgent" <?= $row['project_status'] === 'Urgent' ? 'selected' : '' ?>>Urgent
                            </option>
                            <option value="In Progress"
                                <?= $row['project_status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Revision" <?= $row['project_status'] === 'Revision' ? 'selected' : '' ?>>
                                Revision</option>
                            <option value="Completed" <?= $row['project_status'] === 'Completed' ? 'selected' : '' ?>>
                                Completed</option>
                            <option value="Archived" <?= $row['project_status'] === 'Archived' ? 'selected' : '' ?>>
                                Archived</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select" onchange="updatePriority(<?= $row['id'] ?>, this.value)">
                            <option value="High" <?= $row['priority'] === 'High' ? 'selected' : '' ?>>High</option>
                            <option value="Medium" <?= $row['priority'] === 'Medium' ? 'selected' : '' ?>>Medium
                            </option>
                            <option value="Low" <?= $row['priority'] === 'Low' ? 'selected' : '' ?>>Low</option>
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
                        <?= !empty($row['deadline'])
                                ? htmlspecialchars(Carbon::parse($row['deadline'])->format('d M Y'))
                                : '-' ?>
                    </td>
                    <td>
                        <a href="costume_edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm mb-1">Edit</a>
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

    <!-- Floating Buttons -->
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; display: flex; gap: 10px;">
        <!-- Scroll to Top Button -->
        <button id="scrollToTopBtn" class="btn btn-warning"
            style="display: none; border-radius: 50%; width: 50px; height: 50px;">
            <i class="bi bi-arrow-up" style="color: #198754;"></i>
        </button>

        <!-- Helpdesk Button -->
        <a href="https://wa.me/6287721988393" target="_blank" class="btn btn-success"
            style="border-radius: 50%; width: 50px; height: 50px; display: flex; justify-content: center; align-items: center;">
            <i style="font-size: 16px;">Help!</i>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <div class="mb-0">Create with ❤️ by <a class="text-primary fw-bold" href="" style="text-decoration: none;">IT
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