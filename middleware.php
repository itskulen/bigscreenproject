<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Mulai sesi hanya jika belum dimulai
}

function checkUserRole($requiredRole)
{
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }

    if ($_SESSION['role'] !== $requiredRole) {
        header("Location: unauthorized.php"); // Halaman jika pengguna tidak memiliki akses
        exit;
    }
}