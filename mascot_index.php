<?php
session_start();
require 'config.php';
include 'db.php';
require 'vendor/autoload.php';

use Carbon\Carbon;

// Ambil keyword dan filter status dari URL
$search = $_GET['search'] ?? '';
$filter = $_GET['project_status'] ?? '';

function isThisWeek($deadline)
{
    if (empty($deadline)) {
        return false; // Jika deadline kosong, kembalikan false
    }

    $currentDate = new DateTime();
    $startOfWeek = (clone $currentDate)->modify('this week')->setTime(0, 0, 0); // Awal minggu (Senin)
    $endOfWeek = (clone $startOfWeek)->modify('+6 days')->setTime(23, 59, 59); // Akhir minggu (Minggu)

    $deadlineDate = new DateTime($deadline);

    return $deadlineDate >= $startOfWeek && $deadlineDate <= $endOfWeek;
}

// Filter berdasarkan kategori
$sql = "SELECT * FROM gallery WHERE category = 'mascot' AND project_status != 'archived' AND project_name LIKE ?";
$params = ["%$search%"];

if (isset($_GET['this_week']) && $_GET['this_week'] == '1') {
    $startOfWeekObj = new DateTime();
    $startOfWeekObj->modify('this week');
    $startOfWeek = $startOfWeekObj->format('Y-m-d');

    $endOfWeekObj = new DateTime();
    $endOfWeekObj->modify('this week +6 days');
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

$sql .= ' ORDER BY createAt DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

$startOfWeekObj = new DateTime();
$startOfWeekObj->modify('this week');
$startOfWeek = $startOfWeekObj->format('Y-m-d');

$endOfWeekObj = new DateTime();
$endOfWeekObj->modify('this week +6 days');
$endOfWeek = $endOfWeekObj->format('Y-m-d');

$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery WHERE category = 'mascot' AND project_status != 'archived' AND deadline BETWEEN ? AND ?");
$stmt->execute([$startOfWeek, $endOfWeek]);
$this_week_count = $stmt->fetchColumn();

// Fungsi warna status
function getStatusClass($status)
{
    switch (strtolower($status)) {
        case 'upcoming':
            return 'background-color: #31D2F2;'; // blue
        case 'urgent':
            return 'background-color: #ef4444;'; // red
        case 'in progress':
            return 'background-color: #FFCA2C;'; // yellow
        case 'revision':
            return 'background-color: #fd7e14;'; // orange
        case 'completed':
            return 'background-color: #198754;'; // green
        default:
            return 'background-color: #d1d5db;'; // light gray
    }
}

function getPriorityClass($priority)
{
    switch (strtolower($priority)) {
        case 'high':
            return 'background-color: #dc3545;'; // Red
        case 'medium':
            return 'background-color: #ffc107;'; // Yellow
        case 'normal':
            return 'background-color: #0d6efd;'; // Blue/Cyan
        case 'low':
            return 'background-color: #28a745;'; // Green
        default:
            return 'background-color: #6c757d;'; // Gray
    }
}

$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery WHERE category = 'mascot'");
$stmt->execute();
$total_projects = $stmt->fetchColumn();

// Hitung jumlah proyek berdasarkan status untuk kategori
$status_counts = [
    'Upcoming' => 0,
    'Completed' => 0,
    'In Progress' => 0,
    'Revision' => 0,
    'Urgent' => 0,
];

foreach ($status_counts as $status => &$count) {
    $sql = "SELECT COUNT(*) AS total FROM gallery WHERE category = 'mascot' AND project_status = ?";
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

// Hitung total proyek (kecuali Archived)
$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery WHERE category = 'mascot' AND project_status != 'Archived'");
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
        <title>Mascot Project List</title>
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                color: #000;
                padding: 0;
                margin: 0;
                background: linear-gradient(135deg, #5B4CD0, #5E22CE);
                background-attachment: fixed;
                background-size: cover;
                min-height: 100vh;
            }

            html,
            body {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .container-fluid {
                flex: 1;
                padding: 1rem;
                margin: 0 auto;
            }

            /* Header improvements */
            .header-section {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 0.8rem;
                margin-bottom: 1.2rem;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .text-header {
                color: white;
                text-shadow:
                    -1px -1px 0 rgba(0, 0, 0, 0.3),
                    1px -1px 0 rgba(0, 0, 0, 0.3),
                    -1px 1px 0 rgba(0, 0, 0, 0.3),
                    1px 1px 0 rgba(0, 0, 0, 0.3);
                font-weight: 600;
            }

            /* Search section improvements */
            .search-section {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 1rem;
                margin-bottom: 1.2rem;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            /* Filter buttons improvements */
            .filters-section {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 1rem;
                margin-bottom: 1.2rem;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .filters-container {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                align-items: center;
                justify-content: center;
            }

            .filter-group {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                align-items: center;
            }

            .filter-divider {
                width: 2px;
                height: 30px;
                background: rgba(255, 255, 255, 0.3);
                margin: 0 0.5rem;
            }

            footer {
                margin-top: auto;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                text-align: center;
                padding: 1rem;
                color: white;
            }

            /* Button improvements */
            .btn {
                border-radius: 25px;
                font-weight: 500;
                transition: all 0.3s ease;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }

            .btn-secondary.active,
            .btn-secondary:active {
                background-color: #adb5bd !important;
                /* Lebih terang dari default */
                border-color: #adb5bd !important;
                color: #fff !important;
            }

            .btn-danger.active,
            .btn-danger:active {
                background-color: #EF4444 !important;
                /* Lebih terang dari default */
                border-color: #EF4444 !important;
                color: #fff !important;
                box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.3) !important;
            }

            .btn-success.active,
            .btn-success:active {
                background-color: #28A745 !important;
                /* Lebih terang dari default */
                border-color: #28A745 !important;
                color: #fff !important;
                box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.3) !important;
            }

            .btn-info.active,
            .btn-info:active {
                background-color: #0DCAF0 !important;
                /* Lebih terang dari default */
                border-color: #0DCAF0 !important;
                box-shadow: 0 0 0 3px rgba(13, 202, 240, 0.3) !important;
            }

            .btn-warning.active,
            .btn-warning:active {
                background-color: #FFCA2C !important;
                /* Lebih terang dari default */
                border-color: #FFCA2C !important;
                color: #000 !important;
                /* Teks hitam untuk kontras yang baik */
                box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.3) !important;
            }

            /* Dark mode untuk tombol filter status */
            [data-bs-theme="dark"] .btn-secondary.active {
                background-color: #6c757d !important;
                border-color: #6c757d !important;
                color: #fff !important;
            }

            [data-bs-theme="dark"] .btn-danger.active {
                background-color: #e74c3c !important;
                border-color: #e74c3c !important;
                color: #fff !important;
            }

            [data-bs-theme="dark"] .btn-success.active {
                background-color: #27ae60 !important;
                border-color: #27ae60 !important;
                color: #fff !important;
            }

            [data-bs-theme="dark"] .btn-info.active {
                background-color: #3498db !important;
                border-color: #3498db !important;
                color: #fff !important;
            }

            [data-bs-theme="dark"] .btn-warning.active {
                background-color: #f39c12 !important;
                border-color: #f39c12 !important;
                color: #fff !important;
            }

            .btn-indigo {
                background-color: #fd7e14;
                color: #fff;
                --btn-bg: #fd7e14;
            }

            .btn-indigo:hover {
                background-color: #e85d04;
                color: #fff;
                --btn-bg: #e85d04;
            }

            .btn-indigo.active {
                background-color: #fd7e14;
                color: #fff;
                border-color: #fd7e14;
            }

            .btn-primary-custom {
                background-color: #f59e0b;
                /* Amber/orange */
                border-color: #f59e0b;
                color: #fff;
                font-weight: 500;
                border-radius: 25px;
                transition: all 0.3s ease;
            }

            .btn-primary-custom:hover {
                background-color: #d97706;
                border-color: #d97706;
                color: #fff;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }

            .btn.bg-danger-subtle.active {
                background-color: #f8d7da !important;
                color: #721c24 !important;
                font-weight: 600 !important;
                outline: 2px solid #dc3545 !important;
                /* Tambahkan outline */
                outline-offset: 2px !important;
                transform: scale(1.02) !important;
            }

            .btn.bg-danger-subtle:hover {
                background-color: #f5c2c7 !important;
                border-color: #dc3545 !important;
                color: #721c24 !important;
            }

            /* Dark mode untuk This Week button */
            [data-bs-theme="dark"] .btn.bg-danger-subtle {
                background-color: #2c0b0e !important;
                color: #ea868f !important;
                border-color: #842029 !important;
            }

            [data-bs-theme="dark"] .btn.bg-danger-subtle.active {
                background-color: #58151c !important;
                color: #f8d7da !important;
                outline: 2px solid #dc3545 !important;
                /* Tambahkan outline untuk dark mode */
                outline-offset: 2px !important;
                /* Tambahkan outline-offset untuk dark mode */
                box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.3),
                    0 4px 15px rgba(220, 53, 69, 0.4) !important;
            }

            [data-bs-theme="dark"] .btn.bg-danger-subtle:hover {
                background-color: #842029 !important;
                color: #f8d7da !important;
            }

            .input-group .btn:hover {
                transform: none;
                /* Menghilangkan efek hover untuk semua tombol di area search-filter */
            }

            .search-filter {
                display: flex;
                justify-content: space-between;
                gap: 10px;
            }

            .card-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 1rem;
                justify-content: center;
                padding: 0;
            }

            .card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                border: 1px solid rgba(255, 255, 255, 0.3);
                overflow: hidden;
                transition: all 0.3s ease;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                position: relative;
            }

            .card:hover {
                outline: 2px solid #fd7e14;
                outline-offset: 2px;
                transform: scale(1.04);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
                border-color: #ffffff80;
                z-index: 10;
            }

            .card img {
                width: 100%;
                height: 180px;
                object-fit: contain;
                background-color: #f8f9fa;
                transition: none;
            }

            .card-body {
                padding: 1rem;
                background: rgba(255, 255, 255, 0.9);
            }

            .status-label {
                color: white;
                padding: 3px 6px;
                border-radius: 15px;
                font-size: 12px;
                font-weight: 600;
                display: inline-block;
                margin: 2px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            }

            .deadline {
                font-size: 13px;
                color: #666;
                margin: 4px 0;
                font-weight: 500;
                display: inline-block;
            }

            .deadline-container {
                display: flex;
                gap: 1rem;
                margin: 6px 0;
                flex-wrap: wrap;
            }

            .this-week-badge {
                position: absolute;
                top: 10px;
                right: 10px;
                background: linear-gradient(45deg, #ff6b6b, #ee5a24);
                color: white;
                padding: 4px 8px;
                border-radius: 15px;
                font-size: 11px;
                font-weight: 600;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                z-index: 5;
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
            .close {
                animation: fadein 0.3s;
            }

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

            @keyframes fadein {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes fadeInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
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
                color: white;
                font-size: 18px;
                margin-top: 2rem;
                padding: 2rem;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            /* Responsive improvements */
            @media (max-width: 768px) {
                .container-fluid {
                    padding: 0.5rem;
                }

                .header-section,
                .search-filter-combined {
                    padding: 0.75rem;
                }

                .filters-container {
                    justify-content: flex-start;
                    flex-direction: column;
                    gap: 0.5rem;
                }

                .filter-divider {
                    display: none;
                }

                .card-grid {
                    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                    gap: 1rem;
                }

                .text-header {
                    font-size: 1.5rem;
                }

                .deadline-container {
                    flex-direction: column;
                    gap: 0.25rem;
                }
            }

            @media (max-width: 576px) {
                .card-grid {
                    grid-template-columns: 1fr;
                    padding: 0.5rem 0;
                }

                .btn {
                    font-size: 0.875rem;
                    padding: 0.5rem 1rem;
                }

                .filter-group {
                    justify-content: center;
                    width: 100%;
                }
            }

            /* Dark mode improvements */
            [data-bs-theme="dark"] .header-section,
            [data-bs-theme="dark"] .search-filter-combined {
                background: rgba(0, 0, 0, 0.3);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            [data-bs-theme="dark"] .card {
                background: rgba(33, 37, 41, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            [data-bs-theme="dark"] .card-body {
                background: rgba(33, 37, 41, 0.9);
                color: #fff;
            }

            [data-bs-theme="dark"] .card-body strong {
                color: #fff;
            }

            [data-bs-theme="dark"] .card-body p {
                color: #e9ecef;
            }

            [data-bs-theme="dark"] .deadline {
                color: #ced4da;
            }

            [data-bs-theme="dark"] .form-control,
            [data-bs-theme="dark"] .form-select {
                background: rgba(33, 37, 41, 0.9);
                color: #fff;
                border-color: rgba(255, 255, 255, 0.2);
            }

            [data-bs-theme="dark"] .input-group-text {
                background: rgba(33, 37, 41, 0.9) !important;
                color: #fff;
                border-color: rgba(255, 255, 255, 0.2);
            }

            /* Form improvements */
            .form-control,
            .form-select {
                border-radius: 25px;
                border: 2px solid rgba(255, 255, 255, 0.3);
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                transition: all 0.3s ease;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: rgba(255, 255, 255, 0.6);
                box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
                background: rgba(255, 255, 255, 0.95);
            }

            /* Combined search and filter section */
            .search-filter-combined {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 1rem;
                margin-bottom: 1.2rem;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
        </style>
    </head>

    <body>
        <div class="container-fluid">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <!-- Logo CCM -->
                        <a href="index.php" class="me-3">
                            <img src="uploads/me.png" alt="ME Logo"
                                style="width: 50px; height: 50px; border-radius: 50%; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                        </a>
                        <h3 class="fw-bold text-header mb-0">Mascot Project List</h3>
                    </div>
                    <!-- Tombol Login atau Dashboard -->
                    <div class="d-flex gap-2">
                        <button id="toggleDarkMode" class="btn btn-outline-light">
                            <i class="bi bi-moon"></i>
                        </button>
                        <?php if ($isLoggedIn): ?>
                        <a href="mascot_admin.php" class="btn btn-primary-custom">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                        <?php else: ?>
                        <a href="login.php" class="btn btn-secondary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="search-filter-combined">
                <!-- Search and Filters in one row -->
                <div class="row g-3 align-items-center">
                    <div class="col-lg-4 col-md-6">
                        <!-- Tambahkan form wrapper untuk search -->
                        <form method="GET" action="mascot_index.php">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control border-start-0"
                                    placeholder="Search Project..." value="<?= htmlspecialchars($search) ?>">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <!-- Pertahankan filter yang sudah aktif -->
                            <input type="hidden" name="project_status"
                                value="<?= htmlspecialchars($_GET['project_status'] ?? '') ?>">
                            <input type="hidden" name="priority"
                                value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                            <input type="hidden" name="this_week"
                                value="<?= htmlspecialchars($_GET['this_week'] ?? '') ?>">
                        </form>
                    </div>
                    <div class="col-lg-8 col-md-6">
                        <div class="filters-container">
                            <div class="filter-group">
                                <form method="GET" action="mascot_index.php">
                                    <button type="submit" name="this_week" value="1"
                                        class="btn fw-semibold text-danger-emphasis bg-danger-subtle border border-danger-subtle <?= isset($_GET['this_week']) && $_GET['this_week'] == '1' ? 'active' : '' ?>">
                                        <i class="bi bi-calendar-week me-1"></i>This Week: <?= $this_week_count ?>
                                    </button>
                                    <input type="hidden" name="priority"
                                        value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                                    <input type="hidden" name="project_status"
                                        value="<?= htmlspecialchars($_GET['project_status'] ?? '') ?>">
                                    <input type="hidden" name="search"
                                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                </form>
                            </div>

                            <div class="filter-divider"></div>

                            <div class="filter-group">
                                <form method="GET" action="mascot_index.php">
                                    <button type="submit" name="project_status" value=""
                                        class="btn btn-secondary <?= (!isset($_GET['project_status']) || $_GET['project_status'] === '') && !isset($_GET['this_week']) ? 'active' : '' ?>">
                                        <i class="bi bi-collection me-1"></i>All:
                                        <?= isset($total_projects) ? $total_projects : 0 ?>
                                    </button>
                                    <input type="hidden" name="priority"
                                        value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                                    <input type="hidden" name="search"
                                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                </form>

                                <form method="GET" action="mascot_index.php">
                                    <button type="submit" name="project_status" value="Upcoming"
                                        class="btn btn-info <?= isset($_GET['project_status']) && $_GET['project_status'] === 'Upcoming' ? 'active' : '' ?>">
                                        <i class="bi bi-clock me-1"></i>Upcoming:
                                        <?= $status_counts['Upcoming'] ?? 0 ?>
                                    </button>
                                    <input type="hidden" name="priority"
                                        value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                                    <input type="hidden" name="search"
                                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                    <input type="hidden" name="this_week"
                                        value="<?= htmlspecialchars($_GET['this_week'] ?? '') ?>">
                                </form>

                                <form method="GET" action="mascot_index.php">
                                    <button type="submit" name="project_status" value="In Progress"
                                        class="btn btn-warning <?= isset($_GET['project_status']) && $_GET['project_status'] === 'In Progress' ? 'active' : '' ?>">
                                        <i class="bi bi-gear me-1"></i>Progress:
                                        <?= $status_counts['In Progress'] ?? 0 ?>
                                    </button>
                                    <input type="hidden" name="priority"
                                        value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                                    <input type="hidden" name="search"
                                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                    <input type="hidden" name="this_week"
                                        value="<?= htmlspecialchars($_GET['this_week'] ?? '') ?>">
                                </form>

                                <form method="GET" action="mascot_index.php">
                                    <button type="submit" name="project_status" value="Urgent"
                                        class="btn btn-danger <?= isset($_GET['project_status']) && $_GET['project_status'] === 'Urgent' ? 'active' : '' ?>">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Urgent:
                                        <?= $status_counts['Urgent'] ?? 0 ?>
                                    </button>
                                    <input type="hidden" name="priority"
                                        value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                                    <input type="hidden" name="search"
                                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                    <input type="hidden" name="this_week"
                                        value="<?= htmlspecialchars($_GET['this_week'] ?? '') ?>">
                                </form>

                                <form method="GET" action="mascot_index.php">
                                    <button type="submit" name="project_status" value="Revision"
                                        class="btn btn-indigo <?= isset($_GET['project_status']) && $_GET['project_status'] === 'Revision' ? 'active' : '' ?>">
                                        <i class="bi bi-arrow-repeat me-1"></i>Revision:
                                        <?= $status_counts['Revision'] ?? 0 ?>
                                    </button>
                                    <input type="hidden" name="priority"
                                        value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                                    <input type="hidden" name="search"
                                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                    <input type="hidden" name="this_week"
                                        value="<?= htmlspecialchars($_GET['this_week'] ?? '') ?>">
                                </form>

                                <form method="GET" action="mascot_index.php">
                                    <button type="submit" name="project_status" value="Completed"
                                        class="btn btn-success <?= isset($_GET['project_status']) && $_GET['project_status'] === 'Completed' ? 'active' : '' ?>">
                                        <i class="bi bi-check-circle me-1"></i>Done:
                                        <?= $status_counts['Completed'] ?? 0 ?>
                                    </button>
                                    <input type="hidden" name="priority"
                                        value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                                    <input type="hidden" name="search"
                                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                    <input type="hidden" name="this_week"
                                        value="<?= htmlspecialchars($_GET['this_week'] ?? '') ?>">
                                </form>
                            </div>

                            <div class="filter-divider"></div>

                            <div class="filter-group">
                                <form method="GET" action="mascot_index.php" class="d-flex align-items-center">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-funnel"></i></span>
                                        <select name="priority" class="form-select border-start-0"
                                            onchange="this.form.submit()">
                                            <option value="">All Priority</option>
                                            <option value="High"
                                                <?= isset($_GET['priority']) && $_GET['priority'] === 'High' ? 'selected' : '' ?>>
                                                🔴 High</option>
                                            <option value="Medium"
                                                <?= isset($_GET['priority']) && $_GET['priority'] === 'Medium' ? 'selected' : '' ?>>
                                                🟡 Medium</option>
                                            <option value="Normal"
                                                <?= isset($_GET['priority']) && $_GET['priority'] === 'Normal' ? 'selected' : '' ?>>
                                                🔵 Normal</option>
                                            <option value="Low"
                                                <?= isset($_GET['priority']) && $_GET['priority'] === 'Low' ? 'selected' : '' ?>>
                                                🟢 Low</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="project_status"
                                        value="<?= htmlspecialchars($_GET['project_status'] ?? '') ?>">
                                    <input type="hidden" name="search"
                                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                    <input type="hidden" name="this_week"
                                        value="<?= htmlspecialchars($_GET['this_week'] ?? '') ?>">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-grid">
                <?php if (empty($projects)): ?>
                <p class="text-center">No projects found for the selected filters.</p>
                <?php else: ?>
                <?php foreach ($projects as $row): ?>
                <div class="card">
                    <?php if (isThisWeek($row['deadline'])): ?>
                    <div class="this-week-badge">
                        <i class="bi bi-calendar-event me-1"></i>This Week!
                    </div>
                    <?php endif; ?>
                    <img src="uploads/projects/<?= htmlspecialchars($row['project_image']) ?>"
                        style="cursor: pointer;" alt="No Image Project yet" onclick="openModal(this.src)">
                    <div class="card-body">
                        <strong
                            style="cursor: pointer; font-size: 1.1rem; color: #333; margin-bottom: 0.3rem; display: block;"
                            onclick="openGoogleSlideModal('<?= htmlspecialchars($row['subform_embed']) ?>')">
                            <i class="bi bi-file-earmark-slides me-1"></i><?= htmlspecialchars($row['project_name']) ?>
                        </strong>

                        <div class="d-flex flex-wrap gap-1">
                            <span class="status-label" style="<?= getStatusClass($row['project_status']) ?>">
                                <?= htmlspecialchars($row['project_status']) ?>
                            </span>
                            <span class="status-label" style="<?= getPriorityClass($row['priority']) ?>">
                                Priority: <?= htmlspecialchars($row['priority']) ?>
                            </span>
                        </div>

                        <div class="deadline-container">
                            <div class="deadline">
                                <i class="bi bi-box me-1"></i>Quantity: <?= htmlspecialchars($row['quantity']) ?>
                            </div>
                            <?php if ($row['deadline']): ?>
                            <div class="deadline">
                                <i class="bi bi-calendar-check me-1"></i>Deadline:
                                <?= htmlspecialchars(Carbon::parse($row['deadline'])->format('d M Y')) ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <p style="margin: 6px 0; font-size: 14px; color: #666; line-height: 1.4;">
                            <?= nl2br(htmlspecialchars($row['description'])) ?>
                        </p>

                        <div style="margin-top: 8px; cursor: pointer; border-radius: 10px; overflow: hidden;">
                            <img src="uploads/materials/<?= htmlspecialchars($row['material_image']) ?>"
                                alt="No Submission Notes yet"
                                style="width: 100%; height: 150px; object-fit: contain; background-color: #f8f9fa;"
                                onclick="openModal(this.src)">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <footer class="text-white text-center py-1">
            <div class="mb-0">Create with ❤️ by <a class="text-white fw-bold" href=""
                    style="text-decoration: none;">IT DCM</a></div>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </body>

</html>
