<?php 
// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: src/auth/dashboard.php");
    exit;
}

// Use absolute path for config
require_once __DIR__ . '/includes/config.php';

// Get error/success messages
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';

// Clear messages after displaying
unset($_SESSION['error']);
unset($_SESSION['success']);

$page_title = "Login - TrueCare";
$show_navbar = true;
include __DIR__ . '/includes/header.php';
?>