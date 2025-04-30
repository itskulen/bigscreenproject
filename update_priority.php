<?php
include 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'], $data['priority'])) {
    $id = $data['id'];
    $priority = $data['priority'];

    $stmt = $pdo->prepare("UPDATE gallery SET priority = ? WHERE id = ?");
    $success = $stmt->execute([$priority, $id]);

    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}