<?php
// Simple test registration script
session_start();
include 'includes/config.php';

echo "<h2>Direct Registration Test</h2>";

try {
    // Test data
    $test_name = "Test User " . rand(1000, 9999);
    $test_email = "test" . rand(1000, 9999) . "@example.com";
    $test_password = "password123";
    $test_role = "donor";
    
    echo "Testing registration with:<br>";
    echo "Name: $test_name<br>";
    echo "Email: $test_email<br>";
    echo "Role: $test_role<br><br>";
    
    // Check if email exists
    $check_query = "SELECT user_id FROM users WHERE email = :email";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':email', $test_email);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        echo "❌ Email already exists (unexpected)<br>";
    } else {
        // Create user
        $hashed_password = password_hash($test_password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $insert_stmt = $db->prepare($insert_query);
        $insert_stmt->bindParam(':name', $test_name);
        $insert_stmt->bindParam(':email', $test_email);
        $insert_stmt->bindParam(':password', $hashed_password);
        $insert_stmt->bindParam(':role', $test_role);
        
        if ($insert_stmt->execute()) {
            echo "✅ Registration successful!<br>";
            echo "User ID: " . $db->lastInsertId() . "<br>";
        } else {
            echo "❌ Registration failed<br>";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='register.php'>Back to Registration Page</a>";
?>