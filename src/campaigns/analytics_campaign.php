<?php
require_once '../../includes/functions.php';
require_once '../../includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
checkAuth('orphanage');

$campaign_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$campaign_id) {
    echo showAlert('danger', 'Invalid campaign ID.');
    exit;
}

// Get orphanage_id for this user
$stmt = $db->prepare('SELECT orphanage_id FROM orphanages WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$orphanage = $stmt->fetch(PDO::FETCH_ASSOC);
$orphanage_id = $orphanage['orphanage_id'] ?? null;

if (!$orphanage_id) {
    echo showAlert('danger', 'No orphanage found for this user.');
    exit;
}

// Fetch campaign analytics (total donations, donors, progress)
$stmt = $db->prepare('SELECT c.*, COALESCE(SUM(d.amount),0) as total_raised, COUNT(DISTINCT d.user_id) as total_donors FROM campaigns c LEFT JOIN donations d ON c.campaign_id = d.campaign_id WHERE c.campaign_id = :id AND c.orphanage_id = :orphanage_id GROUP BY c.campaign_id');
$stmt->bindParam(':id', $campaign_id);
$stmt->bindParam(':orphanage_id', $orphanage_id);
$stmt->execute();
$campaign = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$campaign) {
    echo showAlert('danger', 'Campaign not found or access denied.');
    exit;
}
?>
<div class="container mt-5">
    <h2>Campaign Analytics: <?php echo e($campaign['title']); ?></h2>
    <ul class="list-group mb-3">
        <li class="list-group-item">Total Raised: <?php echo formatCurrency($campaign['total_raised']); ?></li>
        <li class="list-group-item">Total Donors: <?php echo $campaign['total_donors']; ?></li>
        <li class="list-group-item">Goal: <?php echo formatCurrency($campaign['target_amount']); ?></li>
        <li class="list-group-item">Status: <?php echo ucfirst($campaign['status']); ?></li>
    </ul>
    <a href="my_campaigns.php" class="btn btn-secondary">Back to My Campaigns</a>
</div>
