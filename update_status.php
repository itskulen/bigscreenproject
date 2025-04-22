<?php
include 'db.php';

// Ambil data dari request JSON
$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'];
$status = $data['status'];
$category = $data['category']; // Tambahkan kategori

// Update status di database hanya jika kategori cocok
$sql = "UPDATE gallery SET project_status = ? WHERE id = ? AND category = ?";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([$status, $id, $category]);

// Kirim respons JSON
echo json_encode(['success' => $success]);