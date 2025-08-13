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

// Pagination settings
$itemsPerPage = 18; // Optimized: Menampilkan 18 item per halaman untuk performa lebih baik
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

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

$sql .= ' ORDER BY (deadline IS NULL), deadline ASC, createAt DESC LIMIT ' . $itemsPerPage . ' OFFSET ' . $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

// Count total projects for pagination
$countSql = "SELECT COUNT(*) FROM gallery WHERE category = 'mascot' AND project_status != 'archived' AND project_name LIKE ?";
$countParams = ["%$search%"];

if (isset($_GET['this_week']) && $_GET['this_week'] == '1') {
    $startOfWeekObj = new DateTime();
    $startOfWeekObj->modify('this week');
    $startOfWeek = $startOfWeekObj->format('Y-m-d');

    $endOfWeekObj = new DateTime();
    $endOfWeekObj->modify('this week +6 days');
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

// Calculate this week count (reuse variables from above if they exist)
if (!isset($startOfWeek) || !isset($endOfWeek)) {
    $startOfWeekObj = new DateTime();
    $startOfWeekObj->modify('this week');
    $startOfWeek = $startOfWeekObj->format('Y-m-d');

    $endOfWeekObj = new DateTime();
    $endOfWeekObj->modify('this week +6 days');
    $endOfWeek = $endOfWeekObj->format('Y-m-d');
}

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

// Count projects by status for category (optimized with GROUP BY)
$status_counts = [
    'Upcoming' => 0,
    'Completed' => 0,
    'In Progress' => 0,
    'Revision' => 0,
];

$sql = "SELECT project_status, COUNT(*) as count FROM gallery WHERE category = 'mascot' AND project_status IN ('Upcoming', 'Completed', 'In Progress', 'Revision')";
$params = [];

// Add priority filter if exists
if (!empty($_GET['priority'])) {
    $sql .= ' AND priority = ?';
    $params[] = $_GET['priority'];
}

$sql .= ' GROUP BY project_status';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

while ($row = $stmt->fetch()) {
    if (isset($status_counts[$row['project_status']])) {
        $status_counts[$row['project_status']] = $row['count'];
    }
}

// Count projects by priority for category (optimized with GROUP BY)
$priority_counts = [
    'Urgent' => 0,
    'High' => 0,
    'Normal' => 0,
    'Low' => 0,
];

$sql = "SELECT priority, COUNT(*) as count FROM gallery WHERE category = 'mascot' AND priority IN ('Urgent', 'High', 'Normal', 'Low') AND project_status != 'archived'";
$params = [];

// Add status filter if exists
if (!empty($_GET['project_status'])) {
    $sql .= ' AND project_status = ?';
    $params[] = $_GET['project_status'];
}

$sql .= ' GROUP BY priority';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

while ($row = $stmt->fetch()) {
    if (isset($priority_counts[$row['priority']])) {
        $priority_counts[$row['priority']] = $row['count'];
    }
}

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
                background: #f8fafc;
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

            /* Header improvements - Simplified */
            .header-section {
                background: #ffffff;
                border-radius: 15px;
                padding: 0.8rem;
                margin-bottom: 1.2rem;
                border: 1px solid #e2e8f0;
            }

            /* Purple accent border - Simplified */
            .header-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: #8b5cf6;
            }

            /* Purple accent for title - Simplified */
            .text-header {
                color: #334155;
                font-weight: 600;
            }

            /* Dark mode for header - Simplified */
            [data-bs-theme="dark"] .header-section {
                background: #1e293b;
                border: 1px solid #475569;
            }

            [data-bs-theme="dark"] .header-section::before {
                background: #a78bfa;
            }

            [data-bs-theme="dark"] .text-header {
                color: #e2e8f0;
            }

            [data-bs-theme="dark"] .text-center.p-2.bg-light {
                background: #212429 !important;
                color: #cbd5e1 !important;
                border: 1px solid #475569;
            }

            .no-image-soft {
                height: 180px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .no-notes-soft {
                height: 150px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .no-image-soft,
            .no-notes-soft {
                background: #f8f9fa !important;
                color: #6c757d !important;
                border: none !important;
                font-size: 0.97rem;
                padding: 16px 0 10px 0;
            }

            .no-image-soft i,
            .no-notes-soft i {
                font-size: 1.3rem !important;
                color: #6c757d !important;
            }

            .no-image-soft p,
            .no-notes-soft p {
                color: #6c757d !important;
                font-size: 0.97rem;
                margin: 0;
            }

            [data-bs-theme="dark"] .no-image-soft,
            [data-bs-theme="dark"] .no-notes-soft {
                background-color: #23272f !important;
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

            /* Search and Filter sections - Simplified */
            .search-section,
            .filters-section {
                background: #ffffff;
                border-radius: 15px;
                padding: 1rem;
                margin-bottom: 1.2rem;
                border: 1px solid #e2e8f0;
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

            /* Reset button styling - Simplified */
            .btn-outline-secondary {
                border: 1px solid #94a3b8;
                color: #64748b;
                background: #ffffff;
                font-weight: 400;
                padding: 0.4rem 0.675rem;
                border-radius: 0.5rem;
                font-size: 0.7rem;
            }

            .btn-outline-secondary:hover {
                background: #8b5cf6;
                border-color: #8b5cf6;
                color: white;
            }

            /* Reset button custom styling for mascot */
            .btn-reset-custom {
                background: linear-gradient(135deg, #f8fafc, #e2e8f0);
                color: #8b5cf6;
                font-weight: 600;
                padding: 0.5rem 1rem;
                border-radius: 25px;
                font-size: 0.875rem;
                transition: all 0.1s ease;
                display: flex;
                align-items: center;
                text-decoration: none;
            }

            .btn-reset-custom:hover {
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                border-color: #7c3aed;
                color: #ffffff;
                text-decoration: none;
            }

            .btn-reset-custom:focus {
                outline: none;
                box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
            }

            /* Dark mode untuk reset button */
            [data-bs-theme="dark"] .btn-reset-custom {
                background: linear-gradient(135deg, #374151, #4b5563);
                border-color: #a78bfa;
                color: #a78bfa;
            }

            [data-bs-theme="dark"] .btn-reset-custom:hover {
                background: linear-gradient(135deg, #a78bfa, #8b5cf6);
                border-color: #8b5cf6;
                color: #ffffff;
            }

            .filter-divider {
                width: 2px;
                height: 30px;
                background: #cbd5e1;
                margin: 0 0.3rem;
            }

            footer {
                margin-top: auto;
                background: #ffffff;
                text-align: center;
                padding: 1rem;
                color: #475569;
                border-top: 1px solid #e2e8f0;
                font-size: 0.8rem;
                font-weight: 400;
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

            /* Button improvements - Consolidated */
            .btn {
                border-radius: 25px;
                font-weight: 500;
                border: 1px solid transparent;
                transition: all 0.1s ease-in-out;
            }

            /* btn-secondary (All) */
            .btn-secondary {
                background: transparent;
                color: #6c757d;
                border: 1.5px solid #6c757d;
            }

            .btn-secondary:hover,
            .btn-secondary.active,
            .btn-secondary:active {
                background: #6c757d !important;
                color: #fff !important;
                border: 1.5px solid #6c757d !important;
            }

            [data-bs-theme="dark"] .btn-secondary {
                background: transparent;
                color: #a7adc3;
                border: 1.5px solid #a7adc3;
            }

            [data-bs-theme="dark"] .btn-secondary:hover,
            [data-bs-theme="dark"] .btn-secondary.active,
            [data-bs-theme="dark"] .btn-secondary:active {
                background: #a7adc3 !important;
                color: #23272f !important;
                border: 1.5px solid #a7adc3 !important;
            }

            /* btn-info (Upcoming) */
            .btn-info {
                background: transparent;
                color: #0dcaf0;
                border: 1.5px solid #0dcaf0;
            }

            .btn-info:hover,
            .btn-info.active,
            .btn-info:active {
                background: #0dcaf0 !important;
                color: #fff !important;
                border: 1.5px solid #0dcaf0 !important;
            }

            [data-bs-theme="dark"] .btn-info {
                background: transparent;
                color: #6dd5ed;
                border: 1.5px solid #6dd5ed;
            }

            [data-bs-theme="dark"] .btn-info:hover,
            [data-bs-theme="dark"] .btn-info.active,
            [data-bs-theme="dark"] .btn-info:active {
                background: #6dd5ed !important;
                color: #0a1e24 !important;
                border: 1.5px solid #6dd5ed !important;
            }

            /* btn-warning (In Progress) */
            .btn-warning {
                background: transparent;
                color: #fbbf24;
                border: 1.5px solid #fbbf24;
            }

            .btn-warning:hover,
            .btn-warning.active,
            .btn-warning:active {
                background: #fbbf24 !important;
                color: #fff !important;
                border: 1.5px solid #fbbf24 !important;
            }

            [data-bs-theme="dark"] .btn-warning {
                background: transparent;
                color: #ffd93d;
                border: 1.5px solid #ffd93d;
            }

            [data-bs-theme="dark"] .btn-warning:hover,
            [data-bs-theme="dark"] .btn-warning.active,
            [data-bs-theme="dark"] .btn-warning:active {
                background: #ffd93d !important;
                color: #1a1300 !important;
                border: 1.5px solid #ffd93d !important;
            }

            /* btn-success (Completed) */
            .btn-success {
                background: transparent;
                color: #198754;
                border: 1.5px solid #198754;
            }

            .btn-success:hover,
            .btn-success.active,
            .btn-success:active {
                background: #198754 !important;
                color: #fff !important;
                border: 1.5px solid #198754 !important;
            }

            [data-bs-theme="dark"] .btn-success {
                background: transparent;
                color: #20c997;
                border: 1.5px solid #20c997;
            }

            [data-bs-theme="dark"] .btn-success:hover,
            [data-bs-theme="dark"] .btn-success.active,
            [data-bs-theme="dark"] .btn-success:active {
                background: #20c997 !important;
                color: #0c1f1a !important;
                border: 1.5px solid #20c997 !important;
            }

            /* btn-indigo (Revision) */
            .btn-indigo {
                background: transparent;
                color: #fd7e14;
                border: 1.5px solid #fd7e14;
            }

            .btn-indigo:hover,
            .btn-indigo.active,
            .btn-indigo:active {
                background: #fd7e14 !important;
                color: #fff !important;
                border: 1.5px solid #fd7e14 !important;
            }

            [data-bs-theme="dark"] .btn-indigo {
                background: transparent;
                color: #ff922b;
                border: 1.5px solid #ff922b;
            }

            [data-bs-theme="dark"] .btn-indigo:hover,
            [data-bs-theme="dark"] .btn-indigo.active,
            [data-bs-theme="dark"] .btn-indigo:active {
                background: #ff922b !important;
                color: #1a0d02 !important;
                border: 1.5px solid #ff922b !important;
            }

            /* This Week button - outline saat tidak aktif */
            .btn.bg-danger-subtle {
                background: transparent !important;
                color: #b02a37 !important;
                border: 1.5px solid #dc3545 !important;
                font-weight: 600 !important;
            }

            /* Fill saat hover/active */
            .btn.bg-danger-subtle:hover,
            .btn.bg-danger-subtle.active,
            .btn.bg-danger-subtle:active {
                background-color: #f8d7da !important;
                color: #b02a37 !important;
                border: 1.5px solid #920000 !important;
            }

            /* Dark mode */
            [data-bs-theme="dark"] .btn.bg-danger-subtle {
                background: transparent !important;
                color: #f8d7da !important;
                border: 1.5px solid #ff6b7a !important;
            }

            [data-bs-theme="dark"] .btn.bg-danger-subtle:hover,
            [data-bs-theme="dark"] .btn.bg-danger-subtle.active,
            [data-bs-theme="dark"] .btn.bg-danger-subtle:active {
                background-color: #58151c !important;
                color: #f8d7da !important;
                border: 1.5px solid #ff6b7a !important;
            }

            .btn-primary-custom {
                background: #8b5cf6;
                border-color: #8b5cf6;
                color: #fff;
                font-weight: 500;
                border-radius: 25px;
            }

            .btn-primary-custom:hover {
                background: #7c3aed;
                border-color: #7c3aed;
                color: #fff;
            }

            .input-group .btn:hover {
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
                background: #ffffff;
                border-radius: 15px;
                border: 1px solid #e2e8f0;
                overflow: hidden;
                position: relative;
            }

            .card:hover {
                border: 1px solid #8b5cf6;
                transform: scale(1.05) !important;
                z-index: 10;
            }

            /* Simple red accent for urgent priority cards */
            .card.priority-urgent::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: rgba(220, 53, 70, 0.7);
                z-index: 2;
            }

            /* Simple red accent for this week deadline cards */
            .card.deadline-this-week::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: rgba(114, 28, 37, 0.7);
                z-index: 2;
            }

            .card.deadline-this-week .deadline-date {
                color: #dc3545 !important;
                font-weight: bold;
                background: rgba(220, 53, 70, 0.08);
                padding: 2px 4px;
                border-radius: 6px;
            }

            /* Both conditions - corner accent */
            .card.priority-urgent.deadline-this-week::before {
                background: linear-gradient(90deg, rgba(220, 53, 70, 0.7) 0%, rgba(114, 28, 37, 0.7) 100%);
            }

            .card.priority-urgent.deadline-this-week::after {
                display: none;
            }

            .card img {
                width: 100%;
                height: 180px;
                object-fit: contain;
                background-color: #f8f9fa;
            }

            .card-body {
                padding: 0.5rem;
                background: #ffffff;
                color: var(--bs-body-color);
            }

            /* Project title - Simplified */
            .card-body strong {
                color: var(--bs-body-color);
                font-size: 16px;
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
                display: inline-block;
            }

            .quantity {
                font-size: 13px;
                color: var(--bs-secondary-color);
                margin: 3px 0;
                display: inline-block;
            }

            .deadline-date,
            .quantity-number {
                font-weight: bold;
            }

            .deadline span:first-child,
            .quantity span:first-child {
                font-weight: 500;
            }

            /* Purple accent untuk material image container */
            .card:hover strong {
                color: #8b5cf6 !important;
            }

            [data-bs-theme="dark"] .card:hover strong {
                color: #b190fd !important;
            }

            /* Real-time Clock Styling - Compact button-like design */
            .realtime-clock-btn {
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: center;
                padding: 0.375rem 0.75rem;
                background: rgba(139, 92, 246, 0.1);
                border: 1px solid rgba(139, 92, 246, 0.3);
                border-radius: 8px;
                font-size: 0.75rem;
                font-weight: 600;
                color: #8b5cf6;
                min-width: 120px;
                height: 36px;
                text-align: center;
                line-height: 1.1;
                gap: 0.25rem;
                white-space: nowrap;
            }

            /* Pastikan semua child elements memiliki font-size yang sama */
            .realtime-clock-btn span,
            .realtime-clock-btn .date-small {
                font-size: 0.75rem !important;
                font-weight: 600;
                line-height: 1.1;
            }

            .date-small {
                color: #94a3b8;
            }

            /* Dark mode untuk compact clock */
            [data-bs-theme="dark"] .realtime-clock-btn {
                background: rgba(139, 92, 246, 0.15);
                border: 1px solid rgba(139, 92, 246, 0.4);
                color: #c4b5fd;
            }

            [data-bs-theme="dark"] .date-small {
                color: #94a3b8;
            }

            /* Pagination styling */
            .pagination .page-link {
                color: #8b5cf6;
                border-color: #e2e8f0;
                background-color: #ffffff;
            }

            .pagination .page-link:hover {
                color: #7c3aed;
                background-color: #f8fafc;
                border-color: #8b5cf6;
            }

            .pagination .page-item.active .page-link {
                background-color: #8b5cf6;
                border-color: #8b5cf6;
                color: #ffffff;
            }

            .pagination .page-item.disabled .page-link {
                color: #94a3b8;
                background-color: #f8fafc;
                border-color: #e2e8f0;
            }

            [data-bs-theme="dark"] .pagination .page-link {
                color: #a78bfa;
                border-color: #374151;
                background-color: #1f2937;
            }

            [data-bs-theme="dark"] .pagination .page-link:hover {
                color: #c4b5fd;
                background-color: #374151;
                border-color: #a78bfa;
            }

            [data-bs-theme="dark"] .pagination .page-item.active .page-link {
                background-color: #8b5cf6;
                border-color: #8b5cf6;
                color: #ffffff;
            }

            /* Performance optimized animations - Remove heavy effects */
            .btn {
                transition: color 0.1s ease-in-out, background-color 0.1s ease-in-out, border-color 0.1s ease-in-out;
            }

            .card {
                transition: border-color 0.1s ease-in-out;
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
                background: rgba(248, 215, 218, 0.7);
                color: #b02a37;
                border: 1px solid rgba(220, 53, 69, 0.5);
                padding: 3px 6px;
                border-radius: 15px;
                font-size: 11px;
                font-weight: 600;
                z-index: 5;
            }

            [data-bs-theme="dark"] .this-week-badge {
                background: rgba(44, 11, 14, 0.7);
                color: #ea868f;
                border: 1px solid rgba(132, 32, 41, 0.5);
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

            .status-badge {
                padding: 4px 8px;
                border-radius: 5px;
                color: white;
                font-weight: bold;
                display: inline-block;
            }

            p.text-center {
                color: #475569;
                font-size: 18px;
                margin-top: 2rem;
                padding: 2rem;
                background: rgba(255, 255, 255, 0.95);
                border-radius: 15px;
                border: 1px solid rgba(226, 232, 240, 0.8);
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

                .header-section .d-flex {
                    flex-direction: column;
                    gap: 0.75rem;
                }

                .realtime-clock-btn {
                    font-size: 0.7rem;
                    min-width: 70px;
                    height: 32px;
                }

                .realtime-clock-btn .date-small {
                    font-size: 0.6rem;
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
            }

            [data-bs-theme="dark"] .card:hover {
                border-color: rgba(139, 92, 246, 0.8);
            }

            /* Dark mode untuk accent indicators */
            [data-bs-theme="dark"] .card.priority-urgent::before {
                background: #ff6b6b;
            }

            [data-bs-theme="dark"] .card.deadline-this-week::after {
                background: #ffa726;
            }

            [data-bs-theme="dark"] .card.priority-urgent.deadline-this-week::before {
                background: linear-gradient(90deg, #ff6b6b 0%, #ffa726 100%);
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

            [data-bs-theme="dark"] .deadline .quantity-number {
                color: #ced4da !important;
            }

            [data-bs-theme="dark"] .card-body .deadline .quantity-number {
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
                box-shadow: 0 0 0 rgba(138, 92, 246, 0) !important;
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
                box-shadow: 0 0 0 rgba(138, 92, 246, 0) !important;
                border-color: #a78bfa !important;
            }

            [data-bs-theme="dark"] #searchInput.typing {
                border-color: #f39c12 !important;
                box-shadow: 0 0 0px rgba(243, 156, 18, 0) !important;
            }

            /* Dark mode untuk no results */
            [data-bs-theme="dark"] #noResults .alert {
                background: linear-gradient(135deg, rgba(255, 193, 7, 0.2), rgba(255, 193, 7, 0.1)) !important;
                border: 1px solid rgba(255, 193, 7, 0.5) !important;
                color: #fbbf24 !important;
            }

            [data-bs-theme="dark"] .project-card.search-highlight {
                border-color: rgba(139, 92, 246, 0.8) !important;
                /* box-shadow: 0 0px 0px rgba(138, 92, 246, 0) !important; */
            }

            /* Form improvements */
            .form-control,
            .form-select {
                border-radius: 25px;
                border: 1.5px solid rgba(226, 232, 240, 0.8);
                background: rgba(255, 255, 255, 0.95);
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #8b5cf6;
                box-shadow: 0 0 0 rgba(138, 92, 246, 0);
                background: rgba(255, 255, 255, 1);
            }

            /* Live search enhancements */
            #searchInput {
                transition: all 0.1s ease;
            }

            #searchInput:focus {
                box-shadow: 0 0 0 rgba(138, 92, 246, 0);
                border-color: #8b5cf6;
            }

            /* Search button loading state */
            .btn-primary-custom {
                transition: all 0.1s ease;
            }

            .btn-primary-custom .bi-hourglass-split {
                animation: searchLoading 1s infinite ease-in-out;
            }

            /* Search input typing indicator */
            #searchInput.typing {
                border-color: #ffc107;
                /* box-shadow: 0 0 0 rgba(255, 193, 7, 0); */
            }

            /* No results styling */
            #noResults .alert {
                background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));
                border: 1px solid rgba(255, 193, 7, 0.3);
                color: #f59e0b;
                border-radius: 15px;
            }

            /* Highlight search matches */
            .project-card.search-highlight {
                border-color: rgba(139, 92, 246, 0.6) !important;
            }

            /* Combined search and filter section */
            .search-filter-combined {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 1rem;
                margin-bottom: 1.2rem;
                border: 1px solid rgba(235, 240, 226, 0.8);
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
                /* box-shadow: 0 0 0 rgba(0, 0, 0, 0); */
            }

            #toggleDarkMode:hover {
                background: #6c757d9a;
                color: white;
                border-color: #6c757d;
                /* box-shadow: 0 0px 0px rgba(0, 0, 0, 0); */
            }

            [data-bs-theme="dark"] #toggleDarkMode {
                border-color: #8b5cf6;
                background: rgba(139, 92, 246, 0.2);
                color: #a78bfa;
            }

            [data-bs-theme="dark"] #toggleDarkMode:hover {
                background: #8a5cf671;
                color: white;
                border-color: #8b5cf6;
            }

            /* Fancybox Custom Styling */
            .fancybox__backdrop {
                background: rgba(0, 0, 0, 0.85) !important;
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

            .fancybox__slide .fancybox__image {
                width: 100% !important;
                height: auto !important;
                max-width: 100vw !important;
                max-height: 90vh !important;
                object-fit: contain !important;
                image-rendering: auto;
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
            }

            .btn-outline-purple:focus {}

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
            }

            .btn-outline-secondary:hover {
                background-color: #6c757d;
                border-color: #6c757d;
                color: white;
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

            /* Priority bullet colors */
            .priority-urgent {
                color: #dc3545 !important;
                /* Red for Urgent */
            }

            .priority-high {
                color: #ffc107 !important;
                /* Yellow for High */
            }

            .priority-normal {
                color: #0d6efd !important;
                /* Blue for Normal */
            }

            .priority-low {
                color: #6c757d !important;
                /* Gray for Low */
            }

            /* Celebration Float GIF */
            .celebration-float {
                position: fixed;
                bottom: 5px;
                right: 5px;
                z-index: 100;
                width: 120px;
                height: 120px;
                border-radius: 50%;
                overflow: hidden;
            }

            .celebration-float img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
            }

            /* Responsive behavior */
            @media (max-width: 768px) {
                .celebration-float {
                    width: 50px;
                    height: 50px;
                    bottom: 15px;
                    right: 15px;
                }
            }

            @media (max-width: 576px) {
                .celebration-float {
                    width: 45px;
                    height: 45px;
                    bottom: 10px;
                    right: 10px;
                }
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
                                    style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid rgba(139, 92, 246, 0.2);"
                                    loading="lazy">
                            </a>
                        </div>
                        <h3 class="fw-bold text-header mb-0">Mascot Project List</h3>
                    </div>

                    <!-- Right side: Clock + Gallery Buttons + Actions -->
                    <div class="d-flex align-items-center gap-2">
                        <!-- Real-time Clock - Compact button style -->
                        <div class="realtime-clock-btn">
                            <i class="bi bi-clock"></i>
                            <span id="timeText">--:--:--</span>
                            <span>WIB</span>
                            <span class="date-small" id="dateText">-- --- ----</span>
                        </div>

                        <?php if (!empty($projects)): ?>
                        <div class="gallery-actions d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-purple"
                                onclick="viewAllProjectImages()"
                                title="View All Project Images (<?= count($projects) ?> projects)"
                                data-bs-toggle="tooltip" data-bs-placement="bottom">
                                <i class="bi bi-images"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-purple"
                                onclick="viewAllMaterialImages()"
                                title="View All Submission Notes (<?= count($projects) ?> images)"
                                data-bs-toggle="tooltip" data-bs-placement="bottom">
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

                            <div class="filter-divider"></div>

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
                                            <option value="Urgent" class="priority-urgent"
                                                <?= isset($_GET['priority']) && $_GET['priority'] === 'Urgent' ? 'selected' : '' ?>>
                                                ● Urgent</option>
                                            <option value="High" class="priority-high"
                                                <?= isset($_GET['priority']) && $_GET['priority'] === 'High' ? 'selected' : '' ?>>
                                                ● High</option>
                                            <option value="Normal" class="priority-normal"
                                                <?= isset($_GET['priority']) && $_GET['priority'] === 'Normal' ? 'selected' : '' ?>>
                                                ● Normal</option>
                                            <option value="Low" class="priority-low"
                                                <?= isset($_GET['priority']) && $_GET['priority'] === 'Low' ? 'selected' : '' ?>>
                                                ● Low</option>
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
                                <a href="mascot_index.php" class="btn btn-reset-custom" title="Reset all filters">
                                    <i class="bi bi-arrow-clockwise"></i>
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
                <?php
                // Tentukan kelas CSS berdasarkan kondisi
                $cardClasses = ['card', 'project-card'];
                if (strtolower($row['priority']) === 'urgent') {
                    $cardClasses[] = 'priority-urgent';
                }
                if (isThisWeek($row['deadline'])) {
                    $cardClasses[] = 'deadline-this-week';
                }
                ?>
                <div class="<?= implode(' ', $cardClasses) ?>"
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
                                alt="No Image Project yet" loading="lazy">
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
                            <?php if ($row['deadline']): ?>
                            <div class="deadline">
                                <i class="bi bi-calendar-check"></i>
                                <span style="font-weight:500;">Deadline:</span>
                                <span class="deadline-date" style="font-weight:bold;">
                                    <?= htmlspecialchars(Carbon::parse($row['deadline'])->format('d M Y')) ?>
                                </span>
                            </div>
                            <?php else: ?>
                            <div class="deadline" style="font-weight:500;">
                                <i class="bi bi-calendar-x me-1"></i>Deadline: -
                            </div>
                            <?php endif; ?>
                            <div class="quantity">
                                <i class="bi bi-box"></i>
                                <span style="font-weight:500;">Quantity:</span>
                                <span class="quantity-number" style="font-weight:bold;">
                                    <?= htmlspecialchars($row['quantity']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Submission Note image dengan purple accent container -->
                        <div class="material-container"
                            style="margin-top: 8px; border-radius: 10px; overflow: hidden; border: 1px solid rgba(139, 92, 246, 0.1); transition: border-color 0.1s ease;">

                            <?php
                            // Handle multiple submission note images
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
                                    data-caption="<?= htmlspecialchars($row['project_name']) ?> - Submission Note <?= $imgIndex + 1 ?>"
                                    <?= $imgIndex === 0 ? '' : 'style="display:none;"' ?>>
                                    <?php if ($imgIndex === 0): ?>
                                    <img src="uploads/materials/<?= htmlspecialchars($image) ?>"
                                        alt="No Submission Notes yet"
                                        style="width: 100%; height: 150px; object-fit: contain; background-color: #f8f9fa; cursor: pointer;"
                                        loading="lazy">
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
                                <p class="m-0">No Submission Notes yet</p>
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
                        <a class="page-link" href="?page=<?= $currentPage - 1 . $urlParams ?>" aria-label="Previous">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link" aria-label="Previous">
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
                        <a class="page-link" href="?page=<?= $currentPage + 1 . $urlParams ?>" aria-label="Next">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link" aria-label="Next">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>

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

                <!-- All Submission Note images -->
                <?php foreach ($projects as $index => $row): ?>
                <?php 
                    $materialImages = parseImageData($row['material_image']);
                    foreach ($materialImages as $imgIndex => $image): ?>
                <a href="uploads/materials/<?= htmlspecialchars($image) ?>" data-fancybox="all-materials"
                    data-caption="<?= htmlspecialchars($row['project_name']) ?> - Submission Note <?= $imgIndex + 1 ?>"></a>
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
                        right: ["slideshow", "thumbs", "fullscreen", "close"],
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

            // Hapus efek hover untuk link gambar untuk mengoptimalkan performa
            document.addEventListener('DOMContentLoaded', function() {
                // Image hover effects dihapus untuk performa
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
                    } else {
                        // Has results
                        noResults.style.display = 'none';
                    }
                }

                // Event listener for real-time search
                if (searchInput && projectCards.length > 0) {
                    const debouncedFilter = debounce(performRealTimeFilter,
                        100); // 100ms delay untuk responsiveness lebih baik

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

            // Real-time Clock - WIB Timezone
            function updateClock() {
                const now = new Date();

                // WIB adalah UTC+7
                const wibTime = new Date(now.getTime() + (7 * 60 * 60 * 1000));

                // Format waktu HH:MM:SS
                const timeString = wibTime.toLocaleTimeString('en-US', {
                    timeZone: 'UTC',
                    hour12: false,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });

                // Format tanggal - Day, DD MMM YYYY (English UK format)
                const dateString = wibTime.toLocaleDateString('en-GB', {
                    timeZone: 'UTC',
                    weekday: 'short',
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });

                // Update elemen
                const timeElement = document.getElementById('timeText');
                const dateElement = document.getElementById('dateText');

                if (timeElement) timeElement.textContent = timeString;
                if (dateElement) dateElement.textContent = dateString;
            }

            // Update setiap detik
            updateClock();
            setInterval(updateClock, 1000);
        </script>

        <!-- Celebration Float GIF -->
        <div class="celebration-float">
            <img src="uploads/celebration.gif" alt="Celebration" loading="lazy">
        </div>
    </body>

</html>
