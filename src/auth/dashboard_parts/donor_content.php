require_once __DIR__ . '/../../../includes/functions.php';
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        if (!is_numeric($amount)) {
            $amount = 0;
        }
        return 'Ksh ' . number_format($amount);
    }
}
<?php
// Ensure we have the dashboard data
$data = $dashboard_data ?? [];
$total_donated = $data['total_donated'] ?? 0;
$total_donations = $data['total_donations'] ?? 0;
$campaigns_supported = $data['campaigns_supported'] ?? 0;
?>

<!-- Stats Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Donated</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo formatCurrency($total_donated); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-donate fa-2x text-gray-300"></i>
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
                            Campaigns Supported</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $campaigns_supported; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
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
                            Total Donations</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_donations; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
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
                            Impact Score</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_donations * 10; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
                    </div>
                </div>
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
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo abs_path('src/campaigns/campaigns.php'); ?>" class="btn btn-primary btn-circle btn-xl">
                            <i class="fas fa-search fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Browse Campaigns</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo abs_path('src/donations/donate.php'); ?>" class="btn btn-success btn-circle btn-xl">
                            <i class="fas fa-donate fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Make Donation</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo abs_path('src/auth/my_donations.php'); ?>" class="btn btn-info btn-circle btn-xl">
                            <i class="fas fa-history fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Donation History</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo abs_path('src/auth/profile.php'); ?>" class="btn btn-warning btn-circle btn-xl">
                            <i class="fas fa-user-edit fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Update Profile</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Featured Campaigns -->
<div class="row">
    <div class="col-lg-8">
        <!-- Featured Campaigns -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Featured Campaigns</h6>
                <a href="<?php echo abs_path('src/campaigns/campaigns.php'); ?>" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php for($i = 1; $i <= 2; $i++): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo abs_path('assets/images/campaign' . $i . '.jpg'); ?>"
                                class="card-img-top" alt="Campaign" height="200" style="object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <span class="badge bg-success mb-2">Education</span>
                                <h6 class="card-title">Education Support #<?php echo $i; ?></h6>
                                <p class="card-text flex-grow-1 small">Providing quality education for orphans in need of support...</p>
                                
                                <div class="mb-3">
                                    <div class="progress mb-2" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: <?php echo rand(30, 80); ?>%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span><?php echo rand(30, 80); ?>% funded</span>
                                        <span>Ksh <?php echo number_format(rand(30000, 80000)); ?></span>
                                    </div>
                                </div>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?php echo rand(5, 30); ?> days left
                                        </small>
                                        <a href="<?php echo abs_path('src/campaigns/campaign_detail.php?id=' . $i); ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Recent Activity -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
            </div>
            <div class="card-body">
                <?php if ($total_donations > 0): ?>
                <div class="timeline">
                    <div class="timeline-item mb-3">
                        <div class="timeline-badge bg-success"></div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6 class="timeline-title">Donation Made</h6>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>2 days ago</small>
                            </div>
                            <div class="timeline-body">
                                <p class="small mb-0">Ksh 2,000 to Education for Orphans campaign</p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item mb-3">
                        <div class="timeline-badge bg-primary"></div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6 class="timeline-title">Account Created</h6>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>1 week ago</small>
                            </div>
                            <div class="timeline-body">
                                <p class="small mb-0">Welcome to TrueCare! Start making a difference.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-donate fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No activity yet</p>
                    <small>Make your first donation to see activity here</small>
                    <div class="mt-3">
                        <a href="<?php echo abs_path('src/campaigns/campaigns.php'); ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-hand-holding-heart me-1"></i>Browse Campaigns
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
}
.timeline-badge {
    position: absolute;
    left: -30px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
}
.timeline-panel {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}
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
</style>