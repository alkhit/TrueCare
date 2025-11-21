<?php
session_start();
include 'includes/config.php';

echo "<h2>Login Debug Information</h2>";

// Test database connection
try {
    $testQuery = $db->query("SELECT COUNT(*) as user_count FROM users");
    $result = $testQuery->fetch(PDO::FETCH_ASSOC);
    echo "<p>✅ Database connected. Users in database: " . $result['user_count'] . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

// Show all users and their password hashes
echo "<h3>Users in Database:</h3>";
try {
    $usersQuery = $db->query("SELECT user_id, name, email, role, password FROM users");
    $users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Password Hash</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['user_id'] . "</td>";
        echo "<td>" . $user['name'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . $user['role'] . "</td>";
        echo "<td style='font-size: 10px;'>" . $user['password'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p>Error fetching users: " . $e->getMessage() . "</p>";
}

// Test password verification
echo "<h3>Password Verification Test:</h3>";
$test_passwords = [
    'admin123',
    'orphanage123', 
    'donor123',
    'wrongpassword'
];

foreach ($test_passwords as $test_pwd) {
    $hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
    $result = password_verify($test_pwd, $hash) ? '✅' : '❌';
    echo "<p>$result '$test_pwd' verifies with admin hash: " . ($result ? 'YES' : 'NO') . "</p>";
}

echo "<hr>";
echo "<p><a href='login.php'>Go to Login</a> | <a href='register.php'>Go to Register</a></p>";
?>