<?php
$host = 'localhost';
$dbname = 'tv_db';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
