
<?php
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/auth.php';
// Check if user is logged in and is an orphanage
if (!isLoggedIn() || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'orphanage') {
    echo '<div class="alert alert-danger">You must be logged in as an orphanage to view this page.</div>';
    return;
}
// Dashboard data
$data = $dashboard_data ?? [];
$total_raised = $data['total_raised'] ?? 0;
$active_campaigns = $data['active_campaigns'] ?? 0;
$total_campaigns = $data['total_campaigns'] ?? 0;
$verification_status = $data['verification_status'] ?? 'pending';
?>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">Total Raised</h6>
                <p class="card-text fw-bold text-success"><?php echo formatCurrency($total_raised); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">Active Campaigns</h6>
                <p class="card-text fw-bold text-primary"><?php echo $active_campaigns; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">Total Campaigns</h6>
                <p class="card-text fw-bold text-info"><?php echo $total_campaigns; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">Verification</h6>
                <p class="card-text fw-bold text-warning text-capitalize"><?php echo $verification_status; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-center gap-4 py-2">
                    <div class="text-center">
                        <a href="../campaigns/create_campaign.php" class="btn btn-primary btn-circle btn-xl">
                            <i class="fas fa-plus fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Create Campaign</small>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="../campaigns/my_campaigns.php" class="btn btn-success btn-circle btn-xl">
                            <i class="fas fa-list fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>My Campaigns</small>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="profile.php" class="btn btn-info btn-circle btn-xl">
                            <i class="fas fa-edit fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Update Profile</small>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="../donations/orphanage_donations.php" class="btn btn-warning btn-circle btn-xl">
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