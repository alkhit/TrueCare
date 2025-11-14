<?php
include '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $phone = trim($_POST['phone'] ?? '');
    
    header('Content-Type: application/json');
    
    // Validation
    if ($password !== $confirm_password) {
        echo json_encode([
            'success' => false,
            'message' => 'Passwords do not match.'
        ]);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode([
            'success' => false,
            'message' => 'Password must be at least 6 characters long.'
        ]);
        exit;
    }
    
    try {
        // Check if email already exists
        $check_query = "SELECT user_id FROM users WHERE email = :email";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':email', $email);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Email already registered. Please use a different email or login.'
            ]);
            exit;
        }
        
        // Insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO users (name, email, password, role, phone) 
                         VALUES (:name, :email, :password, :role, :phone)";
        $insert_stmt = $db->prepare($insert_query);
        $insert_stmt->bindParam(':name', $name);
        $insert_stmt->bindParam(':email', $email);
        $insert_stmt->bindParam(':password', $hashed_password);
        $insert_stmt->bindParam(':role', $role);
        $insert_stmt->bindParam(':phone', $phone);
        
        if ($insert_stmt->execute()) {
            $user_id = $db->lastInsertId();
            
            // If registering as orphanage, create orphanage record
            if ($role === 'orphanage') {
                $orphanage_query = "INSERT INTO orphanages (user_id, name, location, status) 
                                   VALUES (:user_id, :name, '', 'pending')";
                $orphanage_stmt = $db->prepare($orphanage_query);
                $orphanage_stmt->bindParam(':user_id', $user_id);
                $orphanage_stmt->bindParam(':name', $name);
                $orphanage_stmt->execute();
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Account created successfully! Redirecting to login...',
                'redirect' => BASE_URL . '/login.php'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ]);
        }
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'System error. Please try again later.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>