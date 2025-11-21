<?php
// Database configuration
$host = 'localhost';
$dbname = 'truecare_portal';
$username = 'root';
$password = '';
$port = '3306';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Check</h2>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Users table exists<br>";
        
        // Check admin user
        $admin_stmt = $pdo->query("SELECT * FROM users WHERE email = 'admin@truecare.org'");
        $admin = $admin_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            echo "✅ Admin user exists<br>";
            echo "Admin ID: " . $admin['user_id'] . "<br>";
            echo "Admin Name: " . $admin['name'] . "<br>";
            echo "Admin Email: " . $admin['email'] . "<br>";
            echo "Admin Role: " . $admin['role'] . "<br>";
            echo "Admin Password Hash: " . $admin['password'] . "<br>";
            
            // Test password verification
            $test_password = 'admin123';
            if (password_verify($test_password, $admin['password'])) {
                echo "✅ Password 'admin123' verifies correctly<br>";
            } else {
                echo "❌ Password 'admin123' does NOT verify<br>";
                
                // Let's see what the actual hash is
                $new_hash = password_hash($test_password, PASSWORD_DEFAULT);
                echo "New hash for 'admin123': " . $new_hash . "<br>";
            }
        } else {
            echo "❌ Admin user not found<br>";
        }
    } else {
        echo "❌ Users table does not exist<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}
?>