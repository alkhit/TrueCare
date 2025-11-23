<?php
session_start();
require_once '../../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $action = $_POST['action'];
    $current_admin_id = $_SESSION['user_id'];

    if ($user_id === $current_admin_id) {
        $_SESSION['error'] = "You cannot edit or deactivate yourself.";
        header("Location: manage_users.php");
        exit;
    }

    if ($action === 'deactivate') {
        $stmt = $db->prepare("UPDATE users SET is_active = 0 WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $_SESSION['success'] = "User deactivated.";
        } elseif ($action === 'delete') {
            // Delete user and related orphanage/campaigns if needed
            $stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            // Optionally, delete orphanage and campaigns for this user
            $stmt = $db->prepare("DELETE FROM orphanages WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $stmt = $db->prepare("DELETE FROM campaigns WHERE orphanage_id IN (SELECT orphanage_id FROM orphanages WHERE user_id = ?)");
            $stmt->execute([$user_id]);
            $_SESSION['success'] = "User deleted.";
    } elseif ($action === 'edit') {
        // For demo, just set a success message. Implement edit logic as needed.
        $_SESSION['success'] = "Edit user feature coming soon.";
    }
    header("Location: manage_users.php");
    exit;
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: manage_users.php");
    exit;
}
?>
