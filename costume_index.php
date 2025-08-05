<?php
session_start();
require 'config.php';
include 'db.php';
include 'image_helper.php';
require 'vendor/autoload.php';

use Carbon\Carbon;

// Ambil keyword dan filter status dari URL
$search = $_GET['search'] ?? '';
$filter = $_GET['project_status'] ?? '';

// Pagination settings
$itemsPerPage = 12; // Menampilkan 12 item per halaman untuk performa lebih baik
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

function isThisWeek($deadline)
{
    if (empty($deadline)) {
        return false; // Jika deadline kosong, kembalikan false
    }

    $currentDate = new DateTime();
    $startOfWeek = $currentDate->modify('this week')->setTime(0, 0, 0); // Awal minggu (Senin)
    $endOfWeek = (clone $startOfWeek)->modify('+6 days')->setTime(23, 59, 59); // Akhir minggu (Minggu)

    $deadlineDate = new DateTime($deadline);

    return $deadlineDate >= $startOfWeek && $deadlineDate <= $endOfWeek;
}

// Filter berdasarkan kategori
$sql = "SELECT * FROM gallery WHERE category = 'costume' AND project_status != 'archived' AND project_name LIKE ?";
$params = ["%$search%"];

if (isset($_GET['this_week']) && $_GET['this_week'] == '1') {
    $startOfWeekObj = new DateTime();
    $startOfWeekObj->modify('this week');
    $startOfWeek = $startOfWeekObj->format('Y-m-d');
    $endOfWeekObj = clone $startOfWeekObj;
    $endOfWeekObj->modify('+6 days');
    $endOfWeek = $endOfWeekObj->format('Y-m-d');
    $sql .= ' AND deadline BETWEEN ? AND ?';
    $params[] = $startOfWeek;
    $params[] = $endOfWeek;
}

if (!empty($_GET['project_status'])) {
    $sql .= ' AND project_status = ?';
    $params[] = $_GET['project_status'];
}

if (!empty($_GET['priority'])) {
    $sql .= ' AND priority = ?';
    $params[] = $_GET['priority'];
}

$sql .= ' ORDER BY createAt DESC LIMIT ' . $itemsPerPage . ' OFFSET ' . $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

// Count total projects for pagination
$countSql = "SELECT COUNT(*) FROM gallery WHERE category = 'costume' AND project_status != 'archived' AND project_name LIKE ?";
$countParams = ["%$search%"];

if (isset($_GET['this_week']) && $_GET['this_week'] == '1') {
    $startOfWeekObj = new DateTime();
    $startOfWeekObj->modify('this week');
    $startOfWeek = $startOfWeekObj->format('Y-m-d');
    $endOfWeekObj = clone $startOfWeekObj;
    $endOfWeekObj->modify('+6 days');
    $endOfWeek = $endOfWeekObj->format('Y-m-d');
    $countSql .= ' AND deadline BETWEEN ? AND ?';
    $countParams[] = $startOfWeek;
    $countParams[] = $endOfWeek;
}

if (!empty($_GET['project_status'])) {
    $countSql .= ' AND project_status = ?';
    $countParams[] = $_GET['project_status'];
}

if (!empty($_GET['priority'])) {
    $countSql .= ' AND priority = ?';
    $countParams[] = $_GET['priority'];
}

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($countParams);
$totalProjects = $countStmt->fetchColumn();
$totalPages = ceil($totalProjects / $itemsPerPage);

$startOfWeekObj = new DateTime();
$startOfWeekObj->modify('this week');
$startOfWeek = $startOfWeekObj->format('Y-m-d');
$endOfWeekObj = clone $startOfWeekObj;
$endOfWeekObj->modify('+6 days');
$endOfWeek = $endOfWeekObj->format('Y-m-d');

$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery WHERE category = 'costume' AND project_status != 'archived' AND deadline BETWEEN ? AND ?");
$stmt->execute([$startOfWeek, $endOfWeek]);
$this_week_count = $stmt->fetchColumn();

// Fungsi warna status
function getStatusClass($status)
{
    switch (strtolower($status)) {
        case 'upcoming':
            return 'background-color: #31D2F2;'; // blue
        case 'in progress':
            return 'background-color: #FFCA2C;'; // yellow
        case 'revision':
            return 'background-color: #6610f2;'; // purple
        case 'completed':
            return 'background-color: #198754;'; // green
        default:
            return 'background-color: #d1d5db;'; // light gray
    }
}

function getPriorityClass($priority)
{
    switch (strtolower($priority)) {
        case 'urgent':
            return 'background-color: #dc3545;'; // Red
        case 'high':
            return 'background-color: #ffc107;'; // Yellow
        case 'normal':
            return 'background-color: #007bff;'; // Blue
        case 'low':
            return 'background-color: #6c757d;'; // Gray
        default:
            return 'background-color: #6c757d;'; // Gray
    }
}

$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery WHERE category = 'costume'");
$stmt->execute();
$total_projects = $stmt->fetchColumn();

// Hitung jumlah proyek berdasarkan status untuk kategori
$status_counts = [
    'Upcoming' => 0,
    'Completed' => 0,
    'In Progress' => 0,
    'Revision' => 0,
];

foreach ($status_counts as $status => &$count) {
    $sql = "SELECT COUNT(*) AS total FROM gallery WHERE category = 'costume' AND project_status = ?";
    $params = [$status];

    // Tambahkan filter priority jika ada
    if (!empty($_GET['priority'])) {
        $sql .= ' AND priority = ?';
        $params[] = $_GET['priority'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $count = $stmt->fetchColumn();
}
unset($count);

// Count projects by priority for category
$priority_counts = [
    'Urgent' => 0,
    'High' => 0,
    'Normal' => 0,
    'Low' => 0,
];

foreach ($priority_counts as $priority => &$count) {
    $sql = "SELECT COUNT(*) AS total FROM gallery WHERE category = 'costume' AND priority = ? AND project_status != 'archived'";
    $params = [$priority];

    // Add status filter if exists
    if (!empty($_GET['project_status'])) {
        $sql .= ' AND project_status = ?';
        $params[] = $_GET['project_status'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $count = $stmt->fetchColumn();
}
unset($count);

// Hitung total proyek (kecuali Archived)
$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery WHERE category = 'costume' AND project_status != 'Archived'");
$stmt->execute();
$total_projects = $stmt->fetchColumn();

// Periksa apakah pengguna sudah login
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $isLoggedIn ? $_SESSION : null;
?>

<!DOCTYPE html>
<html data-bs-theme="light">

    <head>
        <meta charset="UTF-8">
        <title>Costume Project List</title>
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
        <style>
            body {
                font-family: Arial, sans-serif;
                color: #000;
                padding: 12px;
                text-align: center;
            }

            html,
            body {
                height: 100%;
                /* Pastikan tinggi halaman penuh */
                display: flex;
                /* Gunakan flexbox */
                flex-direction: column;
                /* Atur elemen secara vertikal */
            }

            .container-fluid {
                flex: 1;
                /* Isi ruang yang tersedia di antara header dan footer */
            }

            footer {
                margin-top: auto;
                /* Dorong footer ke bagian bawah */
                background-color: rgba(0, 0, 0, 0.05);
                /* Warna latar belakang */
                text-align: center;
                padding: 10px;
            }

            .btn-indigo {
                background-color: #6610f2;
                color: #fff;
                --btn-bg: #6610f2;
            }

            .btn-indigo:hover {
                background-color: #520dc2;
                color: #fff;
                --btn-bg: #520dc2;
            }

            .btn-indigo.active {
                background-color: #6610f2;
                /* Warna latar belakang saat aktif */
                color: #fff;
                /* Warna teks saat aktif */
                border-color: #6610f2;
                /* Warna border saat aktif */
            }

            .search-filter {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
                gap: 10px;
            }

            .card-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: flex-start;
            }

            .card:hover {
                border-color: rgb(179, 179, 179);
            }

            /* .card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    } */

            .card img {
                width: 100%;
                /* Pastikan gambar memenuhi lebar card */
                height: 150px;
                /* Tetapkan tinggi tetap */
                object-fit: contain;
                /* Pastikan gambar terlihat sepenuhnya tanpa terpotong */
                background-color: #f8f8f8;
                /* Tambahkan latar belakang untuk area kosong */
                border-radius: 4px;
                /* Tambahkan border radius untuk estetika */
            }

            .card-body {
                padding: 10px;
            }

            .status-label {
                color: white;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                display: inline-block;
                margin-top: 5px;
            }

            .deadline {
                font-size: 12px;
                color: #888;
                margin-top: 2px;
            }

            /* Modal styles */
            .modal {
                display: none;
                position: fixed;
                z-index: 99;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.7);
            }

            .modal-content {
                margin: 5% auto;
                display: block;
                max-width: 80%;
                /* Maksimal 80% dari lebar layar */
                max-height: 80vh;
                /* Maksimal 80% dari tinggi layar */
                height: auto;
                /* Menjaga rasio aspek gambar */
                object-fit: contain;
                /* Menjaga gambar tetap proporsional */
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .modal-content,
            .close {}

            .close {
                position: absolute;
                top: 20px;
                right: 30px;
                color: #fff;
                font-size: 30px;
                font-weight: bold;
                cursor: pointer;
            }

            .modal-backdrop {
                z-index: 1040;
                /* Pastikan lebih rendah dari modal */
            }

            .modal {
                z-index: 1050;
                /* Pastikan lebih tinggi dari backdrop */
            }

            /* Pastikan iframe memenuhi layar */
            #googleSlideIframe {
                width: 100%;
                height: 100vh;
                /* Tinggi iframe mengikuti tinggi viewport */
                border: none;
            }

            /* Pastikan modal-body tidak memiliki padding dan memenuhi modal */
            .modal-fullscreen .modal-body {
                overflow: hidden;
                /* Hilangkan scroll */
                padding: 0;
                margin: 0;
                height: 100%;
                /* Pastikan modal-body memenuhi modal */
            }

            /* Pastikan modal-content memenuhi layar */
            .modal-fullscreen .modal-content {
                border-radius: 0;
                /* Hilangkan border radius */
                height: 100vh;
                /* Pastikan modal-content memenuhi tinggi viewport */
            }

            /* Pastikan modal-dialog memenuhi layar */
            .modal-fullscreen .modal-dialog {
                margin: 0;
                max-width: 100%;
                height: 100%;
            }

            .status-badge {
                padding: 4px 8px;
                border-radius: 5px;
                color: white;
                font-weight: bold;
                display: inline-block;
            }

            .btn.active {
                outline: 2px solid var(--btn-bg, var(--bs-btn-bg));
                /* Tambahkan outline */
                outline-offset: 2px;
                /* Berikan jarak antara outline dan elemen */
            }

            .card-body strong {
                color: var(--bs-body-color);
                /* Warna teks dinamis berdasarkan tema */
                font-size: 16px;
                cursor: pointer;
            }

            .card-body p {
                color: var(--bs-body-color);
                /* Warna teks dinamis berdasarkan tema */
                font-size: 14px;
                margin-top: 8px;
                line-height: 1.5;
            }

            p.text-center {
                color: var(--bs-body-color);
                /* Warna teks dinamis berdasarkan tema */
                font-size: 16px;
                margin-top: 20px;
            }

            /* Pagination styling */
            .pagination .page-link {
                color: #6610f2;
                border-color: #e2e8f0;
                background-color: #ffffff;
            }

            .pagination .page-link:hover {
                color: #520dc2;
                background-color: #f8fafc;
                border-color: #6610f2;
            }

            .pagination .page-item.active .page-link {
                background-color: #6610f2;
                border-color: #6610f2;
                color: #ffffff;
            }

            .pagination .page-item.disabled .page-link {
                color: #94a3b8;
                background-color: #f8fafc;
                border-color: #e2e8f0;
            }
        </style>
    </head>

    <body>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <!-- Logo CCM -->
                    <a href="index.php">
                        <img src="uploads/ccm.png" alt="CCM Logo" style="width: 50px; height: 50px; margin-right: 5px;">
                    </a>
                    <h3 class="fw-bold text-header mb-0">Costume Project List</h3>
                </div>
                <!-- Tombol Login atau Dashboard -->
                <div>
                    <button id="toggleDarkMode" class="btn btn-outline-secondary">
                        <i class="bi bi-moon"></i>
                    </button>
                    <?php if ($isLoggedIn): ?>
                    <a href="costume_admin.php" class="btn btn-primary">
                        Dashboard
                    </a>
                    <?php else: ?>
                    <a href="login.php" class="btn btn-secondary">
                        Login
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <form method="GET" class="row g-3 align-items-center mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search Project..."
                        value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
            <div style="display: flex; gap: 20px;">
                <form method="GET" action="costume_index.php">
                    <button type="submit" name="this_week" value="1"
                        class="btn fw-semibold text-danger-emphasis bg-danger-subtle border border-danger-subtle <?= isset($_GET['this_week']) && $_GET['this_week'] == '1' ? 'active' : '' ?>">
                        This Week: <?= $this_week_count ?>
                    </button>
                </form>

                <div style="border-left: 2px solid #ccc; height: 40px;"></div>

                <form method="GET" action="costume_index.php">
                    <button type="submit" name="project_status" value=""
                        class="btn btn-secondary <?= !isset($_GET['project_status']) || $_GET['project_status'] === '' ? 'active' : '' ?>">
                        All Project: <?= isset($total_projects) ? $total_projects : 0 ?>
                    </button>
                </form>
                <form method="GET" action="costume_index.php">
                    <button type="submit" name="project_status" value="Upcoming"
                        class="btn btn-info <?= isset($_GET['project_status']) && $_GET['project_status'] === 'Upcoming' ? 'active' : '' ?>">
                        Upcoming: <?= $status_counts['Upcoming'] ?? 0 ?>
                    </button>
                    <input type="hidden" name="priority" value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                </form>
                <form method="GET" action="costume_index.php">
                    <button type="submit" name="project_status" value="In Progress"
                        class="btn btn-warning <?= isset($_GET['project_status']) && $_GET['project_status'] === 'In Progress' ? 'active' : '' ?>">
                        In Progress: <?= $status_counts['In Progress'] ?? 0 ?>
                    </button>
                    <input type="hidden" name="priority" value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                </form>
                <form method="GET" action="costume_index.php">
                    <button type="submit" name="project_status" value="Revision"
                        class="btn btn-indigo <?= isset($_GET['project_status']) && $_GET['project_status'] === 'Revision' ? 'active' : '' ?>">
                        Revision: <?= $status_counts['Revision'] ?? 0 ?>
                    </button>
                    <input type="hidden" name="priority" value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                </form>
                <form method="GET" action="costume_index.php">
                    <button type="submit" name="project_status" value="Completed"
                        class="btn btn-success <?= isset($_GET['project_status']) && $_GET['project_status'] === 'Completed' ? 'active' : '' ?>">
                        Completed: <?= $status_counts['Completed'] ?? 0 ?>
                    </button>
                    <input type="hidden" name="priority" value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                </form>

                <div style="border-left: 2px solid #ccc; height: 40px;"></div>

                <form method="GET" action="costume_index.php" class="d-flex align-items-center">
                    <select name="priority" class="form-select me-2" onchange="this.form.submit()">
                        <option value="">Filter by Priority</option>
                        <option value="Urgent"
                            <?= isset($_GET['priority']) && $_GET['priority'] === 'Urgent' ? 'selected' : '' ?>>
                            Urgent</option>
                        <option value="High"
                            <?= isset($_GET['priority']) && $_GET['priority'] === 'High' ? 'selected' : '' ?>>
                            High</option>
                        <option value="Normal"
                            <?= isset($_GET['priority']) && $_GET['priority'] === 'Normal' ? 'selected' : '' ?>>
                            Normal</option>
                        <option value="Low"
                            <?= isset($_GET['priority']) && $_GET['priority'] === 'Low' ? 'selected' : '' ?>>Low
                        </option>
                    </select>
                    <input type="hidden" name="project_status"
                        value="<?= htmlspecialchars($_GET['project_status'] ?? '') ?>">
                </form>
            </div>
            <br>

            <div class="card-grid">
                <?php if (empty($projects)): ?>
                <p class="text-center">No projects found for the selected filters.</p>
                <?php else: ?>
                <?php foreach ($projects as $row): ?>
                <div class="card" style="width: 18.5rem; position: relative;">
                    <?php if (isThisWeek($row['deadline'])): ?>
                    <small class="fw-semibold text-danger-emphasis bg-danger-subtle">
                        This Week!
                    </small>
                    <?php endif; ?>

                    <?php
                    // Parse project image (support both old and new format)
                    $projectImages = parseImageData($row['project_image']);
                    $firstProjectImage = !empty($projectImages) ? $projectImages[0] : null;
                    ?>

                    <?php if ($firstProjectImage): ?>
                    <?php if (count($projectImages) > 1): ?>
                    <!-- Multiple images - use gallery -->
                    <div style="position: relative; cursor: pointer;"
                        onclick="openProjectGallery(<?= htmlspecialchars(json_encode($projectImages)) ?>, 0)">
                        <img src="uploads/projects/<?= htmlspecialchars($firstProjectImage) ?>"
                            style="width: 100%; height: 200px; object-fit: contain;" alt="Project Image">
                        <div
                            style="position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                            <i class="bi bi-images"></i> <?= count($projectImages) ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Single image -->
                    <img src="uploads/projects/<?= htmlspecialchars($firstProjectImage) ?>"
                        style="cursor: pointer; width: 100%; height: 200px; object-fit: contain;" alt="Project Image"
                        onclick="openModal(this.src)">
                    <?php endif; ?>
                    <?php else: ?>
                    <div class="no-image-soft d-flex align-items-center justify-content-center"
                        style="height: 200px; background: #f8f9fa;">
                        <div class="text-center">
                            <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0 mt-2">No Image</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="card-body">
                        <?php if (!empty($row['subform_embed'])): ?>
                        <strong style="cursor: pointer;"
                            onclick="openGoogleSlideModal('<?= htmlspecialchars($row['subform_embed']) ?>')">
                            <?= htmlspecialchars($row['project_name']) ?>
                        </strong><br>
                        <?php else: ?>
                        <strong>
                            <?= htmlspecialchars($row['project_name']) ?>
                        </strong><br>
                        <?php endif; ?>
                        <span class="status-label" style="<?= getStatusClass($row['project_status']) ?>">
                            <?= htmlspecialchars($row['project_status']) ?>
                        </span>
                        <span class="status-label"
                            style="<?= getPriorityClass($row['priority']) ?> margin-left: 5px;">
                            P :
                            <?= htmlspecialchars($row['priority']) ?>
                        </span>
                        <div class="deadline">Quantity: <?= htmlspecialchars($row['quantity']) ?></div>
                        <?php if ($row['deadline']): ?>
                        <div class="deadline">
                            Deadline: <?= htmlspecialchars(Carbon::parse($row['deadline'])->format('d M Y')) ?>
                        </div>
                        <?php endif; ?>
                        <p style="margin-top: 8px; font-size: 14px;">
                            <?= nl2br(htmlspecialchars($row['description'])) ?>
                        </p>

                        <!-- Material Images Section -->
                        <?php
                        $materialImages = parseImageData($row['material_image']);
                        $firstMaterialImage = !empty($materialImages) ? $materialImages[0] : null;
                        ?>

                        <?php if ($firstMaterialImage): ?>
                        <?php if (count($materialImages) > 1): ?>
                        <!-- Multiple material images - use gallery -->
                        <div style="margin-top: 5px; position: relative; cursor: pointer;"
                            onclick="openMaterialGallery(<?= htmlspecialchars(json_encode($materialImages)) ?>, 0)">
                            <img src="uploads/materials/<?= htmlspecialchars($firstMaterialImage) ?>"
                                alt="Submission Notes"
                                style="width: 100%; height: 150px; object-fit: contain; border-radius: 4px;">
                            <div
                                style="position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                <i class="bi bi-images"></i> <?= count($materialImages) ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <!-- Single material image -->
                        <div style="margin-top: 5px; cursor: pointer;">
                            <img src="uploads/materials/<?= htmlspecialchars($firstMaterialImage) ?>"
                                alt="Submission Notes"
                                style="width: 100%; height: 150px; object-fit: contain; border-radius: 4px;"
                                onclick="openModal(this.src)">
                        </div>
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="no-image-soft d-flex align-items-center justify-content-center"
                            style="height: 150px; background: #f8f9fa; margin-top: 5px; border-radius: 4px;">
                            <div class="text-center">
                                <i class="bi bi-file-earmark text-muted" style="font-size: 1.5rem;"></i>
                                <p class="text-muted mb-0 mt-1" style="font-size: 0.9rem;">No Submission Notes</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <?php
            // Build pagination URL parameters once to avoid formatter issues
            $searchParam = !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
            $statusParam = !empty($_GET['project_status']) ? '&project_status=' . urlencode($_GET['project_status']) : '';
            $priorityParam = !empty($_GET['priority']) ? '&priority=' . urlencode($_GET['priority']) : '';
            $weekParam = !empty($_GET['this_week']) ? '&this_week=1' : '';
            $urlParams = $searchParam . $statusParam . $priorityParam . $weekParam;
            ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Previous button -->
                    <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $currentPage - 1 . $urlParams ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    </li>
                    <?php endif; ?>

                    <!-- Page numbers -->
                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                    
                    if ($startPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=1<?= $urlParams ?>">1</a>
                    </li>
                    <?php if ($startPage > 2): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i . $urlParams ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $totalPages . $urlParams ?>"><?= $totalPages ?></a>
                    </li>
                    <?php endif; ?>

                    <!-- Next button -->
                    <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $currentPage + 1 . $urlParams ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <footer class="text-secondary text-center py-1 mt-4 rounded" style="background-color: rgba(0, 0, 0, 0.05);">
            <div class="mb-0">Create with ❤️ by <a class="text-primary fw-bold" href=""
                    style="text-decoration: none;">IT
                    DCM</a></div>
        </footer>

        <!-- Modal for Images-->
        <div id="imgModal" class="modal" onclick="closeModal()">
            <span class="close" onclick="closeModal()">&times;</span>
            <img class="modal-content" id="modalImage" alt="Preview Image">
        </div>

        <!-- Modal for Google Slide -->
        <div class="modal fade" id="googleSlideModal" tabindex="-1" aria-labelledby="googleSlideModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-fullscreen modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe id="googleSlideIframe" class="w-100 h-100" frameborder="0" allowfullscreen></iframe>
                        <p id="fallbackLink" style="display: none; text-align: center; margin-top: 20px;">
                            Your browser does not support embedded content.
                            <a href="#" id="googleSlideLink" target="_blank">Click here to view the slides</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openModal(src) {
                const modal = document.getElementById("imgModal");
                const modalImage = document.getElementById("modalImage");

                modal.style.display = "block";
                modalImage.src = src;
            }

            function closeModal() {
                const modal = document.getElementById("imgModal");
                modal.style.display = "none";
            }

            // Gallery functions for multiple images
            function openProjectGallery(images, startIndex = 0) {
                const galleryItems = images.map(image => ({
                    src: `uploads/projects/${image}`,
                    type: 'image'
                }));

                Fancybox.show(galleryItems, {
                    startIndex: startIndex,
                    groupAll: false,
                    Thumbs: {
                        showOnStart: false
                    }
                });
            }

            function openMaterialGallery(images, startIndex = 0) {
                const galleryItems = images.map(image => ({
                    src: `uploads/materials/${image}`,
                    type: 'image'
                }));

                Fancybox.show(galleryItems, {
                    startIndex: startIndex,
                    groupAll: false,
                    Thumbs: {
                        showOnStart: false
                    }
                });
            }

            function openGoogleSlideModal(embedLink) {
                function isWebOS() {
                    const userAgent = navigator.userAgent.toLowerCase();
                    return userAgent.includes("webos") || userAgent.includes("smarttv");
                }

                // Jika bukan WebOS, tampilkan embed iframe
                const embedUrl = embedLink.replace('/edit', '/embed');
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

            function closeGoogleSlideModal() {
                const modal = document.getElementById("googleSlideModal");
                const iframe = document.getElementById("googleSlideIframe");
                iframe.src = ""; // Clear the iframe source
                modal.style.display = "none";
            }

            const toggleDarkMode = document.getElementById('toggleDarkMode');
            const html = document.documentElement;

            // Periksa preferensi dark mode dari localStorage
            if (localStorage.getItem('theme') === 'dark') {
                html.setAttribute('data-bs-theme', 'dark');
                toggleDarkMode.innerHTML = '<i class="bi bi-sun"></i>';
            }

            // Tambahkan event listener untuk tombol toggle
            toggleDarkMode.addEventListener('click', () => {
                if (html.getAttribute('data-bs-theme') === 'dark') {
                    html.setAttribute('data-bs-theme', 'light');
                    toggleDarkMode.innerHTML = '<i class="bi bi-moon"></i>';
                    localStorage.setItem('theme', 'light');
                } else {
                    html.setAttribute('data-bs-theme', 'dark');
                    toggleDarkMode.innerHTML = '<i class="bi bi-sun"></i>';
                    localStorage.setItem('theme', 'dark');
                }
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </body>

</html>
