<?php
// Authentication and user helpers for TrueCare

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user's role
function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

// Get current user's ID
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current user's name
function getUserName() {
    return $_SESSION['user_name'] ?? null;
}

// Require login for a page (optionally require a specific role)
function requireLogin($role = null) {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
    if ($role && getUserRole() !== $role) {
        header('Location: ' . BASE_URL . '/src/auth/dashboard.php');
        exit;
    }
}

// Log out the user
function logoutUser() {
    $_SESSION = [];
    session_destroy();
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// Set user session after login
function setUserSession($user) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_email'] = $user['email'];
}

// You can add more helpers as needed
