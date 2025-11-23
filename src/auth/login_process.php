<?php
// src/auth/login_process.php

// Start session at the very top
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

try {
    $stmt = $db->prepare("SELECT user_id, name, email, password, role, is_active, LENGTH(password) as hash_length FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    // DEBUG: Add this section to see what's happening
    if ($user) {
        error_log("=== LOGIN DEBUG ===");
        error_log("User Email: " . $user['email']);
        error_log("Stored Hash: " . $user['password']);
        error_log("Hash Length: " . $user['hash_length']);
        error_log("Input Password: " . $password);
        error_log("Password Verify: " . (password_verify($password, $user['password']) ? 'TRUE' : 'FALSE'));
        error_log("User Active: " . $user['is_active']);
        error_log("User Role: " . $user['role']);
    }

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        error_log("PASSWORD VERIFICATION FAILED for user: " . $user['email']);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }

    if (!$user['is_active']) {
        echo json_encode(['success' => false, 'message' => 'Account is deactivated. Please contact support.']);
        exit;
    }

    // Regenerate session id on login
    session_regenerate_id(true);
    
    // Set session data
    $_SESSION['user_id'] = (int)$user['user_id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['logged_in'] = true;

    // Update last_login
    $update = $db->prepare("UPDATE users SET last_login = NOW() WHERE user_id = :uid");
    $update->execute([':uid' => $user['user_id']]);

    // Determine redirect based on role
    if ($user['role'] === 'donor') {
        $redirect = abs_path('src/auth/dashboard.php');
    } elseif ($user['role'] === 'orphanage') {
        $redirect = abs_path('src/auth/dashboard.php');
    } elseif ($user['role'] === 'admin') {
        $redirect = abs_path('src/admin/admin.php');
    } else {
        $redirect = abs_path('src/auth/dashboard.php');
    }

    error_log("LOGIN SUCCESSFUL for user: " . $user['email']);
    echo json_encode([
        'success' => true, 
        'message' => 'Login successful!', 
        'redirect' => $redirect
    ]);
    exit;
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
    exit;
}
?>