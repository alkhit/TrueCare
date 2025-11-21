<?php
session_start();

// Enable all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include config
require_once '../../includes/config.php';

// Log the request
error_log("=== REGISTRATION REQUEST ===");
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    try {
        // Validate inputs
        $errors = [];
        
        if (empty($name)) $errors[] = "Name is required";
        if (empty($email)) $errors[] = "Email is required";
        if (empty($role)) $errors[] = "Role is required";
        if (empty($password)) $errors[] = "Password is required";
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        }
        
        if (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters";
        }
        
        if (!in_array($role, ['donor', 'orphanage'])) {
            $errors[] = "Invalid role selected";
        }
        
        if (!empty($errors)) {
            throw new Exception(implode(", ", $errors));
        }

        // Check if email exists
        $checkQuery = "SELECT user_id FROM users WHERE email = :email";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':email', $email);
        
        if (!$checkStmt->execute()) {
            throw new Exception("Database error checking email availability.");
        }
        
        if ($checkStmt->rowCount() > 0) {
            throw new Exception("Email already registered. Please use a different email.");
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        error_log("Password hashed: $hashed_password");

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
            error_log("✅ User created successfully - ID: $user_id");
            
            // If orphanage, create orphanage record
            if ($role === 'orphanage') {
                $orphanageQuery = "INSERT INTO orphanages (user_id, name, location, status, created_at) 
                                   VALUES (:user_id, :name, '', 'pending', NOW())";
                $orphanageStmt = $db->prepare($orphanageQuery);
                $orphanageStmt->bindParam(':user_id', $user_id);
                $orphanageStmt->bindParam(':name', $name);
                
                if ($orphanageStmt->execute()) {
                    error_log("✅ Orphanage record created");
                } else {
                    error_log("⚠️ Orphanage record creation failed (but user was created)");
                }
            }
            
            // Set session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;
            $_SESSION['logged_in'] = true;
            
            error_log("✅ Registration complete - session set");
            
            // Return success for AJAX or redirect
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                echo json_encode([
                    'success' => true,
                    'message' => 'Registration successful!',
                    'redirect' => 'dashboard.php'
                ]);
            } else {
                header("Location: dashboard.php");
                exit;
            }
            
        } else {
            throw new Exception("Failed to create user account in database.");
        }
        
    } catch (Exception $e) {
        error_log("❌ Registration error: " . $e->getMessage());
        
        // Store error for form redisplay
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['form_data'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => $role
        ];
        
        // Return error for AJAX or redirect
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } else {
            header("Location: ../../register.php");
            exit;
        }
    }
    
} else {
    header("Location: ../../register.php");
    exit;
}

error_log("=== REGISTRATION PROCESS COMPLETE ===");
?>