<?php
session_start();
require('config.php');
include 'db.php';

// Ambil keyword dan filter status dari URL
$search = $_GET['search'] ?? '';
$filter = $_GET['project_status'] ?? '';

// Filter berdasarkan kategori costume
$sql = "SELECT * FROM gallery WHERE category = 'costume' AND project_name LIKE ?";
$params = ["%$search%"];

if ($filter !== '') {
    $sql .= " AND project_status = ?";
    $params[] = $filter;
    $status = "project_status";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

// Fungsi warna status
function getStatusClass($status)
{
    return match (strtolower($status)) {
        'not started'   => 'background-color: #ef4444;', // red
        'in progress'   => 'background-color: #eab308;', // yellow
        'revision'      => 'background-color: #8b5cf6;', // purple
        'completed'     => 'background-color: #22c55e;', // green
        default         => 'background-color: #d1d5db;', // light gray
    };
}
$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM gallery WHERE category = 'costume'");
$stmt->execute();
$total_projects = $stmt->fetchColumn();

// Hitung jumlah proyek berdasarkan status untuk kategori costume
$status_counts = [
    'Completed' => 0,
    'In Progress' => 0,
    'Revision' => 0,
    'Not Started' => 0
];

foreach ($status_counts as $status => &$count) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM gallery WHERE project_status = ? AND category = 'costume'");
    $stmt->execute([$status]);
    $count = $stmt->fetchColumn();
}
unset($count);

// Periksa apakah pengguna sudah login
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $isLoggedIn ? $_SESSION : null;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Costume Project List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
        color: #000;
        padding: 20px;
        text-align: center;
    }

    .dark-mode {
        background-color: #121212;
        color: #ffffff;
    }

    .dark-mode .card {
        background-color: #1e1e1e;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
        transition: transform 0.2s;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.8s ease forwards;
        animation-delay: 0.3s;
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

    .card {
        width: 220px;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: transform 0.2s;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.8s ease forwards;
        animation-delay: 0.3s;
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
    }

    .deadline {
        font-size: 12px;
        color: #888;
        margin-top: 4px;
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
        <h1 class="mx-auto">Costume Project List</h1>
        <div>
            <button id="toggleDarkMode" class="btn btn-outline-secondary">Dark Mode</button>
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
            <input type="text" name="search" class="form-control" placeholder="Cari Project..."
                value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>
    <div style="display: flex; gap: 20px;">
        <form method="GET" action="costume_index.php">
            <button type="submit" name="project_status" value="" class="btn btn-secondary">
                All Project: <?= isset($total_projects) ? $total_projects : 0 ?>
            </button>
        </form>
        <form method="GET" action="costume_index.php">
            <button type="submit" name="project_status" value="Completed" class="btn btn-success">
                Completed: <?= $status_counts['Completed'] ?>
            </button>
        </form>
        <form method="GET" action="costume_index.php">
            <button type="submit" name="project_status" value="In Progress" class="btn btn-warning text-dark">
                In Progress: <?= $status_counts['In Progress'] ?>
            </button>
        </form>
        <form method="GET" action="costume_index.php">
            <button type="submit" name="project_status" value="Revision" class="btn btn-primary">
                Revision: <?= $status_counts['Revision'] ?>
            </button>
        </form>
        <form method="GET" action="costume_index.php">
            <button type="submit" name="project_status" value="Not Started" class="btn btn-danger">
                Not Started: <?= $status_counts['Not Started'] ?>
            </button>
        </form>
    </div>
    <br>

    <div class="card-grid">
        <?php foreach ($projects as $row): ?>
        <div class="card" style="width: 18rem;">
            <img src="uploads/projects/<?= htmlspecialchars($row['project_image']) ?>" alt="Project"
                onclick="openModal(this.src)">
            <div class="card-body">
                <strong><?= htmlspecialchars($row['project_name']) ?></strong><br>
                <span class="status-label" style="<?= getStatusClass($row['project_status']) ?>">
                    <?= htmlspecialchars($row['project_status']) ?>
                </span>
                <?php if ($row['deadline']): ?>
                <div class="deadline">Deadline: <?= htmlspecialchars($row['deadline']) ?></div>
                <?php endif; ?>
                <p style="margin-top: 8px; font-size: 14px; color: #555;">
                    <?= nl2br(htmlspecialchars($row['description'])) ?>
                </p>
                <div style="margin-top: 10px;">
                    <img src="uploads/materials/<?= htmlspecialchars($row['material_image']) ?>" alt="Material"
                        style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px;"
                        onclick="openModal(this.src)">
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal -->
    <div id="imgModal" class="modal" onclick="closeModal()">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage" alt="Preview Image">
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
            localStorage.setItem('dark-mode', 'disabled'); // Simpan preferensi pengguna
        } else {
            body.classList.add('dark-mode');
            localStorage.setItem('dark-mode', 'enabled'); // Simpan preferensi pengguna
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>