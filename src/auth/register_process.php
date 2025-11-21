<?php
session_start();

// Enable all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include config
require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    error_log("Registration attempt: $email, Role: $role");
    
    try {
        // Validate inputs
        if (empty($name) || empty($email) || empty($role) || empty($password)) {
            throw new Exception("All required fields must be filled.");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }
        
        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match.");
        }
        
        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters long.");
        }
        
        if (!in_array($role, ['donor', 'orphanage'])) {
            throw new Exception("Please select a valid role.");
        }
        
        // Check if email already exists
        $checkQuery = "SELECT user_id FROM users WHERE email = :email";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':email', $email);
        
        if (!$checkStmt->execute()) {
            throw new Exception("Database error checking email.");
        }
        
        if ($checkStmt->rowCount() > 0) {
            throw new Exception("Email already registered. Please use a different email.");
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $insertQuery = "INSERT INTO users (name, email, phone, role, password, created_at) 
                        VALUES (:name, :email, :phone, :role, :password, NOW())";
        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->bindParam(':name', $name);
        $insertStmt->bindParam(':email', $email);
        $insertStmt->bindParam(':phone', $phone);
        $insertStmt->bindParam(':role', $role);
        $insertStmt->bindParam(':password', $hashed_password);
        
        if ($insertStmt->execute()) {
            $user_id = $db->lastInsertId();
            error_log("User created successfully - ID: $user_id");
            
            // If orphanage, create orphanage record
            if ($role === 'orphanage') {
                $orphanageQuery = "INSERT INTO orphanages (user_id, name, location, status, created_at) 
                                   VALUES (:user_id, :name, '', 'pending', NOW())";
                $orphanageStmt = $db->prepare($orphanageQuery);
                $orphanageStmt->bindParam(':user_id', $user_id);
                $orphanageStmt->bindParam(':name', $name);
                $orphanageStmt->execute();
                error_log("Orphanage record created");
            }
            
            // Set session and redirect
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;
            $_SESSION['logged_in'] = true;
            
            error_log("Registration complete - redirecting to dashboard");
            
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit;
            
        } else {
            throw new Exception("Failed to create user account.");
        }
        
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        
        // Store error and form data
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['form_data'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => $role
        ];
        
        header("Location: ../../register.php");
        exit;
    }
    
} else {
    header("Location: ../../register.php");
    exit;
}
?>