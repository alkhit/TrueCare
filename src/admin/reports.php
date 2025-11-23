<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: ../../login.php");
        exit;
}
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
include '../../includes/header.php';

$user_role = $_SESSION['user_role'] ?? '';
if ($user_role === 'admin') {
        echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 rounded shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="admin.php"><i class="fas fa-cog me-2"></i>Admin Panel</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="verify_orphanages.php"><i class="fas fa-clipboard-check me-1"></i>Verify Orphanages</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_users.php"><i class="fas fa-users-cog me-1"></i>Manage Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_campaigns.php"><i class="fas fa-hand-holding-heart me-1"></i>Manage Campaigns</a></li>
                        <li class="nav-item"><a class="nav-link" href="reports.php"><i class="fas fa-chart-bar me-1"></i>Reports</a></li>
                    </ul>
                </div>
            </div>
        </nav>';
}
// Donation stats
$donationStats = $db->query("SELECT COUNT(*) as total, COALESCE(SUM(amount),0) as total_amount FROM donations WHERE status = 'completed'")->fetch(PDO::FETCH_ASSOC);
// Campaign performance
$campaignStats = $db->query("SELECT COUNT(*) as total, COALESCE(SUM(current_amount),0) as raised FROM campaigns WHERE status = 'active'")->fetch(PDO::FETCH_ASSOC);
// User activity
$userStats = $db->query("SELECT COUNT(*) as total FROM users")->fetch(PDO::FETCH_ASSOC);
?>
<div class="container py-5" id="report-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Reports & Analytics</h2>
        <button class="btn btn-outline-primary" onclick="window.print()"><i class="fas fa-print me-1"></i> Print Details</button>
    </div>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Donations</h5>
                    <p class="card-text">Ksh <?php echo number_format($donationStats['total_amount']); ?> (<?php echo $donationStats['total']; ?> donations)</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title">Active Campaigns</h5>
                    <p class="card-text">Ksh <?php echo number_format($campaignStats['raised']); ?> raised (<?php echo $campaignStats['total']; ?> campaigns)</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $userStats['total']; ?> users</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header">Recent Donations</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead><tr><th>Date</th><th>User</th><th>Amount</th><th>Campaign</th></tr></thead>
                <tbody>
                <?php
                $recent = $db->query("SELECT d.donation_date, u.name as user, d.amount, c.title as campaign FROM donations d LEFT JOIN users u ON d.user_id = u.user_id LEFT JOIN campaigns c ON d.campaign_id = c.campaign_id WHERE d.status = 'completed' ORDER BY d.donation_date DESC LIMIT 10");
                foreach ($recent as $row) {
                    echo '<tr><td>'.date('Y-m-d',strtotime($row['donation_date'])).'</td><td>'.htmlspecialchars($row['user']).'</td><td>Ksh '.number_format($row['amount']).'</td><td>'.htmlspecialchars($row['campaign']).'</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #report-content, #report-content * {
        visibility: visible !important;
    }
    #report-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 100vw;
        background: #fff;
        z-index: 9999;
        padding: 0;
        margin: 0;
    }
    .btn, .navbar, footer, header {
        display: none !important;
    }
}
</style>
<?php include '../../includes/footer.php'; ?>
