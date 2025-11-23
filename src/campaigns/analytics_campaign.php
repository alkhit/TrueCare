<?php
require_once '../../includes/functions.php';
require_once '../../includes/config.php';
// Ensure $db is defined
if (!isset($db)) {
    $db = get_db();
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../includes/header.php';

// Show top navbar for authenticated users (not admin)
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] !== 'admin') {
    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 rounded shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="../auth/dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbar" aria-controls="userNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="userNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="analytics_campaign.php"><i class="fas fa-chart-line me-1"></i>Analytics</a></li>
                    <li class="nav-item"><a class="nav-link" href="create_campaign.php"><i class="fas fa-plus-circle me-1"></i>Create Campaign</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_campaigns.php"><i class="fas fa-list me-1"></i>My Campaigns</a></li>
                </ul>
            </div>
        </div>
    </nav>';
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar removed for orphanage pages -->
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Campaign Analytics</h1>
            </div>
            <?php
            $campaign_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                $stats = [
                    'title' => 'N/A',
                    'total_raised' => 0,
                    'total_donors' => 0,
                    'target_amount' => 0,
                    'status' => 'N/A'
                ];
                if ($campaign_id > 0) {
                    $stmt = $db->prepare('SELECT orphanage_id FROM orphanages WHERE user_id = :user_id');
                    $stmt->bindParam(':user_id', $_SESSION['user_id']);
                    $stmt->execute();
                    $orphanage = $stmt->fetch(PDO::FETCH_ASSOC);
                    $orphanage_id = $orphanage['orphanage_id'] ?? null;
                    if ($orphanage_id) {
                        $stmt = $db->prepare('SELECT c.*, COALESCE(SUM(d.amount),0) as total_raised, COUNT(DISTINCT d.user_id) as total_donors FROM campaigns c LEFT JOIN donations d ON c.campaign_id = d.campaign_id WHERE c.campaign_id = :id AND c.orphanage_id = :orphanage_id GROUP BY c.campaign_id');
                        $stmt->bindParam(':id', $campaign_id);
                        $stmt->bindParam(':orphanage_id', $orphanage_id);
                        $stmt->execute();
                        $campaign = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($campaign) {
                            $stats['title'] = $campaign['title'] ?? 'N/A';
                            $stats['total_raised'] = $campaign['total_raised'] ?? 0;
                            $stats['total_donors'] = $campaign['total_donors'] ?? 0;
                            $stats['target_amount'] = $campaign['target_amount'] ?? 0;
                            $stats['status'] = $campaign['status'] ?? 'N/A';
                        }
                    }
                }
            ?>
            <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Analytics for: <?php echo htmlspecialchars($stats['title']); ?></h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group mb-3">
                            <li class="list-group-item">Total Raised: <?php echo formatCurrency($stats['total_raised']); ?></li>
                            <li class="list-group-item">Total Donors: <?php echo $stats['total_donors']; ?></li>
                            <li class="list-group-item">Goal: <?php echo formatCurrency($stats['target_amount']); ?></li>
                            <li class="list-group-item">Status: <?php echo ucfirst($stats['status']); ?></li>
                        </ul>
                        <a href="my_campaigns.php" class="btn btn-secondary">Back to My Campaigns</a>
                    </div>
            </div>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
