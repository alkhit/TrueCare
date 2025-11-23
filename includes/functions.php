<?php

// Check if user is logged in
function isLoggedIn() {
	return isset($_SESSION['user_id']);
}

// Get user role from session
function getUserRole() {
	return $_SESSION['user_role'] ?? null;
}

// Check authentication and redirect if not logged in
function checkAuth($required_role = null) {
	if (!isLoggedIn()) {
		$_SESSION['error'] = "Please log in to access this page.";
		header("Location: " . BASE_URL . "/login.php");
		exit;
	}
	if ($required_role && getUserRole() !== $required_role) {
		$_SESSION['error'] = "You don't have permission to access that page.";
		header("Location: " . BASE_URL . "/src/auth/dashboard.php");
		exit;
	}
}

// Redirect to specified URL
function redirect($url) {
	header("Location: $url");
	exit;
}

// Sanitize input data
function sanitizeInput($data) {
	return htmlspecialchars(strip_tags(trim($data)));
}

// Format currency for display
function formatCurrency($amount) {
	if (!is_numeric($amount)) {
		$amount = 0;
	}
	return 'Ksh ' . number_format($amount);
}

// Show alert message
function showAlert($type, $message) {
	return '<div class="alert alert-' . $type . ' alert-dismissible fade show">'
		. '<i class="fas fa-' . ($type === 'success' ? 'check-circle' : 'exclamation-triangle') . ' me-2"></i>'
		. $message
		. '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
		. '</div>';
}

// Generate URL with base path
function url($path = '') {
	$base_url = BASE_URL;
	$path = ltrim($path, '/');
	return $base_url . '/' . $path;
}

// Absolute path helper for your project structure
function abs_path($path = '') {
	$path = ltrim($path, '/');
	if (strpos($path, 'TrueCare/') === 0) {
		return '/' . $path;
	} else {
		return '/TrueCare/' . $path;
	}
}
// Simple HTML escape function for output
function e($string) {
	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Get PDO database connection
function get_db() {
	// Use global $database if available
	global $database;
	if (isset($database) && $database instanceof Database) {
		return $database->getConnection();
	}
	// Fallback: create new Database instance
	require_once __DIR__ . '/config.php';
	$database = new Database();
	return $database->getConnection();
}

?>