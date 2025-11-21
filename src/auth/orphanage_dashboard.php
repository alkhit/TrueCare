<?php
// This file is included in dashboard.php for orphanage users
$user_id = $_SESSION['user_id'];

// Fetch orphanage stats (mock data for now)
try {
    $campaignStats = $db->prepare("
        SELECT 
            COUNT(*) as total_campaigns,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active_campaigns,
            COALESCE(SUM(target_amount), 0) as total_goal,
            COALESCE(SUM(current_amount), 0) as total_raised
        FROM campaigns 
        WHERE orphanage_id IN (SELECT orphanage_id FROM orphanages WHERE user_id = :user_id)
    ");
    $campaignStats->bindParam(':user_id', $user_id);
    $campaignStats->execute();
    $stats = $campaignStats->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $stats = ['total_campaigns' => 0, 'active_campaigns' => 0, 'total_goal' => 0, 'total_raised' => 0];
}

// Check verification status
try {
    $verificationStmt = $db->prepare("
        SELECT status FROM orphanages WHERE user_id = :user_id
    ");
    $verificationStmt->bindParam(':user_id', $user_id);
    $verificationStmt->execute();
    $orphanage = $verificationStmt->fetch(PDO::FETCH_ASSOC);
    $verification_status = $orphanage['status'] ?? 'pending';
} catch (Exception $e) {
    $verification_status = 'pending';
}
?>

<!-- Stats Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Raised</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo formatCurrency($stats['total_raised']); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Active Campaigns</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['active_campaigns']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Campaigns</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_campaigns']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Verification</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800 text-capitalize">
                            <?php echo $verification_status; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verification Alert -->
<?php if ($verification_status === 'pending'): ?>
<div class="alert alert-warning">
    <h6><i class="fas fa-clock me-2"></i>Verification Pending</h6>
    <p class="mb-0">
        Your orphanage registration is under review. You can still create campaigns, but they will need approval before going live.
        <a href="profile.php" class="alert-link">Complete your profile</a> to speed up verification.
    </p>
</div>
<?php elseif ($verification_status === 'rejected'): ?>
<div class="alert alert-danger">
    <h6><i class="fas fa-times-circle me-2"></i>Verification Rejected</h6>
    <p class="mb-0">
        Your orphanage registration was not approved. Please check your email for details or 
        <a href="#" class="alert-link">contact support</a> for more information.
    </p>
</div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="../campaigns/create_campaign.php" class="btn btn-primary btn-circle btn-xl">
                            <i class="fas fa-plus fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Create Campaign</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="../campaigns/my_campaigns.php" class="btn btn-success btn-circle btn-xl">
                            <i class="fas fa-list fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>My Campaigns</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="profile.php" class="btn btn-info btn-circle btn-xl">
                            <i class="fas fa-edit fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Update Profile</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="../donations/donations_received.php" class="btn btn-warning btn-circle btn-xl">
                            <i class="fas fa-donate fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>View Donations</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Campaign Progress -->
<div class="row">
    <div class="col-lg-8">
        <!-- Campaign Progress -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Campaign Progress</h6>
                <a href="../campaigns/my_campaigns.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if ($stats['total_campaigns'] > 0): ?>
                <div class="campaign-progress">
                    <?php for($i = 1; $i <= min(3, $stats['total_campaigns']); $i++): ?>
                    <div class="campaign-item mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="mb-0">Education Support Campaign #<?php echo $i; ?></h6>
                            <span class="badge bg-<?php echo $i === 1 ? 'success' : 'warning'; ?>">
                                <?php echo $i === 1 ? 'Active' : 'Pending'; ?>
                            </span>
                        </div>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: <?php echo rand(20, 80); ?>%"></div>
                        </div>
                        <div class="d-flex justify-content-between text-sm text-muted">
                            <span>Ksh <?php echo number_format(rand(20000, 80000)); ?> raised</span>
                            <span><?php echo rand(20, 80); ?>% funded</span>
                            <span><?php echo rand(5, 30); ?> days left</span>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-hand-holding-heart fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No campaigns yet</p>
                    <small>Create your first campaign to start receiving donations</small>
                    <div class="mt-3">
                        <a href="../campaigns/create_campaign.php" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i>Create Campaign
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Recent Donations -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Donations</h6>
            </div>
            <div class="card-body">
                <?php if ($stats['total_raised'] > 0): ?>
                <div class="donation-list">
                    <?php for($i = 1; $i <= 3; $i++): ?>
                    <div class="donation-item mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Ksh <?php echo number_format(rand(500, 5000)); ?></h6>
                                <small class="text-muted">From Anonymous Donor</small>
                            </div>
                            <small class="text-muted"><?php echo rand(1, 7); ?> days ago</small>
                        </div>
                        <small class="text-success">Education Support Campaign</small>
                    </div>
                    <?php endfor; ?>
                </div>
                <div class="text-center mt-3">
                    <a href="../donations/donations_received.php" class="btn btn-outline-primary btn-sm">
                        View All Donations
                    </a>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-donate fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No donations yet</p>
                    <small>When you receive donations, they will appear here</small>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="card shadow mt-4">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 font-weight-bold">Campaign Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        <small>Use compelling images and stories</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-bullhorn text-success me-2"></i>
                        <small>Share your campaign on social media</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-sync-alt text-primary me-2"></i>
                        <small>Update donors regularly</small>
                    </li>
                    <li>
                        <i class="fas fa-thumbs-up text-info me-2"></i>
                        <small>Show appreciation to donors</small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.btn-circle {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-circle.btn-xl {
    width: 70px;
    height: 70px;
    padding: 10px 16px;
    border-radius: 35px;
    font-size: 24px;
    line-height: 1.33;
}
.campaign-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}
.donation-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
</style>