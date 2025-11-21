<?php
echo "<h2>Path Debug Information</h2>";

// Check current directory
echo "<h3>Current Directory</h3>";
echo "Current file: " . __FILE__ . "<br>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

// Check if files exist
echo "<h3>File Existence Check</h3>";
$files_to_check = [
    'includes/config.php',
    'src/auth/register_process.php',
    'register.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;Full path: " . realpath($file) . "<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Check BASE_URL
echo "<h3>URL Information</h3>";
include 'includes/config.php';
echo "BASE_URL: " . BASE_URL . "<br>";
echo "Expected register process URL: " . BASE_URL . "/src/auth/register_process.php<br>";

// Test the actual URL
echo "<h3>Test Links</h3>";
echo '<a href="' . BASE_URL . '/src/auth/register_process.php">Test Register Process URL</a><br>';
echo '<a href="' . BASE_URL . '/register.php">Test Register Page</a><br>';
?>