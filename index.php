<?php
require('config.php');
include 'db.php';

// Ambil keyword dan filter status dari URL
$search = $_GET['search'] ?? '';
$filter = $_GET['project_status'] ?? '';

$sql = "SELECT * FROM gallery WHERE project_name LIKE ?";
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
function getStatusClass($status) {
    return match (strtolower($status)) {
        'not started'   => 'background-color: #ef4444;', // red
        'in progress'   => 'background-color: #eab308;', // yellow
        'revision'      => 'background-color: #8b5cf6;', // purple
        'completed'     => 'background-color: #22c55e;', // green
        default         => 'background-color: #d1d5db;', // light gray
    };
}
$sql = "SELECT COUNT(*) AS total FROM gallery";
$result = $conn->query($sql);



$status_counts = [
    'Completed' => 0,
    'In Progress' => 0,
    'Revision' => 0,
    'Not Started' => 0
];

foreach ($status_counts as $status => &$count) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM gallery WHERE project_status = ?");
    $stmt->execute([$status]);
    $count = $stmt->fetchColumn();
}
unset($count);




// Tampilkan hasil
if ($result) {
    $row = $result->fetch_assoc();
    $total_projects = $row['total'];
} else {
    $total_projects = 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mascot Project List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
            text-align: center;
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);        }
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
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.7);
        }
        .modal-content {
            margin: 5% auto;
            display: block;
            max-width: 80%;
            border-radius: 8px;
        }
        .modal-content, .close {
            animation: fadein 0.3s;
        }
        .close {
            position: absolute;
            top: 20px; right: 30px;
            color: #fff;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
        }
        @keyframes fadein {
            from { opacity: 0; }
            to   { opacity: 1; }
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
.status-belum { background-color: red; }
.status-progress { background-color: orange; }
.status-revisi { background-color: purple; }
.status-selesai { background-color: green; }
                    
    </style>
</head>
<body>

<h1>Mascot Project List</h1>

<form method="GET" class="search-filter">
    <input type="text" name="search" placeholder="Cari Project..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
    <select name="project_status">
        <option value="">All Project</option>
        <option value="Not Started" <?= $filter === 'Not Started' ? 'selected' : '' ?>>Not Started</option>
        <option value="In Progress" <?= $filter === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
        <option value="Revision" <?= $filter === 'Revision' ? 'selected' : '' ?>>Revision</option>
        <option value="Completed" <?= $filter === 'Completed' ? 'selected' : '' ?>>Completed</option>
    </select>

    <a href="login.php">
    <button type="button">Admin Login</button>
</a></form>
</button>       
<div style="display: flex; gap: 20px; margin: 20px 0;">
    <div style="background-color:rgb(113, 136, 121); padding: 10px 20px; border-radius: 10px; color: white;">
    All Project: <?= isset($total_projects) ? $total_projects : 0 ?>
    </div>
    <div style="background-color: #22c55e; padding: 10px 20px; border-radius: 10px; color: white;">
        Completed: <?= $status_counts['Completed'] ?>
    </div>
    <div style="background-color: #eab308; padding: 10px 20px; border-radius: 10px; color: white;">
        In Progress: <?= $status_counts['In Progress'] ?>
    </div>
    <div style="background-color: #8b5cf6; padding: 10px 20px; border-radius: 10px; color: white;">
        Revision: <?= $status_counts['Revision'] ?>
    </div>
    <div style="background-color: #ef4444; padding: 10px 20px; border-radius: 10px; color: white;">
        Not Started: <?= $status_counts['Not Started'] ?>
    </div>
</div>
<br>

<div class="card-grid">
    <?php foreach ($projects as $row): ?>
        <div class="card" style="width: 18rem;">
        <img src="uploads/projects/<?= htmlspecialchars($row['project_image']) ?>" alt="Project" onclick="openModal(this.src)">
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
                    <img src="uploads/materials/<?= htmlspecialchars($row['material_image']) ?>" alt="Material" style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px;" onclick="openModal(this.src)">
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal -->
<div id="imgModal" class="modal" onclick="closeModal()">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<script>
function openModal(src) {
    document.getElementById("imgModal").style.display = "block";
    document.getElementById("modalImage").src = src;
}
function closeModal() {
    document.getElementById("imgModal").style.display = "none";
}
</script>

</body>
</html>
    