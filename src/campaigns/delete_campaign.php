<?php
require_once '../../includes/functions.php';
require_once '../../includes/config.php';
// Ensure $db is defined
if (!isset($db)) {
    $db = get_db();
}
session_start();
checkAuth('orphanage');

$campaign_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$campaign_id) {
    echo showAlert('danger', 'Invalid campaign ID.');
    exit;
}

// Only allow deletion if campaign belongs to this orphanage
$stmt = $db->prepare('DELETE FROM campaigns WHERE campaign_id = :id AND orphanage_id IN (SELECT orphanage_id FROM orphanages WHERE user_id = :user_id)');
$stmt->bindParam(':id', $campaign_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();

// Redirect back to campaigns list
header('Location: my_campaigns.php');
exit;
