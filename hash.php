<?php
// generate_hash.php
$passwords = [
    'admin123',
    'password123',
    'truecare2024'
];

foreach ($passwords as $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "Password: <strong>{$password}</strong><br>";
    echo "Hash: <strong>{$hash}</strong><br>";
    echo "SQL: INSERT INTO users (name, email, password, role, phone, is_active) VALUES ('Admin User', 'admin@example.com', '{$hash}', 'admin', '0700000000', 1);<br><br>";
    
    // Verify the hash works
    if (password_verify($password, $hash)) {
        echo "✓ Hash verification: SUCCESS<br><br>";
    } else {
        echo "✗ Hash verification: FAILED<br><br>";
    }
}
?>