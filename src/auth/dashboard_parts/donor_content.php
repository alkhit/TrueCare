<?php
require_once __DIR__ . '/../../../includes/functions.php';
// Ensure $db is defined
if (!isset($db)) {
    $db = get_db();
}
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        if (!is_numeric($amount)) {
            $amount = 0;
        }
        return 'Ksh ' . number_format($amount);
    }
}
// Get donor's total donated amount from DB
$user_id = $_SESSION['user_id'] ?? null;
$total_donated = 0;
if ($user_id) {
    $stmt = $db->prepare('SELECT COALESCE(SUM(amount),0) as total FROM donations WHERE user_id = ? AND status = "completed"');
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_donated = $row['total'] ?? 0;
}
// Ensure we have the dashboard data
$data = $dashboard_data ?? [];
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
                <div class="row row-cols-1 row-cols-md-2 g-3 justify-content-center">
                <?php
                $featuredStmt = $db->query("SELECT campaign_id, title, description, category, target_amount, current_amount, deadline FROM campaigns WHERE status='active' ORDER BY created_at DESC LIMIT 2");
                $featuredCampaigns = $featuredStmt ? $featuredStmt->fetchAll(PDO::FETCH_ASSOC) : [];
                if (count($featuredCampaigns) === 0): ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No campaigns available at the moment.</h5>
                    </div>
                <?php else:
                    foreach ($featuredCampaigns as $campaign):
                        $category = isset($campaign['category']) && !empty($campaign['category']) ? strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', $campaign['category'])) : 'default';
                        $img_check_path = $_SERVER['DOCUMENT_ROOT'] . "/TrueCare/assets/images/campaigns/$category.jpg";
                        $image_path = abs_path('assets/images/campaigns/' . $category . '.jpg');
                        if (!file_exists($img_check_path)) {
                            $image_path = abs_path('assets/images/campaigns/default.jpg');
                        }
                        $progress = $campaign['target_amount'] > 0 ? ($campaign['current_amount'] / $campaign['target_amount']) * 100 : 0;
                        $progress_class = $progress >= 80 ? 'bg-success' : ($progress >= 50 ? 'bg-info' : 'bg-warning');
                        $days_left = ceil((strtotime($campaign['deadline']) - time()) / (60 * 60 * 24));
                ?>
                    <div class="col d-flex align-items-stretch">
                        <div class="card h-100 d-flex flex-column border-0 shadow-sm amazon-card">
                            <div class="position-relative text-center bg-white" style="padding-top:8px;">
                                <img src="<?php echo $image_path; ?>" class="card-img-top mx-auto" alt="<?php echo htmlspecialchars($campaign['title']); ?>" style="height: 260px; width: 98%; object-fit:cover; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.10);" onerror="this.onerror=null;this.src='<?php echo abs_path('assets/images/campaigns/default.jpg'); ?>';">
                                <span class="badge bg-success text-capitalize position-absolute top-0 start-0 m-2" style="font-size:1em;"> <?php echo htmlspecialchars(ucfirst($campaign['category'])); ?> </span>
                                <span class="badge <?php echo $days_left <= 7 ? 'bg-danger' : 'bg-warning'; ?> position-absolute top-0 end-0 m-2" style="font-size:1em;"> <i class="fas fa-clock me-1"></i><?php echo $days_left > 0 ? $days_left . ' days left' : 'Ended'; ?> </span>
                            </div>
                            <div class="card-body d-flex flex-column px-3 pb-3 pt-2">
                                <h6 class="card-title fw-bold mb-1 text-dark text-truncate" title="<?php echo htmlspecialchars($campaign['title']); ?>"><?php echo htmlspecialchars($campaign['title']); ?></h6>
                                <p class="card-text text-muted flex-grow-1 mb-2" style="min-height: 40px; font-size:0.97em;"> <?php echo htmlspecialchars($campaign['description']); ?> </p>
                                <div class="mb-2">
                                    <div class="progress mb-1" style="height: 7px;">
                                        <div class="progress-bar <?php echo $progress_class; ?>" style="width: <?php echo min($progress, 100); ?>%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted" style="font-size:0.95em;">
                                        <span><?php echo number_format($progress, 1); ?>% funded</span>
                                        <span>Ksh <?php echo number_format($campaign['current_amount']); ?> of Ksh <?php echo number_format($campaign['target_amount']); ?></span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <a href="<?php echo abs_path('src/campaigns/campaign_detail.php?id=' . $campaign['campaign_id']); ?>" class="btn btn-warning btn-sm px-3 w-100" style="font-weight:500;">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach;
                endif; ?>
                </div>
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