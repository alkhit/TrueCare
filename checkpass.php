<?php
echo "<h2>Simple Password Test</h2>";

// Test different passwords with the common hash
$passwords_to_test = [
    '123456',
    'password123', 
    'test123',
    'password',
    'admin123'
];

$common_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "<p>Testing common hash: $common_hash</p>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Password</th><th>Result</th></tr>";

foreach ($passwords_to_test as $pwd) {
    $result = password_verify($pwd, $common_hash) ? '✅ WORKS' : '❌ FAILS';
    echo "<tr><td>$pwd</td><td>$result</td></tr>";
}
echo "</table>";
?>