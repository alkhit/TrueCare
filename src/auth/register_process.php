<?php
// src/auth/register_process.php

// Start session at the very top
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Clear any existing session data to prevent conflicts
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_role']);
unset($_SESSION['user_email']);

// Basic sanitation
$name = trim($_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
$role = in_array($_POST['role'] ?? 'donor', ['donor','orphanage','admin']) ? $_POST['role'] : 'donor';
$phone = trim($_POST['phone'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

if ($password !== $confirm) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

try {
    // Check existing user (case-insensitive)
    $stmt = $db->prepare("SELECT user_id FROM users WHERE LOWER(email) = :email LIMIT 1");
    $stmt->execute([':email' => strtolower($email)]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already registered. Please login instead.']);
        exit;
    }

    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $insert = $db->prepare("INSERT INTO users (name, email, password, role, phone, created_at, is_active) VALUES (:name, :email, :password, :role, :phone, NOW(), 1)");
    $insert->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $hash,
        ':role' => $role,
        ':phone' => $phone
    ]);

    $userId = $db->lastInsertId();

    // Set session data immediately
    $_SESSION['user_id'] = (int)$userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_role'] = $role;
    $_SESSION['user_email'] = $email;
    $_SESSION['logged_in'] = true;

    // Determine redirect based on role
    $redirect = '';
    switch($role) {
        case 'donor':
            $redirect = '../../src/donor/dashboard.php';
            break;
        case 'orphanage':
            $redirect = '../../src/auth/register_orphanage.php';
            break;
        case 'admin':
            $redirect = '../../src/admin/dashboard.php';
            break;
        default:
            $redirect = '../../src/auth/dashboard.php';
    }
    http_response_code(200);
    header('Location: ' . $redirect);
    exit;

} catch (Exception $e) {
    error_log("Register error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid details. Please check your input and try again.']);
    exit;
}

// Helper function to get absolute path
function getAbsolutePath($relativePath) {
    $basePath = dirname(dirname(dirname(__DIR__)));
    return realpath($basePath . '/' . $relativePath);
}
?>