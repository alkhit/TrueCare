<?php
echo "<h2>Database Connection Test</h2>";

try {
    include 'includes/config.php';
    echo "<p style='color: green;'>✅ Database config loaded successfully</p>";
    
    // Test query
    $test = $db->query("SELECT 1");
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // Check if users table exists and has data
    $users = $db->query("SELECT COUNT(*) as count FROM users")->fetch();
    echo "<p style='color: green;'>✅ Users table exists with {$users['count']} users</p>";
    
    // Show all users
    $all_users = $db->query("SELECT user_id, name, email, role FROM users")->fetchAll();
    echo "<h3>Current Users:</h3>";
    echo "<ul>";
    foreach ($all_users as $user) {
        echo "<li>{$user['name']} ({$user['email']}) - {$user['role']}</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>