<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Mulai sesi hanya jika belum dimulai
}

// Include error handler
require_once 'error_handler.php';

function checkUserRole($requiredRole)
{
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }

    if ($_SESSION['role'] !== $requiredRole) {
        ErrorHandler::handle403("You don't have permission to access this resource");
    }
}

// Function to validate ID parameter
function validateId($id)
{
    if (!isset($id) || !is_numeric($id) || $id <= 0) {
        ErrorHandler::handleInvalidParameter('id', $id);
    }
    return (int) $id;
}

// Function to validate required POST data
function validatePostData($requiredFields)
{
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            ErrorHandler::handleInvalidParameter($field, 'missing or empty');
        }
    }
}

// Function to check if page exists
function validatePage($page)
{
    ErrorHandler::validatePageExists($page);
}
