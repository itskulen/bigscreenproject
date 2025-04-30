<?php
session_start();
require('config.php');
include 'db.php';

// Ambil keyword dan filter status dari URL
$search = $_GET['search'] ?? '';
$filter = $_GET['project_status'] ?? '';

function isThisWeek($deadline)
{
    $currentDate = new DateTime();
    $startOfWeek = $currentDate->modify('this week')->setTime(0, 0, 0); // Awal minggu (Senin)
    $endOfWeek = (clone $startOfWeek)->modify('+6 days')->setTime(23, 59, 59); // Akhir minggu (Minggu)

    $deadlineDate = new DateTime($deadline);

    return $deadlineDate >= $startOfWeek && $deadlineDate <= $endOfWeek;
}

// Filter berdasarkan kategori 
$sql = "SELECT * FROM gallery WHERE category = 'mascot' AND project_status != 'archived' AND project_name LIKE ?";
$params = ["%$search%"];

if (isset($_GET['this_week']) && $_GET['this_week'] == '1') {
    $startOfWeek = (new DateTime())->modify('this week')->format('Y-m-d');
    $endOfWeek = (new DateTime())->modify('this week +6 days')->format('Y-m-d');
    $sql .= " AND deadline BETWEEN ? AND ?";
    $params[] = $startOfWeek;
    $params[] = $endOfWeek;
} elseif ($filter !== '') {
    $sql .= " AND project_status = ?";
    $params[] = $filter;
}

if (isset($_GET['priority']) && $_GET['priority'] !== '') {
    $sql .= " AND priority = ?";
    $params[] = $_GET['priority'];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

$startOfWeek = (new DateTime())->modify('this week')->format('Y-m-d');
$endOfWeek = (new DateTime())->modify('this week +6 days')->format('Y-m-d');

$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery WHERE category = 'mascot' AND project_status != 'archived' AND deadline BETWEEN ? AND ?");
$stmt->execute([$startOfWeek, $endOfWeek]);
$this_week_count = $stmt->fetchColumn();

// Fungsi warna status
function getStatusClass($status)
{
    switch (strtolower($status)) {
        case 'upcoming':
            return 'background-color: #0dcaf0;'; // blue
        case 'urgent':
            return 'background-color: #ef4444;'; // red
        case 'in progress':
            return 'background-color: #eab308;'; // yellow
        case 'revision':
            return 'background-color: #8b5cf6;'; // purple
        case 'completed':
            return 'background-color: #22c55e;'; // green
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
    'Urgent' => 0
];

foreach ($status_counts as $status => &$count) {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery WHERE category = 'mascot' AND project_status = ?");
    $stmt->execute([$status]);
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
<html>

<head>
    <meta charset="UTF-8">
    <title>Mascot Project List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
        color: #000;
        padding: 20px;
        text-align: center;
    }

    .dark-mode {
        background-color: #212529;
        color: #ffffff;
    }

    .dark-mode .card {
        background-color: #1e1e1e;
        color: #ffffff;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.8s ease forwards;
        animation-delay: 0.3s;
    }

    .dark-mode .card:hover {
        transform: translateY(-20px);
        transition: transform 0.3s ease;
        box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
    }

    .dark-mode .card-body {
        padding: 10px;
    }

    .dark-mode .btn {
        border-color: #ffffff;
    }

    .dark-mode .btn-secondary {
        background-color: #444;
        color: #fff;
    }

    .dark-mode .btn-primary {
        background-color: #007bff;
        color: #fff;
    }

    .dark-mode .btn-success {
        background-color: #28a745;
        color: #fff;
    }

    .dark-mode .btn-warning {
        background-color: #ffc107;
        color: #000;
    }

    .dark-mode .btn-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .btn-indigo {
        background-color: #6610f2;
        color: #fff;
    }

    .btn-indigo:hover {
        background-color: #520dc2;
        color: #fff;
    }

    .dark-mode .modal {
        background-color: rgba(0, 0, 0, 0.9);
    }

    .dark-mode .modal-content {
        background-color: #1e1e1e;
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
        transform: translateY(-10px);
        transition: transform 0.3s ease;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
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

    .status-belum {
        background-color: red;
    }

    .status-progress {
        background-color: orange;
    }

    .status-revisi {
        background-color: purple;
    }

    .status-selesai {
        background-color: green;
    }
    </style>
</head>

<body>
    <!-- Tombol Login atau Dashboard -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mx-auto">Mascot Project List</h1>
        <div>
            <button id="toggleDarkMode" class="btn btn-outline-secondary">
                <i class="bi bi-moon"></i> Dark Mode
            </button>
            <?php if ($isLoggedIn): ?>
            <a href="mascot_admin.php" class="btn btn-primary">
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
        <form method="GET" action="mascot_index.php">
            <button type="submit" name="this_week" value="1"
                class="btn fw-semibold text-danger-emphasis bg-danger-subtle border border-danger-subtle">
                This Week: <?= $this_week_count ?>
            </button>
        </form>

        <div style="border-left: 2px solid #ccc; height: 40px;"></div>

        <form method="GET" action="mascot_index.php">
            <button type="submit" name="project_status" value="" class="btn btn-secondary">
                All Project: <?= isset($total_projects) ? $total_projects : 0 ?>
            </button>
        </form>
        <form method="GET" action="mascot_index.php">
            <button type="submit" name="project_status" value="Upcoming" class="btn btn-info">
                Upcoming: <?= $status_counts['Upcoming'] ?>
            </button>
        </form>
        <form method="GET" action="mascot_index.php">
            <button type="submit" name="project_status" value="In Progress" class="btn btn-warning text-dark">
                In Progress: <?= $status_counts['In Progress'] ?>
            </button>
        </form>
        <form method="GET" action="mascot_index.php">
            <button type="submit" name="project_status" value="Urgent" class="btn btn-danger">
                Urgent: <?= $status_counts['Urgent'] ?>
            </button>
        </form>
        <form method="GET" action="mascot_index.php">
            <button type="submit" name="project_status" value="Revision" class="btn btn-indigo">
                Revision: <?= $status_counts['Revision'] ?>
            </button>
        </form>
        <form method="GET" action="mascot_index.php">
            <button type="submit" name="project_status" value="Completed" class="btn btn-success">
                Completed: <?= $status_counts['Completed'] ?>
            </button>
        </form>

        <div style="border-left: 2px solid #ccc; height: 40px;"></div>

        <form method="GET" action="mascot_index.php" class="d-flex align-items-center">
            <select name="priority" class="form-select me-2" onchange="this.form.submit()">
                <option value="">Filter by Priority</option>
                <option value="High" <?= isset($_GET['priority']) && $_GET['priority'] === 'High' ? 'selected' : '' ?>>
                    High</option>
                <option value="Medium"
                    <?= isset($_GET['priority']) && $_GET['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="Low" <?= isset($_GET['priority']) && $_GET['priority'] === 'Low' ? 'selected' : '' ?>>Low
                </option>
            </select>
        </form>
    </div>
    <br>

    <div class="card-grid">
        <?php foreach ($projects as $row): ?>
        <div class="card" style="width: 18rem; position: relative;">
            <?php if (isThisWeek($row['deadline'])): ?>
            <small class="fw-semibold text-danger-emphasis bg-danger-subtle">
                This Week!
            </small>
            <?php endif; ?>
            <img src="uploads/projects/<?= htmlspecialchars($row['project_image']) ?>" style="cursor: pointer;"
                alt="No Image Project yet" onclick="openModal(this.src)">
            <div class="card-body">
                <strong style="cursor: pointer;"
                    onclick="openGoogleSlideModal('<?= htmlspecialchars($row['subform_embed']) ?>')">
                    <?= htmlspecialchars($row['project_name']) ?>
                </strong><br>
                <span class="status-label" style="<?= getStatusClass($row['project_status']) ?>">
                    <?= htmlspecialchars($row['project_status']) ?>
                </span>
                <span class="status-label" style="<?= getPriorityClass($row['priority']) ?> margin-left: 5px;">
                    P :
                    <?= htmlspecialchars($row['priority']) ?>
                </span>
                <div class="deadline">Quantity: <?= htmlspecialchars($row['quantity']) ?></div>
                <?php if ($row['deadline']): ?>
                <div class="deadline">Deadline: <?= htmlspecialchars($row['deadline']) ?></div>
                <?php endif; ?>
                <p style="margin-top: 8px; font-size: 14px;">
                    <?= nl2br(htmlspecialchars($row['description'])) ?>
                </p>
                <div style="margin-top: 5px; cursor: pointer;">
                    <img src="uploads/materials/<?= htmlspecialchars($row['material_image']) ?>"
                        alt="No Submission Notes yet"
                        style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px;"
                        onclick="openModal(this.src)">
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="googleSlideIframe" class="w-100 h-100" frameborder="0" allowfullscreen></iframe>
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
        // Format URL embed Google Slides
        const embedUrl = embedLink.replace('/edit', '/embed');

        // Set iframe source
        document.getElementById('googleSlideIframe').src = embedUrl;

        // Tampilkan modal
        const modal = new bootstrap.Modal(document.getElementById('googleSlideModal'));
        modal.show();
    }

    function closeGoogleSlideModal() {
        const modal = document.getElementById("googleSlideModal");
        const iframe = document.getElementById("googleSlideIframe");
        iframe.src = ""; // Clear the iframe source
        modal.style.display = "none";
    }

    // Ambil tombol toggle
    const toggleDarkMode = document.getElementById('toggleDarkMode');
    const body = document.body;

    // Periksa preferensi dark mode dari localStorage
    if (localStorage.getItem('dark-mode') === 'enabled') {
        body.classList.add('dark-mode');
    }

    // Tambahkan event listener untuk tombol toggle
    toggleDarkMode.addEventListener('click', () => {
        if (body.classList.contains('dark-mode')) {
            body.classList.remove('dark-mode');
            toggleDarkMode.innerHTML = '<i class="bi bi-moon"></i> Dark Mode';
            localStorage.setItem('dark-mode', 'disabled');
        } else {
            body.classList.add('dark-mode');
            toggleDarkMode.innerHTML = '<i class="bi bi-sun"></i> Light Mode';
            localStorage.setItem('dark-mode', 'enabled');
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>