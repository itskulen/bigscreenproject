<?php
session_start();
require 'config.php';
include 'db.php';
include 'image_helper.php';
require 'vendor/autoload.php';

use Carbon\Carbon;

// Get keyword and filter status from URL
$search = $_GET['search'] ?? '';
$filter = $_GET['project_status'] ?? '';

function isThisWeek($deadline)
{
    if (empty($deadline)) {
        return false; // If deadline is empty, return false
    }

    $currentDate = new DateTime();
    $startOfWeek = (clone $currentDate)->modify('this week')->setTime(0, 0, 0); // Start of week (Monday)
    $endOfWeek = (clone $startOfWeek)->modify('+6 days')->setTime(23, 59, 59); // End of week (Sunday)

    $deadlineDate = new DateTime($deadline);

    return $deadlineDate >= $startOfWeek && $deadlineDate <= $endOfWeek;
}

// Filter by category
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

$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery WHERE category = 'mascot'");
$stmt->execute();
$total_projects = $stmt->fetchColumn();

// Count projects by status for category
$status_counts = [
    'Upcoming' => 0,
    'Completed' => 0,
    'In Progress' => 0,
    'Revision' => 0,
];

foreach ($status_counts as $status => &$count) {
    $sql = "SELECT COUNT(*) AS total FROM gallery WHERE category = 'mascot' AND project_status = ?";
    $params = [$status];

    // Add priority filter if exists
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
    $sql = "SELECT COUNT(*) AS total FROM gallery WHERE category = 'mascot' AND priority = ? AND project_status != 'archived'";
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

// Count total projects (except Archived)
$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery WHERE category = 'mascot' AND project_status != 'Archived'");
$stmt->execute();
$total_projects = $stmt->fetchColumn();

// Check if user is logged in
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
        <!-- Fancybox CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                color: #000;
                padding: 0;
                margin: 0;
                background: linear-gradient(135deg, #f8fafc, #e2e8f0, #cbd5e1);
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
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 0.8rem;
                margin-bottom: 1.2rem;
                border: 1px solid rgba(226, 232, 240, 0.8);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                position: relative;
                overflow: hidden;
            }

            /* Purple accent border/gradient at the top of header */
            .header-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #8b5cf6, #7c3aed, #6d28d9, #7c3aed, #8b5cf6);
                background-size: 200% 100%;
                animation: gradientShift 3s ease-in-out infinite;
            }

            @keyframes gradientShift {

                0%,
                100% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }
            }

            /* Purple accent for logo container */
            .logo-container {
                position: relative;
                display: inline-block;
            }

            /* Purple accent for title */
            .text-header {
                color: #334155;
                text-shadow: none;
                font-weight: 600;
                position: relative;
            }

            .text-header::after {
                content: '';
                position: absolute;
                bottom: -4px;
                left: 0;
                width: 0;
                height: 2px;
                background: linear-gradient(90deg, #8b5cf6, #7c3aed);
                transition: width 0.3s ease;
                border-radius: 1px;
            }

            .text-header:hover::after {
                width: 100%;
            }

            /* Purple glow effect untuk header saat hover */
            .header-section:hover {
                box-shadow: 0 4px 20px rgba(139, 92, 246, 0.15),
                    0 8px 40px rgba(139, 92, 246, 0.1);
                border-color: rgba(139, 92, 246, 0.3);
            }

            /* Dark mode for header accent */
            [data-bs-theme="dark"] .header-section {
                background: rgba(30, 41, 59, 0.95);
                border: 1px solid rgba(139, 92, 246, 0.3);
            }

            [data-bs-theme="dark"] .header-section::before {
                background: linear-gradient(90deg, #a78bfa, #8b5cf6, #7c3aed, #8b5cf6, #a78bfa);
                background-size: 200% 100%;
            }

            [data-bs-theme="dark"] .text-header {
                color: #e2e8f0;
            }

            [data-bs-theme="dark"] .header-section:hover {
                box-shadow: 0 4px 20px rgba(139, 92, 246, 0.25),
                    0 8px 40px rgba(139, 92, 246, 0.15);
                border-color: rgba(139, 92, 246, 0.5);
            }

            [data-bs-theme="dark"] .text-center.p-2.bg-light {
                background: #212429 !important;
                color: #cbd5e1 !important;
                border: 1px solid rgba(71, 85, 105, 0.2);
            }

            .no-image-soft,
            .no-notes-soft {
                background: rgba(255, 255, 255, 0.171) !important;
                color: #b0b4bb !important;
                border: none !important;
                font-size: 0.97rem;
                padding: 16px 0 10px 0;
                box-shadow: none !important;
            }

            .no-image-soft i,
            .no-notes-soft i {
                font-size: 1.3rem !important;
                color: #b0b4bb !important;
                opacity: 0.7;
            }

            .no-image-soft p,
            .no-notes-soft p {
                color: #b0b4bb !important;
                font-size: 0.97rem;
                margin: 0;
                opacity: 0.85;
            }

            [data-bs-theme="dark"] .no-image-soft,
            [data-bs-theme="dark"] .no-notes-soft {
                background: rgba(36, 39, 44, 0.082) !important;
                color: #6c7383 !important;
            }

            [data-bs-theme="dark"] .no-image-soft i,
            [data-bs-theme="dark"] .no-notes-soft i {
                color: #6c7383 !important;
            }

            [data-bs-theme="dark"] .no-image-soft p,
            [data-bs-theme="dark"] .no-notes-soft p {
                color: #6c7383 !important;
            }

            /* Search section improvements */
            .search-section {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 1rem;
                margin-bottom: 1.2rem;
                border: 1px solid rgba(226, 232, 240, 0.8);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }

            /* Filter buttons improvements */
            .filters-section {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 1rem;
                margin-bottom: 1.2rem;
                border: 1px solid rgba(226, 232, 240, 0.8);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
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

            /* Reset button styling */
            .btn-outline-secondary {
                border: 1px solid #94a3b8;
                color: #64748b;
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(5px);
                transition: all 0.3s ease;
                font-weight: 300;
                padding: 0.4rem 0.675rem;
                border-radius: 0.5rem;
                font-size: 0.7rem;
            }

            .btn-outline-secondary:hover {
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                border-color: #8b5cf6;
                color: white;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
            }

            .btn-outline-secondary:active {
                transform: translateY(0);
                box-shadow: 0 2px 4px rgba(139, 92, 246, 0.2);
            }

            .filter-divider {
                width: 2px;
                height: 30px;
                background: rgba(148, 163, 184, 0.5);
                margin: 0 0.5rem;
            }

            footer {
                margin-top: auto;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                text-align: center;
                padding: 1rem;
                color: #475569;
                border-top: 1px solid rgba(226, 232, 240, 0.8);
            }

            /* Purple accents for small elements */
            .purple-accent {
                background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
                color: white !important;
            }

            .purple-border {
                border-color: #8b5cf6 !important;
            }

            .purple-text {
                color: #7c3aed !important;
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
                /* Brighter than default */
                border-color: #adb5bd !important;
                color: #fff !important;
            }

            .btn-danger.active,
            .btn-danger:active {
                background-color: #EF4444 !important;
                /* Brighter than default */
                border-color: #EF4444 !important;
                color: #fff !important;
                box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.3) !important;
            }

            .btn-success.active,
            .btn-success:active {
                background-color: #28A745 !important;
                /* Brighter than default */
                border-color: #28A745 !important;
                color: #fff !important;
                box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.3) !important;
            }

            .btn-info.active,
            .btn-info:active {
                background-color: #0DCAF0 !important;
                /* Brighter than default */
                border-color: #0DCAF0 !important;
                box-shadow: 0 0 0 3px rgba(13, 202, 240, 0.3) !important;
            }

            .btn-warning.active,
            .btn-warning:active {
                background-color: #FFCA2C !important;
                /* Brighter than default */
                border-color: #FFCA2C !important;
                color: #000 !important;
                /* Black text for good contrast */
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
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                border-color: #8b5cf6;
                color: #fff;
                font-weight: 500;
                border-radius: 25px;
                transition: all 0.3s ease;
            }

            .btn-primary-custom:hover {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border-color: #7c3aed;
                color: #fff;
                box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
            }

            .btn.bg-danger-subtle.active {
                background-color: #f8d7da !important;
                color: #721c24 !important;
                font-weight: 600 !important;
                outline: 2px solid #dc3545 !important;
                /* Add outline */
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
                /* Remove hover effect for all buttons in search-filter area */
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
                border: 2px solid rgba(139, 92, 246, 0.3);
                overflow: hidden;
                transition: all 0.3s ease;
                box-shadow: 0 8px 32px rgba(139, 92, 246, 0.08);
                position: relative;
            }

            .card:hover {
                transform: scale(1.06);
                box-shadow: 0 20px 40px rgba(139, 92, 246, 0.2);
                border-color: rgba(139, 92, 246, 0.8);
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
                padding: 0.5rem;
                background: rgba(255, 255, 255, 0.9);
                color: var(--bs-body-color);
            }

            /* Purple accent untuk project title */
            .card-body strong {
                color: var(--bs-body-color);
                font-size: 16px;
                position: relative;
                transition: color 0.3s ease;
            }

            .card-body strong:hover {
                color: #8b5cf6;
            }

            /* Card text elements */
            .card-body p {
                color: var(--bs-body-color);
                font-size: 14px;
                margin-top: 8px;
                line-height: 1.5;
            }

            /* Deadline text */
            .deadline {
                font-size: 13px;
                color: var(--bs-secondary-color);
                margin: 3px 0;
                font-weight: 500;
                display: inline-block;
            }

            /* Purple accent untuk material image container */
            .card:hover .material-container {
                border-color: rgba(139, 92, 246, 0.3) !important;
            }

            [data-bs-theme="dark"] .card:hover .material-container {
                border-color: rgba(139, 92, 246, 0.5) !important;
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
                background: linear-gradient(45deg, rgba(239, 68, 68, 0.6), #dc2626d7);
                color: white;
                padding: 4px 8px;
                border-radius: 15px;
                font-size: 11px;
                font-weight: 600;
                box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
                z-index: 5;
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% {
                    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
                }

                50% {
                    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.7), 0 0 0 4px rgba(239, 68, 68, 0.2);
                }

                100% {
                    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
                }
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

            p.text-center {
                color: #475569;
                font-size: 18px;
                margin-top: 2rem;
                padding: 2rem;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                border: 1px solid rgba(226, 232, 240, 0.8);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
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

                /* Reset button mobile styling */
                .btn-outline-secondary {
                    font-size: 0.8rem;
                    padding: 0.4rem 0.75rem;
                    min-width: 120px;
                }
            }

            /* Dark mode improvements */
            [data-bs-theme="dark"] .header-section,
            [data-bs-theme="dark"] .search-filter-combined {
                background: rgba(30, 41, 59, 0.95);
                border: 1px solid rgba(71, 85, 105, 0.5);
            }

            [data-bs-theme="dark"] .text-header {
                color: #e2e8f0;
            }

            [data-bs-theme="dark"] footer {
                background: rgba(30, 41, 59, 0.95);
                color: #cbd5e1;
                border-top: 1px solid rgba(71, 85, 105, 0.5);
            }

            [data-bs-theme="dark"] body {
                background: linear-gradient(135deg, #0f172a, #1e293b, #334155);
            }

            [data-bs-theme="dark"] .filter-divider {
                background: rgba(71, 85, 105, 0.5);
            }

            [data-bs-theme="dark"] p.text-center {
                background: rgba(30, 41, 59, 0.95);
                color: #cbd5e1;
                border: 1px solid rgba(71, 85, 105, 0.5);
            }

            [data-bs-theme="dark"] footer a {
                color: #a78bfa !important;
            }

            [data-bs-theme="dark"] .card {
                background: rgba(33, 37, 41, 0.95);
                border: 2px solid rgba(139, 92, 246, 0.4);
                box-shadow: 0 8px 32px rgba(139, 92, 246, 0.15);
            }

            [data-bs-theme="dark"] .card:hover {
                border-color: rgba(139, 92, 246, 0.8);
                box-shadow: 0 20px 40px rgba(139, 92, 246, 0.3);
            }

            [data-bs-theme="dark"] .card-body {
                background: rgba(33, 37, 41, 0.9);
                color: #fff !important;
            }

            [data-bs-theme="dark"] .card-body strong:hover {
                color: #a78bfa;
            }

            [data-bs-theme="dark"] .card-body strong {
                color: #fff !important;
            }

            [data-bs-theme="dark"] .card-body p {
                color: #e9ecef;
            }

            [data-bs-theme="dark"] .deadline {
                color: #ced4da !important;
            }

            [data-bs-theme="dark"] .card-body .deadline {
                color: #ced4da !important;
            }

            [data-bs-theme="dark"] .form-control,
            [data-bs-theme="dark"] .form-select {
                background: rgba(33, 37, 41, 0.9) !important;
                color: #fff !important;
                border-color: rgba(255, 255, 255, 0.2) !important;
            }

            [data-bs-theme="dark"] .form-control:focus,
            [data-bs-theme="dark"] .form-select:focus {
                background: rgba(33, 37, 41, 1) !important;
                color: #fff !important;
                border-color: #8b5cf6 !important;
                box-shadow: 0 0 20px rgba(139, 92, 246, 0.3) !important;
            }

            [data-bs-theme="dark"] .form-control::placeholder {
                color: #adb5bd !important;
            }

            /* Ensure text in inputs is always visible */
            .form-control {
                color: var(--bs-body-color) !important;
            }

            .form-select {
                color: var(--bs-body-color) !important;
            }

            [data-bs-theme="dark"] .input-group-text {
                background: rgba(139, 92, 246, 0.9) !important;
                color: #fff !important;
                border-color: rgba(139, 92, 246, 0.6) !important;
            }

            /* Dark mode untuk live search */
            [data-bs-theme="dark"] #searchInput:focus {
                box-shadow: 0 0 20px rgba(139, 92, 246, 0.5) !important;
                border-color: #a78bfa !important;
            }

            [data-bs-theme="dark"] #searchInput.typing {
                border-color: #f39c12 !important;
                box-shadow: 0 0 15px rgba(243, 156, 18, 0.4) !important;
            }

            /* Dark mode untuk no results */
            [data-bs-theme="dark"] #noResults .alert {
                background: linear-gradient(135deg, rgba(255, 193, 7, 0.2), rgba(255, 193, 7, 0.1)) !important;
                border: 1px solid rgba(255, 193, 7, 0.5) !important;
                color: #fbbf24 !important;
            }

            [data-bs-theme="dark"] .project-card.search-highlight {
                border-color: rgba(139, 92, 246, 0.8) !important;
                box-shadow: 0 8px 32px rgba(139, 92, 246, 0.3) !important;
            }

            /* Form improvements */
            .form-control,
            .form-select {
                border-radius: 25px;
                border: 2px solid rgba(226, 232, 240, 0.8);
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                transition: all 0.3s ease;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #8b5cf6;
                box-shadow: 0 0 20px rgba(139, 92, 246, 0.2);
                background: rgba(255, 255, 255, 1);
            }

            /* Live search enhancements */
            #searchInput {
                transition: all 0.3s ease;
            }

            #searchInput:focus {
                box-shadow: 0 0 20px rgba(139, 92, 246, 0.4);
                border-color: #8b5cf6;
            }

            /* Search button loading state */
            .btn-primary-custom {
                transition: all 0.3s ease;
            }

            .btn-primary-custom .bi-hourglass-split {
                animation: searchLoading 1s infinite ease-in-out;
            }

            @keyframes searchLoading {

                0%,
                100% {
                    transform: rotate(0deg) scale(1);
                }

                50% {
                    transform: rotate(180deg) scale(1.1);
                }
            }

            /* Search input typing indicator */
            #searchInput.typing {
                border-color: #ffc107;
                box-shadow: 0 0 15px rgba(255, 193, 7, 0.3);
            }

            /* No results styling */
            #noResults .alert {
                background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));
                border: 1px solid rgba(255, 193, 7, 0.3);
                color: #f59e0b;
                border-radius: 15px;
                backdrop-filter: blur(10px);
            }

            /* Animation untuk project cards */
            .project-card {
                transition: all 0.3s ease;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Highlight search matches */
            .project-card.search-highlight {
                border-color: rgba(139, 92, 246, 0.6) !important;
                box-shadow: 0 8px 32px rgba(139, 92, 246, 0.2) !important;
            }

            /* Combined search and filter section */
            .search-filter-combined {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 1rem;
                margin-bottom: 1.2rem;
                border: 1px solid rgba(226, 232, 240, 0.8);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }

            /* Dark mode toggle button improvements */
            #toggleDarkMode {
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
                border: 0.5px solid #6c757d;
                background: rgba(255, 255, 255, 0.95);
                color: #6c757d;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            #toggleDarkMode:hover {
                background: #6c757d;
                color: white;
                border-color: #6c757d;
                transform: scale(1.05);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }

            [data-bs-theme="dark"] #toggleDarkMode {
                border-color: #8b5cf6;
                background: rgba(139, 92, 246, 0.2);
                color: #a78bfa;
            }

            [data-bs-theme="dark"] #toggleDarkMode:hover {
                background: #8b5cf6;
                color: white;
                border-color: #8b5cf6;
            }

            /* Fancybox Custom Styling */
            .fancybox__backdrop {
                background: rgba(0, 0, 0, 0.9) !important;
            }

            .fancybox__button {
                color: #8b5cf6 !important;
                transition: all 0.3s ease !important;
                border-radius: 8px !important;
            }

            .fancybox__button:hover {
                background: rgba(139, 92, 246, 0.2) !important;
                transform: scale(1.1) !important;
            }

            .fancybox__infobar {
                color: #8b5cf6 !important;
                font-weight: 600 !important;
                font-size: 14px !important;
            }

            .fancybox__content {
                border-radius: 5px !important;
                overflow: hidden !important;
            }

            .fancybox__caption {
                font-weight: 600 !important;
                text-align: center !important;
                border-radius: 10px !important;
                padding: 5px 10px !important;
                backdrop-filter: blur(10px) !important;
            }

            /* Dark mode untuk Fancybox */
            [data-bs-theme="dark"] .fancybox__button {
                color: #a78bfa !important;
            }

            [data-bs-theme="dark"] .fancybox__button:hover {
                background: rgba(167, 139, 250, 0.2) !important;
            }

            [data-bs-theme="dark"] .fancybox__infobar,
            [data-bs-theme="dark"] .fancybox__caption {
                color: #a78bfa !important;
            }

            /* Gallery View Buttons Styling */
            .btn-outline-purple {
                color: #8b5cf6 !important;
                border-color: #8b5cf6 !important;
                background: transparent !important;
                border-radius: 8px !important;
                transition: all 0.3s ease !important;
                padding: 0.375rem 0.5rem !important;
                font-size: 0.875rem !important;
            }

            .btn-outline-purple:hover {
                background-color: #8b5cf6 !important;
                border-color: #8b5cf6 !important;
                color: white !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3) !important;
            }

            .btn-outline-purple:focus {
                box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25) !important;
            }

            /* Gallery actions container */
            .gallery-actions .btn {
                width: 36px !important;
                height: 36px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 0 !important;
            }

            /* Vertical divider */
            .vr {
                width: 1px !important;
                background: rgba(139, 92, 246, 0.3) !important;
                opacity: 1 !important;
            }

            /* Dark mode untuk gallery buttons */
            [data-bs-theme="dark"] .btn-outline-purple {
                color: #a78bfa !important;
                border-color: #a78bfa !important;
            }

            [data-bs-theme="dark"] .btn-outline-purple:hover {
                background-color: #a78bfa !important;
                border-color: #a78bfa !important;
                color: white !important;
            }

            [data-bs-theme="dark"] .vr {
                background: rgba(167, 139, 250, 0.4) !important;
            }

            /* Responsive behavior untuk gallery buttons */
            @media (max-width: 992px) {
                .gallery-actions .btn {
                    width: 32px !important;
                    height: 32px !important;
                    font-size: 0.8rem !important;
                }

                .vr {
                    height: 24px !important;
                }
            }

            @media (max-width: 768px) {
                .gallery-actions {
                    order: -1;
                    /* Pindah ke kiri pada mobile */
                    margin-right: auto;
                }

                .gallery-actions .btn {
                    width: 30px !important;
                    height: 30px !important;
                    font-size: 0.75rem !important;
                }

                .vr {
                    height: 20px !important;
                    margin: 0 8px !important;
                }
            }

            @media (max-width: 576px) {
                .header-section .d-flex {
                    flex-wrap: wrap !important;
                }

                .gallery-actions {
                    order: 1;
                    margin-top: 10px;
                    width: 100%;
                    justify-content: center !important;
                }

                .vr {
                    display: none !important;
                }

                .text-header {
                    font-size: 1.25rem !important;
                }
            }

            .btn-group .btn {
                border-radius: 25px !important;
                margin: 0 5px;
                transition: all 0.3s ease;
                font-weight: 500;
            }

            .btn-group .btn:first-child {
                margin-left: 0;
            }

            .btn-group .btn:last-child {
                margin-right: 0;
            }

            .btn-outline-primary:hover {
                background-color: #8b5cf6;
                border-color: #8b5cf6;
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
            }

            .btn-outline-secondary:hover {
                background-color: #6c757d;
                border-color: #6c757d;
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
            }

            /* Dark mode untuk gallery buttons */
            [data-bs-theme="dark"] .btn-outline-primary {
                color: #a78bfa;
                border-color: #a78bfa;
            }

            [data-bs-theme="dark"] .btn-outline-primary:hover {
                background-color: #a78bfa;
                border-color: #a78bfa;
                color: white;
            }
        </style>
    </head>

    <body>
        <div class="container-fluid">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <!-- Logo dengan purple accent -->
                        <div class="logo-container me-3">
                            <a href="index.php">
                                <img src="uploads/me.png" alt="ME Logo"
                                    style="width: 50px; height: 50px; border-radius: 50%; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3); border: 2px solid rgba(139, 92, 246, 0.2);">
                            </a>
                        </div>
                        <h3 class="fw-bold text-header mb-0">Mascot Project List</h3>
                    </div>

                    <!-- Gallery View Buttons - Compact -->
                    <div class="d-flex align-items-center gap-2">
                        <?php if (!empty($projects)): ?>
                        <div class="gallery-actions d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-purple"
                                onclick="viewAllProjectImages()"
                                title="View All Project Images (<?= count($projects) ?> projects)"
                                data-bs-toggle="tooltip">
                                <i class="bi bi-images"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-purple"
                                onclick="viewAllMaterialImages()"
                                title="View All Submission Notes (<?= count($projects) ?> notes)"
                                data-bs-toggle="tooltip">
                                <i class="bi bi-file-earmark-image"></i>
                            </button>
                        </div>
                        <div class="vr mx-2" style="height: 30px;"></div>
                        <?php endif; ?>

                        <!-- Tombol Login atau Dashboard -->
                        <button id="toggleDarkMode" class="btn btn-outline-secondary">
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
                        <!-- Real-time search input tanpa form -->
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control"
                                placeholder="Search Project..." value="<?= htmlspecialchars($search) ?>">
                            <span class="input-group-text btn-primary-custom">
                                <i class="bi bi-search"></i>
                            </span>
                        </div>
                        <!-- Hidden form untuk maintain URL parameters saat menggunakan filter buttons -->
                        <form method="GET" action="mascot_index.php" id="filterForm" style="display: none;">
                            <input type="hidden" name="project_status" id="hiddenStatus"
                                value="<?= htmlspecialchars($_GET['project_status'] ?? '') ?>">
                            <input type="hidden" name="priority" id="hiddenPriority"
                                value="<?= htmlspecialchars($_GET['priority'] ?? '') ?>">
                            <input type="hidden" name="this_week" id="hiddenThisWeek"
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
                                </form>
                            </div>



                            <div class="filter-group">
                                <form method="GET" action="mascot_index.php" class="d-flex align-items-center">
                                    <div class="input-group">
                                        <span class="input-group-text" id="priorityDropdownBtn"
                                            style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; border-color: #8b5cf6; cursor:pointer;">
                                            <i class="bi bi-funnel"></i>
                                        </span>
                                        <select name="priority" class="form-select border-start-0"
                                            id="prioritySelect" onchange="this.form.submit()">
                                            <option value="">All Priority</option>
                                            <option value="Urgent"
                                                <?= isset($_GET['priority']) && $_GET['priority'] === 'Urgent' ? 'selected' : '' ?>>
                                                🔴 Urgent</option>
                                            <option value="High"
                                                <?= isset($_GET['priority']) && $_GET['priority'] === 'High' ? 'selected' : '' ?>>
                                                🟡 High</option>
                                            <option value="Normal"
                                                <?= isset($_GET['priority']) && $_GET['priority'] === 'Normal' ? 'selected' : '' ?>>
                                                🔵 Normal</option>
                                            <option value="Low"
                                                <?= isset($_GET['priority']) && $_GET['priority'] === 'Low' ? 'selected' : '' ?>>
                                                ⚪ Low</option>
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

                            <div class="filter-group">
                                <a href="mascot_index.php" class="btn btn-outline-secondary"
                                    title="Reset all filters">
                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="text-center" style="display: none;">
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-search me-2"></i>
                    <strong>No results found</strong><br>
                    <small>Try using different keywords or clear the search to see all projects.</small>
                </div>
            </div>

            <div class="card-grid">
                <?php if (empty($projects)): ?>
                <p class="text-center">No projects found for the selected filters.</p>
                <?php else: ?>
                <?php foreach ($projects as $index => $row): ?>
                <div class="card project-card"
                    data-project-name="<?= strtolower(htmlspecialchars($row['project_name'])) ?>"
                    data-description="<?= strtolower(htmlspecialchars($row['description'])) ?>"
                    data-status="<?= strtolower(htmlspecialchars($row['project_status'])) ?>"
                    data-priority="<?= strtolower(htmlspecialchars($row['priority'])) ?>">
                    <?php if (isThisWeek($row['deadline'])): ?>
                    <div class="this-week-badge">
                        <i class="bi bi-calendar-event me-1"></i>This Week!
                    </div>
                    <?php endif; ?>

                    <?php 
                    // Handle multiple project images
                    $projectImages = [];
                    if (!empty($row['project_image'])) {
                        $decoded = json_decode($row['project_image'], true);
                        $projectImages = is_array($decoded) ? $decoded : [$row['project_image']];
                    }
                    
                    if (!empty($projectImages)): ?>
                    <div class="position-relative">
                        <?php foreach ($projectImages as $imgIndex => $image): ?>
                        <a href="uploads/projects/<?= htmlspecialchars($image) ?>"
                            data-fancybox="gallery-project-<?= $row['id'] ?>"
                            data-caption="<?= htmlspecialchars($row['project_name']) ?> - Image <?= $imgIndex + 1 ?>"
                            <?= $imgIndex === 0 ? '' : 'style="display:none;"' ?>>
                            <?php if ($imgIndex === 0): ?>
                            <img src="uploads/projects/<?= htmlspecialchars($image) ?>" style="cursor: pointer;"
                                alt="No Image Project yet">
                            <?php endif; ?>
                        </a>
                        <?php endforeach; ?>

                        <?php if (count($projectImages) > 1): ?>
                        <div class="position-absolute bottom-0 start-0 m-1">
                            <span class="badge bg-dark bg-opacity-25 text-white"
                                style="font-size: 0.65rem; padding: 2px 6px;">
                                <i class="bi bi-images" style="font-size: 0.7rem;"></i> <?= count($projectImages) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center p-2 no-image-soft">
                        <i class="bi bi-image"></i>
                        <p>No Image Available</p>
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <?php if (!empty($row['subform_embed'])): ?>
                        <strong style="cursor: pointer; font-size: 1.1rem; margin-bottom: 0.3rem; display: block;"
                            onclick="openGoogleSlideModal('<?= htmlspecialchars($row['subform_embed']) ?>')">
                            <i class="bi bi-file-earmark-slides me-1"></i><?= htmlspecialchars($row['project_name']) ?>
                        </strong>
                        <?php else: ?>
                        <strong style="font-size: 1.1rem; margin-bottom: 0.3rem; display: block;">
                            <?= htmlspecialchars($row['project_name']) ?>
                        </strong>
                        <?php endif; ?>

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

                        <!-- Material image dengan purple accent container -->
                        <div class="material-container"
                            style="margin-top: 8px; border-radius: 10px; overflow: hidden; border: 1px solid rgba(139, 92, 246, 0.1); transition: border-color 0.3s ease;">

                            <?php 
                            // Handle multiple material images
                            $materialImages = [];
                            if (!empty($row['material_image'])) {
                                $decoded = json_decode($row['material_image'], true);
                                $materialImages = is_array($decoded) ? $decoded : [$row['material_image']];
                            }
                            
                            if (!empty($materialImages)): ?>
                            <div class="position-relative">
                                <?php foreach ($materialImages as $imgIndex => $image): ?>
                                <a href="uploads/materials/<?= htmlspecialchars($image) ?>"
                                    data-fancybox="gallery-material-<?= $row['id'] ?>"
                                    data-caption="<?= htmlspecialchars($row['project_name']) ?> - Material <?= $imgIndex + 1 ?>"
                                    <?= $imgIndex === 0 ? '' : 'style="display:none;"' ?>>
                                    <?php if ($imgIndex === 0): ?>
                                    <img src="uploads/materials/<?= htmlspecialchars($image) ?>"
                                        alt="No Submission Notes yet"
                                        style="width: 100%; height: 150px; object-fit: contain; background-color: #f8f9fa; cursor: pointer;">
                                    <?php endif; ?>
                                </a>
                                <?php endforeach; ?>

                                <?php if (count($materialImages) > 1): ?>
                                <div class="position-absolute bottom-0 start-0 m-1">
                                    <span class="badge bg-dark bg-opacity-25 text-white"
                                        style="font-size: 0.65rem; padding: 2px 6px;">
                                        <i class="bi bi-images" style="font-size: 0.7rem;"></i>
                                        <?= count($materialImages) ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="text-center p-2 no-notes-soft">
                                <i class="bi bi-file-earmark text-muted"></i>
                                <p>No Submission Notes yet</p>
                            </div>
                            <?php endif; ?>
                        </div>

                        <p style="margin: 6px 0 0 0; font-size: 14px; line-height: 1.4;">
                            <?= nl2br(htmlspecialchars($row['description'])) ?>
                        </p>

                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Hidden links for gallery view all -->
            <?php if (!empty($projects)): ?>
            <div id="hiddenGalleryLinks" style="display: none;">
                <!-- All project images -->
                <?php foreach ($projects as $index => $row): ?>
                <?php 
                    $projectImages = parseImageData($row['project_image']);
                    foreach ($projectImages as $imgIndex => $image): ?>
                <a href="uploads/projects/<?= htmlspecialchars($image) ?>" data-fancybox="all-projects"
                    data-caption="<?= htmlspecialchars($row['project_name']) ?> - Project Image <?= $imgIndex + 1 ?>"></a>
                <?php endforeach; ?>
                <?php endforeach; ?>

                <!-- All material images -->
                <?php foreach ($projects as $index => $row): ?>
                <?php 
                    $materialImages = parseImageData($row['material_image']);
                    foreach ($materialImages as $imgIndex => $image): ?>
                <a href="uploads/materials/<?= htmlspecialchars($image) ?>" data-fancybox="all-materials"
                    data-caption="<?= htmlspecialchars($row['project_name']) ?> - Material <?= $imgIndex + 1 ?>"></a>
                <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <footer class="text-center py-1">
            <div class="mb-0">Create with ❤️ by <a class="fw-bold" href=""
                    style="text-decoration: none; color: #8b5cf6;">IT DCM</a></div>
        </footer>

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
            function openGoogleSlideModal(embedLink) {
                function isWebOS() {
                    const userAgent = navigator.userAgent.toLowerCase();
                    return userAgent.includes("webos") || userAgent.includes("smarttv");
                }

                // If not WebOS, display embed iframe
                const embedUrl = embedLink.replace('/edit', '/embed');
                const iframe = document.getElementById('googleSlideIframe');
                const fallbackLink = document.getElementById('fallbackLink');
                const googleSlideLink = document.getElementById('googleSlideLink');

                iframe.src = embedUrl;
                googleSlideLink.href = embedLink;

                // Check if iframe is supported
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

            // Check dark mode preference from localStorage
            if (localStorage.getItem('theme') === 'dark') {
                html.setAttribute('data-bs-theme', 'dark');
                toggleDarkMode.innerHTML = '<i class="bi bi-sun"></i>';
            }

            // Add event listener for toggle button
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
        <!-- Fancybox JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
        <script>
            // Initialize Fancybox
            Fancybox.bind("[data-fancybox]", {
                // Configuration options
                Toolbar: {
                    display: {
                        left: ["infobar"],
                        middle: [
                            "zoomIn",
                            "zoomOut",
                            "toggle1to1",
                            "rotateCCW",
                            "rotateCW",
                            "flipX",
                            "flipY",
                        ],
                        right: ["slideshow", "download", "thumbs", "close"],
                    },
                },
                Thumbs: {
                    autoStart: false,
                    showOnStart: false,
                    type: "classic",
                },
                Images: {
                    zoom: true,
                    protect: false,
                    Panzoom: {
                        maxScale: 3,
                        step: 0.5,
                    },
                },
                Carousel: {
                    infinite: true,
                    transition: "slide",
                    preload: 3,
                },
                Slideshow: {
                    autoStart: false,
                    speed: 3000,
                },
                // Animation and UI
                showClass: "f-fadeIn",
                hideClass: "f-fadeOut",
                animated: true,
                dragToClose: true,
                hideScrollbar: true,
                // Custom styling
                l10n: {
                    CLOSE: "Close",
                    NEXT: "Next",
                    PREV: "Previous",
                    MODAL: "Modal",
                    ERROR: "Image could not be loaded",
                    IMAGE_ERROR: "Image not found",
                    ELEMENT_NOT_FOUND: "HTML element not found",
                    AJAX_NOT_FOUND: "AJAX loading error: Not found",
                    AJAX_FORBIDDEN: "AJAX loading error: Forbidden",
                    IFRAME_ERROR: "Page loading error",
                    TOGGLE_ZOOM: "Toggle zoom level",
                    TOGGLE_THUMBS: "Toggle thumbnails",
                    TOGGLE_SLIDESHOW: "Toggle slideshow",
                    TOGGLE_FULLSCREEN: "Toggle fullscreen",
                    DOWNLOAD: "Download"
                },
                // Event callbacks
                on: {
                    "Carousel.ready": (fancybox) => {
                        // When carousel is ready
                        console.log("Fancybox gallery ready");
                    }
                }
            });

            // Tambahkan efek hover untuk link gambar
            document.addEventListener('DOMContentLoaded', function() {
                const imageLinks = document.querySelectorAll('[data-fancybox]');
                imageLinks.forEach(link => {
                    link.addEventListener('mouseenter', function() {
                        this.style.transform = 'scale(1.02)';
                        this.style.transition = 'transform 0.3s ease';
                    });
                    link.addEventListener('mouseleave', function() {
                        this.style.transform = 'scale(1)';
                    });
                });
            });

            // Function to view all project images
            function viewAllProjectImages() {
                const projectLinks = document.querySelectorAll('[data-fancybox="all-projects"]');
                if (projectLinks.length > 0) {
                    // Trigger fancybox on first link
                    projectLinks[0].click();
                } else {
                    alert('No project images available');
                }
            }

            // Function to view all material images
            function viewAllMaterialImages() {
                const materialLinks = document.querySelectorAll('[data-fancybox="all-materials"]');
                if (materialLinks.length > 0) {
                    // Trigger fancybox on first link
                    materialLinks[0].click();
                } else {
                    alert('No submission notes images available');
                }
            }

            // Initialize Bootstrap tooltips
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Live Search Functionality (like DataTables)
                const searchInput = document.getElementById('searchInput');
                const projectCards = document.querySelectorAll('.project-card');
                const cardGrid = document.querySelector('.card-grid');
                const noResults = document.getElementById('noResults');
                const originalNoProjectsMessage = document.querySelector('.text-center');

                let searchTimeout;

                // Debounce function for smooth performance
                function debounce(func, delay) {
                    return function(args) {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => func.apply(this, [args]), delay);
                    };
                }

                // Function to perform real-time filtering
                function performRealTimeFilter() {
                    const searchValue = searchInput.value.toLowerCase().trim();
                    let visibleCount = 0;
                    const totalCards = projectCards.length;

                    // Hide original "No projects found" message if exists
                    if (originalNoProjectsMessage && totalCards > 0) {
                        originalNoProjectsMessage.style.display = 'none';
                    }

                    projectCards.forEach(card => {
                        const projectName = card.getAttribute('data-project-name') || '';
                        const description = card.getAttribute('data-description') || '';
                        const status = card.getAttribute('data-status') || '';
                        const priority = card.getAttribute('data-priority') || '';

                        // Combine all searchable text
                        const searchableText = (projectName + ' ' + description + ' ' + status + ' ' + priority)
                            .toLowerCase();

                        const isMatch = searchValue === '' || searchableText.includes(searchValue);

                        if (isMatch) {
                            card.style.display = 'block';
                            card.style.animation = 'fadeInUp 0.3s ease forwards';
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    // Update search info and no results message
                    updateSearchResults(searchValue, visibleCount, totalCards);
                }

                // Function to update search results info
                function updateSearchResults(searchValue, visibleCount, totalCards) {
                    if (searchValue === '') {
                        // No search
                        noResults.style.display = 'none';
                    } else if (visibleCount === 0) {
                        // No results
                        noResults.style.display = 'block';
                        noResults.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    } else {
                        // Has results
                        noResults.style.display = 'none';
                    }
                }

                // Event listener for real-time search
                if (searchInput && projectCards.length > 0) {
                    const debouncedFilter = debounce(performRealTimeFilter, 150); // 150ms delay untuk responsiveness

                    searchInput.addEventListener('input', function(e) {
                        const searchButton = document.querySelector('.input-group-text');

                        // Visual feedback while typing
                        this.classList.add('typing');

                        if (this.value.length > 0) {
                            searchButton.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                            searchButton.style.opacity = '0.8';
                        } else {
                            searchButton.innerHTML = '<i class="bi bi-search"></i>';
                            searchButton.style.opacity = '1';
                        }

                        // Run filter with debounce
                        debouncedFilter();

                        // Remove typing class after delay
                        setTimeout(() => {
                            this.classList.remove('typing');
                            searchButton.innerHTML = '<i class="bi bi-search"></i>';
                            searchButton.style.opacity = '1';
                        }, 300);
                    });

                    // Run initial filter if there's search value from URL
                    if (searchInput.value.trim() !== '') {
                        performRealTimeFilter();
                    }

                    // Add interactive placeholder
                    const originalPlaceholder = searchInput.placeholder;
                    searchInput.addEventListener('focus', function() {
                        this.placeholder = 'Search Project...';
                    });

                    searchInput.addEventListener('blur', function() {
                        if (this.value === '') {
                            this.placeholder = originalPlaceholder;
                        }
                    });
                }
            });

            document.getElementById('priorityDropdownBtn').addEventListener('click', function() {
                document.getElementById('prioritySelect').focus();
                // Untuk browser modern, ini akan membuka dropdown jika user menekan tombol panah bawah
                // Untuk Chrome/Edge, bisa juga trigger event keydown:
                const select = document.getElementById('prioritySelect');
                const event = new KeyboardEvent('keydown', {
                    key: 'ArrowDown',
                    bubbles: true
                });
                select.dispatchEvent(event);
            });
        </script>
    </body>

</html>
