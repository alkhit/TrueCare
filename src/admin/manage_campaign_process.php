<?php
session_start();
require_once '../../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['campaign_id'], $_POST['action'])) {
    $campaign_id = intval($_POST['campaign_id']);
    $action = $_POST['action'];

    if ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM campaigns WHERE campaign_id = ?");
        $stmt->execute([$campaign_id]);
        $_SESSION['success'] = "Campaign deleted.";
    } elseif ($action === 'edit') {
        // For demo, just set a success message. Implement edit logic as needed.
        $_SESSION['success'] = "Edit campaign feature coming soon.";
    }
    header("Location: manage_campaigns.php");
    exit;
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: manage_campaigns.php");
    exit;
}
?>
