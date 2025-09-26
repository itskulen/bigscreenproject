<?php
session_start();
include 'db.php';
include 'middleware.php';
include 'image_helper.php';
checkUserRole('mascot'); // Hanya mascot admin yang bisa mengakses

$sql = "SELECT project_name, project_status, priority, quantity, project_image, material_image, description, deadline,
createAt, updateAt
FROM gallery WHERE category = 'mascot' ORDER BY createAt DESC";
$result = $pdo->query($sql);
?>
<!DOCTYPE html>
<html>

    <head>
        <title>Admin Mascot - Project</title>
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
        <!-- Fancybox CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                margin: 0;
                padding: 0;
            }

            .main-container {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border-radius: 15px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
                margin: 15px;
                padding: 20px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                width: calc(100% - 30px);
                max-width: none;
            }

            .header-section {
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                color: white;
                padding: 15px 20px;
                border-radius: 12px;
                margin-bottom: 20px;
                box-shadow: 0 0px 10px rgba(139, 92, 246, 0.3);
            }

            .header-section h2 {
                margin: 0;
                font-weight: 600;
                font-size: 1.5rem;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            }

            .top-buttons {
                display: flex;
                gap: 8px;
                align-items: center;
            }

            .btn-modern {
                border-radius: 20px;
                font-weight: 500;
                transition: all 0.3s ease;
                box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
                border: none;
                padding: 8px 16px;
                font-size: 14px;
            }

            .btn-modern:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            }

            .btn-modern.btn-danger {
                background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            }

            .btn-modern.btn-success {
                background: linear-gradient(135deg, #51cf66, #40c057);
            }

            .form-section {
                background: rgba(255, 255, 255, 0.9);
                padding: 20px;
                border-radius: 12px;
                margin-bottom: 20px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(139, 92, 246, 0.1);
            }

            .form-group {
                margin-bottom: 18px;
            }

            .form-control,
            .form-select {
                border-radius: 10px;
                border: 2px solid #e9ecef;
                transition: all 0.3s ease;
                background: rgba(255, 255, 255, 0.9);
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #8b5cf6;
                box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
                background: white;
            }

            .form-label {
                font-weight: 600;
                color: #4a5568;
                margin-bottom: 4px;
                display: block;
            }

            .drop-zone {
                border: 2px dashed #8b5cf6;
                padding: 10px;
                text-align: center;
                background: linear-gradient(135deg, rgba(139, 92, 246, 0.05), rgba(124, 58, 237, 0.05));
                margin-bottom: 6px;
                cursor: pointer;
                border-radius: 12px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .drop-zone::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.1), transparent);
                transition: left 0.5s ease;
            }

            .drop-zone:hover::before {
                left: 100%;
            }

            .drop-zone:hover {
                background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(124, 58, 237, 0.1));
                border-color: #7c3aed;
            }

            .drop-zone i {
                font-size: 2rem;
                color: #8b5cf6;
                margin-bottom: 2px;
                display: block;
            }

            .drop-zone-text {
                color: #6b7280;
                font-weight: 500;
            }

            .error {
                color: #ef4444;
                font-size: 0.875rem;
                margin-top: 5px;
                font-weight: 500;
            }

            .btn-upload {
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                border: none;
                color: white;
                padding: 12px 30px;
                border-radius: 20px;
                font-weight: 600;
                font-size: 15px;
                transition: all 0.3s ease;
                box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
                width: 100%;
            }

            .btn-upload:hover {
                transform: translateY(-1px);
                color: whitesmoke;
                box-shadow: 0 12px 35px rgba(139, 92, 246, 0.4);
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            .table-section {
                background: rgba(255, 255, 255, 0.9);
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(139, 92, 246, 0.1);
            }

            .table-section h2 {
                color: #4a5568;
                font-weight: 600;
                font-size: 1.2rem;
                margin-bottom: 15px;
                padding-bottom: 8px;
                border-bottom: 3px solid #8b5cf6;
                display: inline-block;
            }

            .container {
                margin-top: 0;
            }

            /* DataTable Styling */
            .dataTables_wrapper {
                padding: 15px 0;
            }

            .dataTables_filter input {
                border-radius: 20px !important;
                border: 2px solid #e9ecef !important;
                padding: 6px 14px !important;
                margin-left: 8px !important;
                font-size: 14px !important;
            }

            .dataTables_filter input:focus {
                border-color: #8b5cf6 !important;
                box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1) !important;
            }

            .dataTables_length select {
                border-radius: 6px !important;
                border: 2px solid #e9ecef !important;
                padding: 4px 8px !important;
                font-size: 14px !important;
            }

            .table-responsive {
                border-radius: 10px;
                overflow-x: auto;
                overflow-y: visible;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
                max-width: 100%;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                margin-bottom: 0;
                font-size: 14px;
                /* Increased font size for better readability */
                width: 100%;
                /* Use full width on desktop */
                white-space: nowrap;
            }

            /* Only apply minimum width on smaller screens */
            @media (max-width: 1199px) {
                .table {
                    min-width: 1000px;
                }
            }

            .table thead th {
                background: #8b5cf6;
                color: white;
                font-weight: 600;
                border: none;
                padding: 12px 8px;
                text-align: center;
                font-size: 14px;
                /* Increased font size */
                white-space: nowrap;
            }

            .table tbody td {
                padding: 12px 8px;
                /* Increased padding */
                vertical-align: middle;
                border-bottom: 1px solid #f1f5f9;
                font-size: 16px;
                /* Increased font size */
            }

            .table tbody tr:hover {
                background-color: rgba(139, 92, 246, 0.05);
                transition: all 0.2s ease;
            }

            .table .form-select {
                border: 1px solid #d1d5db;
                border-radius: 6px;
                padding: 4px 8px;
                font-size: 14px;
                min-width: 100px;
            }

            .table th,
            .table td {
                padding: 8px 4px;
                font-size: 14px;
            }

            .table th:nth-child(1),
            /* Project Name */
            .table td:nth-child(1) {
                max-width: 120px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .table th:nth-child(2),
            /* Status */
            .table td:nth-child(2),
            .table th:nth-child(3),
            /* Priority */
            .table td:nth-child(3),
            .table th:nth-child(4),
            /* Quantity */
            .table td:nth-child(4) {
                max-width: 80px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .table td.truncate-description {
                max-width: 180px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .btn-sm {
                border-radius: 12px;
                font-weight: 500;
                padding: 4px 10px;
                font-size: 11px;
                transition: all 0.2s ease;
            }

            .btn-sm:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            }

            /* Floating buttons styling */
            .floating-buttons {
                position: fixed;
                bottom: 30px;
                right: 30px;
                z-index: 1000;
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .floating-btn {
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                justify-content: center;
                align-items: center;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                text-decoration: none;
                font-size: 12px;
                font-weight: bold;
                color: white;
                background: linear-gradient(135deg, #10b981, #059669);
            }

            .floating-btn:hover {
                transform: translateY(-3px) scale(1.1);
                box-shadow: 0 12px 35px rgba(16, 185, 129, 0.4);
                color: white;
                text-decoration: none;
            }

            /* Scroll to project list button */
            #scrollToProjectBtn {
                display: none;
                position: fixed;
                bottom: 160px;
                /* Position above scroll to top button */
                right: 30px;
                z-index: 1000;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                border: none;
                color: white;
                cursor: pointer;
            }

            #scrollToProjectBtn:hover {
                transform: translateY(-3px) scale(1.1);
                box-shadow: 0 12px 35px rgba(139, 92, 246, 0.4);
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
            }

            /* Scroll to top button - specific positioning */
            #scrollToTopBtn {
                display: none;
                position: fixed;
                bottom: 100px;
                /* Positioning above WhatsApp button */
                right: 30px;
                z-index: 1000;
                border-radius: 50%;
                width: 40px;
                height: 40px;
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

            /* Loading state for select elements */
            .form-select:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                background-color: #f8f9fa !important;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") !important;
            }

            /* Preview container styling */
            #project_image_preview,
            #material_image_preview {
                max-height: 250px;
                overflow-y: auto;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 8px;
            }

            .table {
                font-size: 14px;
                min-width: auto;
                /* Remove minimum width constraint */
            }

            .table thead th,
            .table tbody td {
                padding: 14px 10px;
                /* More padding on desktop */
                font-size: 16px;
            }
            }

            @media (max-width: 1200px) {
                .main-container {
                    margin: 10px;
                    padding: 15px;
                }
            }

            @media (max-width: 768px) {
                .header-section {
                    padding: 12px 15px;
                }

                .header-section h2 {
                    font-size: 1.3rem;
                }

                .header-section .d-flex {
                    flex-direction: column;
                    gap: 10px;
                }

                .top-buttons {
                    justify-content: center;
                    width: 100%;
                }

                .btn-modern {
                    font-size: 13px;
                    padding: 6px 12px;
                }

                .form-section,
                .table-section {
                    padding: 15px;
                }

                .form-group {
                    margin-bottom: 15px;
                }

                .drop-zone {
                    padding: 20px;
                }

                .drop-zone i {
                    font-size: 1.5rem;
                }

                .table thead th,
                .table tbody td {
                    padding: 8px 4px;
                }

                .table {
                    min-width: 800px;
                    /* Smaller minimum width for mobile */
                }

                .table-responsive {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    border: 1px solid #dee2e6;
                    border-radius: 8px;
                }

                .table .form-select {
                    font-size: 11px;
                    padding: 3px 6px;
                    min-width: 80px;
                }

                .btn-sm {
                    font-size: 10px;
                    padding: 3px 8px;
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

                .dataTables_filter input {
                    padding: 5px 10px !important;
                    font-size: 13px !important;
                }

                /* Mobile positioning for floating buttons */
                .floating-buttons {
                    bottom: 20px;
                    right: 15px;
                }

                .floating-btn {
                    width: 40px;
                    height: 40px;
                    font-size: 10px;
                }

                #scrollToTopBtn {
                    bottom: 80px;
                    right: 15px;
                    width: 45px;
                    height: 45px;
                }

                #scrollToProjectBtn {
                    bottom: 130px;
                    right: 15px;
                    width: 45px;
                    height: 45px;
                }
            }

            @media (max-width: 576px) {
                .main-container {
                    margin: 5px;
                    padding: 10px;
                    border-radius: 10px;
                }

                .header-section {
                    padding: 10px;
                    border-radius: 8px;
                }

                .header-section h2 {
                    font-size: 1.2rem;
                }

                .table-section h2 {
                    font-size: 1.2rem;
                }

                .form-section,
                .table-section {
                    padding: 12px;
                    border-radius: 8px;
                }

                .table {
                    font-size: 10px;
                    min-width: 700px;
                    /* Even smaller for very small screens */
                }

                .table thead th,
                .table tbody td {
                    padding: 6px 3px;
                }

                .table-responsive {
                    border-radius: 6px;
                    margin: 0 -5px;
                    /* Negative margin to utilize full width */
                }

                .btn-upload {
                    padding: 10px 20px;
                    font-size: 14px;
                }
            }
        </style>
    </head>

    <body>
        <div class="main-container">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="bi bi-gear-fill me-2"></i>Admin Mascot Project</h2>
                    <div class="top-buttons">
                        <a href="logout.php">
                            <button type="button" class="btn btn-modern btn-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </a>
                        <a href="mascot_index.php" target="_blank">
                            <button type="button" class="btn btn-modern btn-success">
                                <i class="bi bi-eye me-2"></i>View Projects
                            </button>
                        </a>
                    </div>
                </div>
            </div>
            <?php
            if (isset($_SESSION['message']) && $_SESSION['message_type'] === 'danger'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); endif; ?>
            <div class="form-section">
                <h5 class="mb-3"><i class="bi bi-plus-circle me-2"></i>Upload New Project</h5>
                <form id="uploadForm" action="mascot_upload.php" method="POST" enctype="multipart/form-data"
                    novalidate>
                    <?php
                    $old = $_SESSION['old'] ?? [];
                    ?>
                    <div class="form-group">
                        <label for="project_name" class="form-label">Project Name</label>
                        <input type="text" class="form-control" id="project_name" name="project_name" required
                            value="<?= htmlspecialchars($old['project_name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="subform_embed" class="form-label">Submission Form Embed Link</label>
                        <textarea type="text" class="form-control" id="subform_embed" name="subform_embed"
                            placeholder="Link Example: https://docs.google.com/presentation/d/e/2PACX-.../edit"><?= htmlspecialchars($old['subform_embed'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="type" class="form-label">Mascot Category</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Select Category</option>
                            <option value="compressed foam"
                                <?= isset($old['type']) && $old['type'] == 'compressed foam' ? 'selected' : '' ?>>
                                Compressed Foam</option>
                            <option value="inflatable"
                                <?= isset($old['type']) && $old['type'] == 'inflatable' ? 'selected' : '' ?>>
                                Inflatable</option>
                            <option value="statue"
                                <?= isset($old['type']) && $old['type'] == 'statue' ? 'selected' : '' ?>>
                                Statue</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_status" class="form-label">Project Status</label>
                                <select class="form-select" name="project_status" id="project_status"
                                    aria-label="Default select example" required>
                                    <option value="">Select Status</option>
                                    <option value="Upcoming"
                                        <?= isset($old['project_status']) && $old['project_status'] == 'Upcoming' ? 'selected' : '' ?>>
                                        Upcoming</option>
                                    <option value="In Progress"
                                        <?= isset($old['project_status']) && $old['project_status'] == 'In Progress' ? 'selected' : '' ?>>
                                        In Progress</option>
                                    <option value="Revision"
                                        <?= isset($old['project_status']) && $old['project_status'] == 'Revision' ? 'selected' : '' ?>>
                                        Revision</option>
                                    <option value="Completed"
                                        <?= isset($old['project_status']) && $old['project_status'] == 'Completed' ? 'selected' : '' ?>>
                                        Completed</option>
                                    <option value="Archived"
                                        <?= isset($old['project_status']) && $old['project_status'] == 'Archived' ? 'selected' : '' ?>>
                                        Archived</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" name="priority" id="priority" required>
                                    <option value="">Select Priority</option>
                                    <option value="Urgent"
                                        <?= isset($old['priority']) && $old['priority'] == 'Urgent' ? 'selected' : '' ?>>
                                        Urgent</option>
                                    <option value="High"
                                        <?= isset($old['priority']) && $old['priority'] == 'High' ? 'selected' : '' ?>>
                                        High</option>
                                    <option value="Normal"
                                        <?= isset($old['priority']) && $old['priority'] == 'Normal' ? 'selected' : '' ?>>
                                        Normal</option>
                                    <option value="Low"
                                        <?= isset($old['priority']) && $old['priority'] == 'Low' ? 'selected' : '' ?>>
                                        Low</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity"
                                    min="1" required value="<?= htmlspecialchars($old['quantity'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="deadline" class="form-label">Deadline</label>
                                <input type="date" class="form-control" id="deadline" name="deadline"
                                    value="<?= htmlspecialchars($old['deadline'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    </div>
                    <?php if (isset($_SESSION['old'])) {
                        unset($_SESSION['old']);
                    } ?>

                    <div class="form-group">
                        <label for="project_image" class="form-label">Project Images:</label>
                        <div class="drop-zone" onclick="document.getElementById('project_image').click();">
                            <i class="bi bi-cloud-upload"></i>
                            <div class="drop-zone-text">Click or Drag to Upload Project Images<br><small>Multiple files
                                    allowed</small></div>
                        </div>
                        <input type="file" id="project_image" name="project_image[]" accept="image/*"
                            style="display:none;" multiple required>
                        <div id="project_image_preview" class="mt-2"></div>
                        <div class="error" id="project_image_error"></div>
                    </div>
                    <div class="form-group">
                        <label for="material_image" class="form-label">Submission Notes:</label>
                        <div class="drop-zone" onclick="document.getElementById('material_image').click();">
                            <i class="bi bi-file-earmark-image"></i>
                            <div class="drop-zone-text">Click or Drag to Upload Submission Notes<br><small>Multiple
                                    files allowed</small></div>
                        </div>
                        <input type="file" id="material_image" name="material_image[]" accept="image/*"
                            style="display:none;" multiple required>
                        <div id="material_image_preview" class="mt-2"></div>
                        <div class="error" id="material_image_error"></div>
                    </div>

                    <button type="submit" class="btn btn-upload">
                        <i class="bi bi-upload me-2"></i>Upload Project
                    </button>
                </form>
            </div>
            <?php if (isset($_SESSION['message']) && $_SESSION['message_type'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show" id="alertSuccessMessage" role="alert">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); endif; ?>
            <div class="table-section">
                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>
                <h2><i class="bi bi-table me-2"></i>Project List</h2>
                <div class="table-responsive">
                    <table id="projectTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Project Name</th>
                                <th>Category</th>
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
                            $stmt = $pdo->query("SELECT * FROM gallery WHERE category = 'mascot' ORDER BY id DESC");
                            while ($row = $stmt->fetch()):
                            ?>
                            <tr>
                                <td title="<?= htmlspecialchars($row['project_name']) ?>"><?= htmlspecialchars($row['project_name']) ?></td>
                                <td title="<?= htmlspecialchars($row['type'] ?? '-') ?>"><?= htmlspecialchars($row['type'] ?? '-') ?></td>
                                <td>
                                    <select class="form-select"
                                        onchange="updateStatus(<?= $row['id'] ?>, this.value, 'mascot')">
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
                                            <?= $row['priority'] === 'Urgent' ? 'selected' : '' ?>>
                                            Urgent
                                        </option>
                                        <option value="High"
                                            <?= $row['priority'] === 'High' ? 'selected' : '' ?>>
                                            High
                                        </option>
                                        <option value="Normal"
                                            <?= $row['priority'] === 'Normal' ? 'selected' : '' ?>>
                                            Normal
                                        </option>
                                        <option value="Low"
                                            <?= $row['priority'] === 'Low' ? 'selected' : '' ?>>
                                            Low
                                        </option>
                                    </select>
                                </td>
                                <td><?= htmlspecialchars($row['quantity']) ?></td>
                                <td class="text-center">
                                    <?= generateImageGallery($row['project_image'], 'projects', 'project-' . $row['id'], $row['project_name'], 'Project') ?>
                                </td>
                                <td class="text-center">
                                    <?= generateImageGallery($row['material_image'], 'materials', 'material-' . $row['id'], $row['project_name'], 'Material') ?>
                                </td>
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
                                <td class="truncate-description" title="<?= htmlspecialchars($row['description']) ?>">
                                    <?= htmlspecialchars($row['description']) ?>
                                </td>
                                <td><?= !empty($row['deadline']) ? htmlspecialchars($row['deadline']) : '-' ?></td>
                                <td>
                                    <a href="mascot_edit.php?id=<?= $row['id'] ?>"
                                        class="btn btn-warning btn-sm">Edit</a>
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
                                                    window.location.href = `mascot_delete.php?id=${id}`;
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

        <!-- Scroll to Project List Button -->
        <button id="scrollToProjectBtn" style="display: none;" title="Go to Project List">
            <i class="bi bi-list-ul" style="color: white; font-size: 18px;"></i>
        </button>

        <!-- Scroll to Top Button -->
        <button id="scrollToTopBtn" style="display: none;">
            <i class="bi bi-arrow-up" style="color: white; font-size: 18px;"></i>
        </button>

        <!-- Floating Buttons -->
        <div class="floating-buttons">
            <!-- Helpdesk Button -->
            <a href="https://wa.me/6287721988393" target="_blank" class="floating-btn">
                Help!
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
        <!-- Fancybox JS -->
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
        <script>
            // Fungsi untuk menampilkan preview gambar multiple
            function previewMultipleImages(input, previewContainerId) {
                const previewContainer = document.getElementById(previewContainerId);
                previewContainer.innerHTML = ''; // Clear previous previews

                if (input.files && input.files.length > 0) {
                    for (let i = 0; i < input.files.length; i++) {
                        const file = input.files[i];
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const imgWrapper = document.createElement('div');
                            imgWrapper.className = 'd-inline-block position-relative me-2 mb-2';

                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.maxWidth = '120px';
                            img.style.maxHeight = '120px';
                            img.style.border = '1px solid #ddd';
                            img.style.padding = '5px';
                            img.style.borderRadius = '5px';

                            const badge = document.createElement('span');
                            badge.className = 'image-count-badge position-absolute';
                            badge.style.top = '4px';
                            badge.style.right = '4px';
                            badge.style.background = 'rgba(0, 0, 0, 0.7)';
                            badge.style.color = 'white';
                            badge.style.borderRadius = '10px';
                            badge.style.padding = '2px 6px';
                            badge.style.fontSize = '0.65rem';
                            badge.style.fontWeight = '600';
                            badge.style.lineHeight = '1';
                            badge.style.minWidth = '18px';
                            badge.style.textAlign = 'center';
                            badge.style.backdropFilter = 'blur(4px)';
                            badge.style.border = '1px solid rgba(255, 255, 255, 0.2)';
                            badge.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.2)';
                            badge.style.zIndex = '2';
                            badge.textContent = i + 1;

                            imgWrapper.appendChild(img);
                            imgWrapper.appendChild(badge);
                            previewContainer.appendChild(imgWrapper);
                        };

                        reader.readAsDataURL(file);
                    }
                }
            }

            // Fungsi untuk menampilkan preview gambar (backward compatibility)
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
                previewMultipleImages(this, 'project_image_preview');
            });

            // Event listener untuk Submission Notes
            document.getElementById('material_image').addEventListener('change', function() {
                previewMultipleImages(this, 'material_image_preview');
            });

            function updateStatus(id, status, category) {
                // Show loading state
                const selectElement = event.target;
                const originalValue = selectElement.getAttribute('data-original-value') || selectElement.value;
                selectElement.disabled = true;

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
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        selectElement.disabled = false;

                        if (data.success) {
                            // Store new value as original
                            selectElement.setAttribute('data-original-value', status);

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message || 'Status updated successfully!',
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        } else {
                            // Revert to original value on error
                            selectElement.value = originalValue;

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to update status.',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        selectElement.disabled = false;
                        selectElement.value = originalValue;

                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'An error occurred while updating status. Please check your connection.',
                            confirmButtonText: 'OK'
                        });
                    });
            }

            $(document).ready(function() {
                $('#projectTable').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    lengthChange: true,
                    pageLength: 10,
                    order: [
                        [9, 'desc']
                    ],
                    language: {
                        search: "Search Projects:",
                        lengthMenu: "Show _MENU_ entries per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ projects",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous",
                        },
                    },
                });

                // Initialize Fancybox for image galleries
                Fancybox.bind("[data-fancybox]", {
                    Toolbar: {
                        display: {
                            left: ["infobar"],
                            middle: ["zoomIn", "zoomOut", "toggle1to1", "rotateCCW", "rotateCW"],
                            right: ["slideshow", "download", "thumbs", "close"],
                        },
                    },
                    Thumbs: {
                        autoStart: false,
                        showOnStart: false,
                    },
                    Images: {
                        zoom: true,
                        protect: false,
                    },
                });
            });

            function updatePriority(id, priority) {
                // Show loading state
                const selectElement = event.target;
                const originalValue = selectElement.getAttribute('data-original-value') || selectElement.value;
                selectElement.disabled = true;

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
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        selectElement.disabled = false;

                        if (data.success) {
                            // Store new value as original
                            selectElement.setAttribute('data-original-value', priority);

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message || 'Priority updated successfully!',
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        } else {
                            // Revert to original value on error
                            selectElement.value = originalValue;

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to update priority.',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        selectElement.disabled = false;
                        selectElement.value = originalValue;

                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'An error occurred while updating priority. Please check your connection.',
                            confirmButtonText: 'OK'
                        });
                    });
            }

            // Ambil elemen tombol
            const scrollToTopBtn = document.getElementById('scrollToTopBtn');
            const scrollToProjectBtn = document.getElementById('scrollToProjectBtn');

            // Tampilkan tombol saat pengguna menggulir ke bawah
            window.addEventListener('scroll', () => {
                const projectListSection = document.querySelector('.table-section');
                const projectListOffset = projectListSection ? projectListSection.offsetTop : 800;

                if (window.scrollY > 200) { // Tampilkan tombol jika scroll lebih dari 200px
                    scrollToTopBtn.style.display = 'block';
                } else {
                    scrollToTopBtn.style.display = 'none';
                }

                // Show scroll to project button when not in project list area
                if (window.scrollY < projectListOffset - 100) {
                    scrollToProjectBtn.style.display = 'block';
                } else {
                    scrollToProjectBtn.style.display = 'none';
                }
            });

            // Fungsi untuk menggulir ke atas
            scrollToTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth' // Gulir dengan animasi halus
                });
            });

            // Fungsi untuk menggulir ke Project List
            scrollToProjectBtn.addEventListener('click', () => {
                const projectListSection = document.querySelector('.table-section');
                if (projectListSection) {
                    projectListSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });

            document.addEventListener('DOMContentLoaded', function() {
                var alertSuccess = document.getElementById('alertSuccessMessage');
                if (alertSuccess) {
                    alertSuccess.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
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
