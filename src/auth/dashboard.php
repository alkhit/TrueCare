<?php 
include '../../includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(BASE_URL . '/login.php');
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$user_name = $_SESSION['user_name'];

// Get user-specific data based on role
try {
    if ($user_role === 'orphanage') {
        // Get orphanage details
        $query = "SELECT o.* FROM orphanages o WHERE o.user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $orphanage = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($orphanage) {
            // Get orphanage campaigns
            $campaigns_query = "SELECT * FROM campaigns WHERE orphanage_id = :orphanage_id ORDER BY created_at DESC LIMIT 5";
            $campaigns_stmt = $db->prepare($campaigns_query);
            $campaigns_stmt->bindParam(':orphanage_id', $orphanage['orphanage_id']);
            $campaigns_stmt->execute();
            $campaigns = $campaigns_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get campaign stats
            $stats_query = "SELECT 
                COUNT(*) as total_campaigns,
                SUM(target_amount) as total_goal,
                SUM(current_amount) as total_raised
                FROM campaigns 
                WHERE orphanage_id = :orphanage_id";
            $stats_stmt = $db->prepare($stats_query);
            $stats_stmt->bindParam(':orphanage_id', $orphanage['orphanage_id']);
            $stats_stmt->execute();
            $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
        }
        
    } elseif ($user_role === 'donor') {
        // Get donor's donation history
        $donations_query = "SELECT d.*, c.title, c.campaign_id, o.name as orphanage_name 
                           FROM donations d 
                           JOIN campaigns c ON d.campaign_id = c.campaign_id 
                           JOIN orphanages o ON c.orphanage_id = o.orphanage_id 
                           WHERE d.user_id = :user_id 
                           ORDER BY d.donation_date DESC 
                           LIMIT 5";
        $donations_stmt = $db->prepare($donations_query);
        $donations_stmt->bindParam(':user_id', $user_id);
        $donations_stmt->execute();
        $donations = $donations_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get donation stats
        $donation_stats_query = "SELECT 
            COUNT(*) as total_donations,
            SUM(amount) as total_amount
            FROM donations 
            WHERE user_id = :user_id AND status = 'completed'";
        $donation_stats_stmt = $db->prepare($donation_stats_query);
        $donation_stats_stmt->bindParam(':user_id', $user_id);
        $donation_stats_stmt->execute();
        $donation_stats = $donation_stats_stmt->fetch(PDO::FETCH_ASSOC);
        
    } elseif ($user_role === 'admin') {
        // Admin statistics
        $stats_query = "SELECT 
            (SELECT COUNT(*) FROM users WHERE role = 'donor') as total_donors,
            (SELECT COUNT(*) FROM orphanages WHERE status = 'verified') as verified_orphanages,
            (SELECT COUNT(*) FROM orphanages WHERE status = 'pending') as pending_orphanages,
            (SELECT COUNT(*) FROM campaigns WHERE status = 'active') as active_campaigns,
            (SELECT SUM(amount) FROM donations WHERE status = 'completed') as total_donations";
        $stats_stmt = $db->prepare($stats_query);
        $stats_stmt->execute();
        $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
        
        // Recent orphanages needing verification
        $pending_orphanages_query = "SELECT o.*, u.name as user_name, u.email 
                                   FROM orphanages o 
                                   JOIN users u ON o.user_id = u.user_id 
                                   WHERE o.status = 'pending' 
                                   ORDER BY o.created_at DESC 
                                   LIMIT 5";
        $pending_orphanages_stmt = $db->prepare($pending_orphanages_query);
        $pending_orphanages_stmt->execute();
        $pending_orphanages = $pending_orphanages_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $error = "Error loading dashboard data. Please try again.";
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container-fluid mt-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                        Dashboard
                    </h1>
                    <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-<?php 
                        echo $user_role === 'admin' ? 'danger' : 
                             ($user_role === 'orphanage' ? 'success' : 'primary'); 
                    ?> fs-6">
                        <?php echo ucfirst($user_role); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Role-specific Dashboard Content -->
    <?php if ($user_role === 'orphanage'): ?>
        <!-- Orphanage Dashboard -->
        <?php if ($orphanage): ?>
            <div class="row">
                <!-- Stats Cards -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Campaigns</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['total_campaigns'] ?? 0; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
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
                                        Total Raised</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        KES <?php echo number_format($stats['total_raised'] ?? 0); ?>
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
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Fundraising Goal</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        KES <?php echo number_format($stats['total_goal'] ?? 0); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-bullseye fa-2x text-gray-300"></i>
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
                                        Orphanage Status</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800 text-capitalize">
                                        <?php echo $orphanage['status']; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-home fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Campaigns -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Campaigns</h6>
                            <a href="<?php echo BASE_URL; ?>/src/campaigns/create_campaign.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>New Campaign
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($campaigns)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Category</th>
                                                <th>Goal</th>
                                                <th>Raised</th>
                                                <th>Progress</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($campaigns as $campaign): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($campaign['title']); ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info text-capitalize">
                                                            <?php echo $campaign['category']; ?>
                                                        </span>
                                                    </td>
                                                    <td>KES <?php echo number_format($campaign['target_amount']); ?></td>
                                                    <td>KES <?php echo number_format($campaign['current_amount']); ?></td>
                                                    <td>
                                                        <div class="progress" style="height: 6px; width: 100px;">
                                                            <div class="progress-bar bg-success" 
                                                                 style="width: <?php echo min(($campaign['current_amount'] / $campaign['target_amount']) * 100, 100); ?>%">
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">
                                                            <?php echo number_format(min(($campaign['current_amount'] / $campaign['target_amount']) * 100, 100), 1); ?>%
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $campaign['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                            <?php echo ucfirst($campaign['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo BASE_URL; ?>/src/campaigns/campaign_detail.php?id=<?php echo $campaign['campaign_id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="<?php echo BASE_URL; ?>/src/campaigns/campaigns.php?my_campaigns=1" 
                                       class="btn btn-outline-primary">
                                        View All Campaigns
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-hand-holding-heart fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Campaigns Yet</h5>
                                    <p class="text-muted">Start your first fundraising campaign to help your orphanage.</p>
                                    <a href="<?php echo BASE_URL; ?>/src/campaigns/create_campaign.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Create First Campaign
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Orphanage Profile Not Found</h5>
                <p>Your orphanage profile could not be loaded. Please contact support.</p>
            </div>
        <?php endif; ?>

    <?php elseif ($user_role === 'donor'): ?>
        <!-- Donor Dashboard -->
        <div class="row">
            <!-- Stats Cards -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Donated</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    KES <?php echo number_format($donation_stats['total_amount'] ?? 0); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-donate fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Campaigns Supported</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $donation_stats['total_donations'] ?? 0; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Impact Made</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo ($donation_stats['total_donations'] ?? 0) * 5; ?>+ Lives
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-child fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Donations</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($donations)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Campaign</th>
                                            <th>Orphanage</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($donations as $donation): ?>
                                            <tr>
                                                <td><?php echo date('M j, Y', strtotime($donation['donation_date'])); ?></td>
                                                <td><?php echo htmlspecialchars($donation['title']); ?></td>
                                                <td><?php echo htmlspecialchars($donation['orphanage_name']); ?></td>
                                                <td>
                                                    <strong>KES <?php echo number_format($donation['amount']); ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $donation['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                                        <?php echo ucfirst($donation['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo BASE_URL; ?>/src/campaigns/campaign_detail.php?id=<?php echo $donation['campaign_id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-donate fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Donations Yet</h5>
                                <p class="text-muted">Start making a difference by supporting orphanage campaigns.</p>
                                <a href="<?php echo BASE_URL; ?>/src/campaigns/campaigns.php" class="btn btn-primary">
                                    <i class="fas fa-hand-holding-heart me-2"></i>Browse Campaigns
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($user_role === 'admin'): ?>
        <!-- Admin Dashboard -->
        <div class="row">
            <!-- Stats Cards -->
            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Donors</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $stats['total_donors'] ?? 0; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Verified Orphanages</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $stats['verified_orphanages'] ?? 0; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-home fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending Verification</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $stats['pending_orphanages'] ?? 0; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Active Campaigns</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $stats['active_campaigns'] ?? 0; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-8 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Total Donations</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    KES <?php echo number_format($stats['total_donations'] ?? 0); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-donate fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orphanages -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <h6 class="m-0 font-weight-bold text-warning">
                            <i class="fas fa-clock me-2"></i>Orphanages Pending Verification
                        </h6>
                        <a href="<?php echo BASE_URL; ?>/src/admin/verify_orphanages.php" class="btn btn-warning btn-sm">
                            <i class="fas fa-check-circle me-1"></i>Manage Verifications
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($pending_orphanages)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Orphanage Name</th>
                                            <th>Contact Person</th>
                                            <th>Email</th>
                                            <th>Location</th>
                                            <th>Registration Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pending_orphanages as $orphanage): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($orphanage['name']); ?></strong>
                                                    <?php if ($orphanage['registration_number']): ?>
                                                        <br><small class="text-muted">Reg: <?php echo $orphanage['registration_number']; ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($orphanage['user_name']); ?></td>
                                                <td><?php echo htmlspecialchars($orphanage['email']); ?></td>
                                                <td><?php echo htmlspecialchars($orphanage['location'] ?? 'Not specified'); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($orphanage['created_at'])); ?></td>
                                                <td>
                                                    <a href="<?php echo BASE_URL; ?>/src/admin/verify_orphanages.php?action=review&id=<?php echo $orphanage['orphanage_id']; ?>" 
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-search"></i> Review
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="text-success">All Caught Up!</h5>
                                <p class="text-muted">No orphanages pending verification at the moment.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo BASE_URL; ?>/src/admin/verify_orphanages.php" 
                                   class="btn btn-outline-warning w-100 h-100 py-3">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                                    Verify Orphanages
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo BASE_URL; ?>/src/admin/manage_users.php" 
                                   class="btn btn-outline-primary w-100 h-100 py-3">
                                    <i class="fas fa-users fa-2x mb-2"></i><br>
                                    Manage Users
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo BASE_URL; ?>/src/campaigns/campaigns.php" 
                                   class="btn btn-outline-success w-100 h-100 py-3">
                                    <i class="fas fa-hand-holding-heart fa-2x mb-2"></i><br>
                                    View Campaigns
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo BASE_URL; ?>/src/admin/reports.php" 
                                   class="btn btn-outline-info w-100 h-100 py-3">
                                    <i class="fas fa-chart-bar fa-2x mb-2"></i><br>
                                    View Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>