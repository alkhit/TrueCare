<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with absolute path
header("Location: /TrueCare/login.php");
exit;
?>