<?php
require_once __DIR__ . '/../../includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Unset all session variables
$_SESSION = [];

// Delete the session cookie
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params['path'], $params['domain'],
		$params['secure'], $params['httponly']
	);
}

// Destroy the session
session_destroy();

// Regenerate session ID for security
session_regenerate_id(true);


// Redirect to index page (absolute path)
// Always redirect to root index.php
$redirect_url = '/TrueCare/index.php';
if (!headers_sent()) {
	header('Location: ' . $redirect_url);
	exit;
} else {
	echo '<script>window.location.href = "' . $redirect_url . '";</script>';
	echo '<noscript><meta http-equiv="refresh" content="0;url=' . $redirect_url . '" /></noscript>';
	exit;
}
?>