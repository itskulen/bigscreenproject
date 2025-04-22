<?php
include 'db.php';

// Data pengguna yang akan ditambahkan
$users = [
    ['username' => 'mascot_admin', 'password' => 'mascot123', 'role' => 'mascot'],
    ['username' => 'costume_admin', 'password' => 'costume123', 'role' => 'costume']
];

foreach ($users as $user) {
    $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT); // Hash password
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$user['username'], $hashedPassword, $user['role']]);
}

echo "Users successfully added!";