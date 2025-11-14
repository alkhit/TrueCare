<?php
// Simple debug script
echo "<h1>Debug Information</h1>";

// Check if config loads
echo "<h3>1. Checking Config</h3>";
try {
    include 'includes/config.php';
    echo "✅ Config loaded successfully<br>";
    echo "BASE_URL: " . BASE_URL . "<br>";
} catch (Exception $e) {
    echo "❌ Config error: " . $e->getMessage() . "<br>";
}

// Check session
echo "<h3>2. Checking Session</h3>";
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . session_status() . "<br>";

// Check JavaScript loading
echo "<h3>3. JavaScript Test</h3>";
?>
<script>
console.log('JavaScript is working!');
document.write('✅ JavaScript is executing<br>');

// Test form submission
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    const testForm = document.createElement('form');
    testForm.innerHTML = '<button type="submit">Test Button</button>';
    testForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted!');
        alert('Form submission works!');
    });
    document.body.appendChild(testForm);
});
</script>

<?php
// Check file paths
echo "<h3>4. File Paths</h3>";
$files_to_check = [
    'includes/config.php',
    'assets/js/app.js',
    'src/auth/login_process.php',
    'src/auth/register_process.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}
?>