<?php
session_start();
require_once '../../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['new_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['new_role'];
    $current_admin_id = $_SESSION['user_id'];

    if ($user_id === $current_admin_id) {
        $_SESSION['error'] = "You cannot change your own role.";
        header("Location: manage_users.php");
        exit;
    }

    if (!in_array($new_role, ['admin', 'donor', 'orphanage'])) {
        $_SESSION['error'] = "Invalid role selected.";
        header("Location: manage_users.php");
        exit;
    }

    $stmt = $db->prepare("UPDATE users SET role = ? WHERE user_id = ?");
    $stmt->execute([$new_role, $user_id]);
    $_SESSION['success'] = "User role updated.";
    header("Location: manage_users.php");
    exit;
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: manage_users.php");
    exit;
}
?>
