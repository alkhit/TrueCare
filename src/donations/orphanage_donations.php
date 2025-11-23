
<?php
require_once __DIR__ . '/../../includes/functions.php';
session_start();
require_once __DIR__ . '/../../includes/config.php';
checkAuth('orphanage');

$user_id = $_SESSION['user_id'];

// Get orphanage_id for this user
$stmt = $db->prepare('SELECT orphanage_id FROM orphanages WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$orphanage = $stmt->fetch(PDO::FETCH_ASSOC);
$orphanage_id = $orphanage['orphanage_id'] ?? null;

// Fetch donations for campaigns owned by this orphanage
$donations = [];
if ($orphanage_id) {
    $donationsStmt = $db->prepare('
        SELECT d.*, c.title AS campaign_title, u.name AS donor_name
        FROM donations d
        JOIN campaigns c ON d.campaign_id = c.campaign_id
        JOIN users u ON d.user_id = u.user_id
        WHERE c.orphanage_id = :orphanage_id AND d.status = "completed"
        ORDER BY d.donation_date DESC
    ');
    $donationsStmt->bindParam(':orphanage_id', $orphanage_id);
    $donationsStmt->execute();
    $donations = $donationsStmt->fetchAll(PDO::FETCH_ASSOC);
}

include '../../includes/header.php';
?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-none d-md-block">
            <?php include '../auth/sidebar.php'; ?>
        </div>
        <main class="col-12 col-md-9 col-lg-10 px-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h4 class="m-0 fw-bold text-primary">Donations Received</h4>
                </div>
                <div class="card-body">
                    <?php if (!$orphanage_id): ?>
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-circle me-2"></i>No orphanage registered</h6>
                            <p class="mb-0">You haven't registered an orphanage yet, so you cannot receive donations.<br>
                            <a href="<?php echo abs_path('src/auth/profile.php'); ?>" class="alert-link">Register your orphanage</a> to start receiving donations.</p>
                        </div>
                    <?php elseif (count($donations) === 0): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-donate fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No donations received yet</h6>
                            <small>When you receive donations, they will appear here.</small>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Donor</th>
                                        <th>Campaign</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($donations as $donation): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($donation['donor_name']); ?></td>
                                            <td><?php echo htmlspecialchars($donation['campaign_title']); ?></td>
                                            <td><?php echo formatCurrency($donation['amount']); ?></td>
                                            <td><?php echo date('d M Y', strtotime($donation['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
