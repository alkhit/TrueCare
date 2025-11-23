
<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orphanage_id'], $_POST['action'])) {
    $orphanage_id = intval($_POST['orphanage_id']);
    $action = $_POST['action'];
    if ($action === 'approve') {
        $stmt = $db->prepare("UPDATE orphanages SET status = 'verified' WHERE orphanage_id = ?");
        $stmt->execute([$orphanage_id]);
        $_SESSION['success'] = "Orphanage approved.";
    } elseif ($action === 'reject') {
        $stmt = $db->prepare("UPDATE orphanages SET status = 'rejected' WHERE orphanage_id = ?");
        $stmt->execute([$orphanage_id]);
        $_SESSION['success'] = "Orphanage rejected.";
    }
    header("Location: verify_orphanages.php");
    exit;
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: verify_orphanages.php");
    exit;
}


