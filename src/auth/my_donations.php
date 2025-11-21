<?php
session_start();
checkAuth('donor');

include '../../includes/config.php';
$page_title = "My Donations - TrueCare";
include '../../includes/header.php';

$user_id = $_SESSION['user_id'];

// Fetch donations
try {
    $donationsQuery = $db->prepare("
        SELECT d.*, c.title as campaign_title, c.image_url, o.name as orphanage_name
        FROM donations d
        LEFT JOIN campaigns c ON d.campaign_id = c.campaign_id
        LEFT JOIN orphanages o ON c.orphanage_id = o.orphanage_id
        WHERE d.user_id = :user_id
        ORDER BY d.donation_date DESC
        LIMIT 50
    ");
    $donationsQuery->bindParam(':user_id', $user_id);
    $donationsQuery->execute();
    $donations = $donationsQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $donations = [];
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <?php include 'sidebar.php'; ?>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-donate me-2"></i>My Donations
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../campaigns/campaigns.php" class="btn btn-success btn-sm">
                        <i class="fas fa-hand-holding-heart me-1"></i>Support Another Campaign
                    </a>
                </div>
            </div>

            <!-- Donation Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Total Donated</h6>
                            <h3>
                                <?php
                                $total = array_sum(array_column($donations, 'amount'));
                                echo formatCurrency($total);
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Total Donations</h6>
                            <h3><?php echo count($donations); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Campaigns Supported</h6>
                            <h3>
                                <?php
                                $unique_campaigns = array_unique(array_column($donations, 'campaign_id'));
                                echo count($unique_campaigns);
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Last Donation</h6>
                            <h6>
                                <?php
                                if (!empty($donations)) {
                                    $last_date = date('M j, Y', strtotime($donations[0]['donation_date']));
                                    echo $last_date;
                                } else {
                                    echo 'Never';
                                }
                                ?>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donations Table -->
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Donation History</h6>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary active">All</button>
                        <button class="btn btn-sm btn-outline-secondary">This Month</button>
                        <button class="btn btn-sm btn-outline-secondary">This Year</button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($donations)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="donationsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Campaign</th>
                                    <th>Orphanage</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($donations as $donation): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../../assets/images/<?php echo !empty($donation['image_url']) ? $donation['image_url'] : 'campaign1.jpg'; ?>" 
                                                 class="rounded me-3" width="40" height="40" style="object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 small"><?php echo htmlspecialchars($donation['campaign_title'] ?? 'Unknown Campaign'); ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($donation['orphanage_name'] ?? 'Unknown Orphanage'); ?></td>
                                    <td class="fw-bold text-success"><?php echo formatCurrency($donation['amount']); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark text-capitalize">
                                            <?php echo $donation['payment_method'] ?? 'mpesa'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $donation['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($donation['status'] ?? 'pending'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($donation['donation_date'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-success" title="Download Receipt">
                                                <i class="fas fa-receipt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-donate fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Donations Yet</h4>
                        <p class="text-muted">You haven't made any donations yet. Start supporting orphanages today!</p>
                        <a href="../campaigns/campaigns.php" class="btn btn-success btn-lg">
                            <i class="fas fa-hand-holding-heart me-2"></i>Browse Campaigns
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Impact Summary -->
            <?php if (!empty($donations)): ?>
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Donation Impact</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="border rounded p-3">
                                        <h4 class="text-success mb-0"><?php echo count($unique_campaigns); ?></h4>
                                        <small class="text-muted">Campaigns</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3">
                                        <h4 class="text-primary mb-0"><?php echo count($donations); ?></h4>
                                        <small class="text-muted">Donations</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3">
                                        <h4 class="text-info mb-0"><?php echo count(array_unique(array_column($donations, 'orphanage_name'))); ?></h4>
                                        <small class="text-muted">Orphanages</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Payment Methods</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="paymentMethodChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php if (!empty($donations)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment Method Chart
    const paymentData = {
        mpesa: <?php echo count(array_filter($donations, function($d) { return ($d['payment_method'] ?? 'mpesa') === 'mpesa'; })); ?>,
        card: <?php echo count(array_filter($donations, function($d) { return ($d['payment_method'] ?? '') === 'card'; })); ?>,
        paypal: <?php echo count(array_filter($donations, function($d) { return ($d['payment_method'] ?? '') === 'paypal'; })); ?>
    };

    const ctx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['M-Pesa', 'Card', 'PayPal'],
            datasets: [{
                data: [paymentData.mpesa, paymentData.card, paymentData.paypal],
                backgroundColor: ['#28a745', '#007bff', '#003087'],
                hoverBackgroundColor: ['#218838', '#0056b3', '#002f65'],
            }],
        },
        options: {
            maintainAspectRatio: false,
            legend: {
                position: 'bottom'
            }
        },
    });
});
</script>
<?php endif; ?>

<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}
</style>

<?php include '../../includes/footer.php'; ?>