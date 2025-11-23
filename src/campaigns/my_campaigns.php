<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'orphanage') {
    header("Location: ../../login.php");
    exit;
}
include '../../includes/config.php';
require_once '../../includes/functions.php';
// Ensure $db is defined
if (!isset($db)) {
    $db = get_db();
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

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? null;

// Get orphanage ID from user ID
$stmt = $db->prepare("SELECT orphanage_id FROM orphanages WHERE user_id = ?");
$stmt->execute([$user_id]);
$orphanage = $stmt->fetch(PDO::FETCH_ASSOC);
$orphanage_id = $orphanage['orphanage_id'] ?? null;

// Fetch campaigns for this orphanage
if ($orphanage_id) {
    $stmt = $db->prepare("SELECT campaign_id, title, description, category, target_amount, current_amount, status, deadline, created_at FROM campaigns WHERE orphanage_id = ? ORDER BY created_at DESC");
    $stmt->execute([$orphanage_id]);
    $myCampaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $myCampaigns = [];
}

// Calculate stats
$total_campaigns = count($myCampaigns);
$active_campaigns = 0;
$total_raised = 0;

foreach ($myCampaigns as $campaign) {
    if ($campaign['status'] === 'active') {
        $active_campaigns++;
    }
    $total_raised += $campaign['current_amount'];
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar removed for orphanage pages -->

        <!-- Main content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                        <!-- Success Modal -->
                        <div class="modal fade" id="campaignSuccessModal" tabindex="-1" aria-labelledby="campaignSuccessLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="campaignSuccessLabel">Campaign Created</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Your campaign has been created successfully!</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">My Campaigns</h1>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createCampaignModal">
                                    <a href="create_campaign.php" class="btn btn-success">
                                        <i class="fas fa-plus-circle me-2"></i>Create New Campaign
                                    </a>

                                <!-- Modal and JS removed: all create buttons now link to create_campaign.php -->
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Campaigns</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_campaigns; ?></div>
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
                                        Active Campaigns</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $active_campaigns; ?></div>
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
                                        Total Raised</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Ksh <?php echo number_format($total_raised, 2); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                        Completion Rate</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $total_campaigns > 0 ? round(($active_campaigns / $total_campaigns) * 100) : 0; ?>%
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaigns Table -->
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">My Campaigns</h6>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary active">All</button>
                        <button class="btn btn-sm btn-outline-secondary">Active</button>
                        <button class="btn btn-sm btn-outline-secondary">Completed</button>
                        <button class="btn btn-sm btn-outline-secondary">Draft</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="campaignsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Category</th>
                                    <th>Goal</th>
                                    <th>Raised</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Deadline</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($myCampaigns) === 0): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No campaigns found</p>
                                            <a href="create_campaign.php" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Create Your First Campaign
                                            </a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($myCampaigns as $campaign): 
                                        $progress = $campaign['target_amount'] > 0 ? ($campaign['current_amount'] / $campaign['target_amount']) * 100 : 0;
                                        $status_class = [
                                            'active' => 'success',
                                            'completed' => 'primary',
                                            'draft' => 'secondary',
                                            'cancelled' => 'danger'
                                        ][$campaign['status'] ?? 'draft'];
                                        
                                        // Calculate days left
                                        $days_left = 'N/A';
                                        if ($campaign['deadline'] && $campaign['status'] === 'active') {
                                            $deadline = new DateTime($campaign['deadline']);
                                            $today = new DateTime();
                                            $interval = $today->diff($deadline);
                                            $days_left = $interval->days;
                                            if ($interval->invert) {
                                                $days_left = 'Expired';
                                            }
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php
                                                $category = isset($campaign['category']) && !empty($campaign['category']) ? strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', $campaign['category'])) : 'default';
                                                $image_path = "../../assets/images/campaigns/{$category}.jpg";
                                                if (!file_exists($image_path)) {
                                                    $image_path = "../../assets/images/campaigns/default.jpg";
                                                }
                                                ?>
                                                <img src="<?php echo $image_path; ?>" class="rounded me-3" width="60" height="60" style="object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($campaign['title']); ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo strlen($campaign['description']) > 50 ? substr($campaign['description'], 0, 50) . '...' : $campaign['description']; ?>
                                                    </small>
                                                    <?php if ($campaign['status'] === 'active' && $days_left !== 'N/A' && $days_left !== 'Expired'): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?php echo $days_left; ?> days left
                                                    </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark text-capitalize">
                                                <?php echo $campaign['category']; ?>
                                            </span>
                                        </td>
                                        <td>Ksh <?php echo number_format($campaign['target_amount'], 2); ?></td>
                                        <td>Ksh <?php echo number_format($campaign['current_amount'], 2); ?></td>
                                        <td>
                                            <div class="progress" style="height: 6px; width: 100px;">
                                                <div class="progress-bar bg-<?php echo $status_class; ?>" 
                                                     style="width: <?php echo min($progress, 100); ?>%"></div>
                                            </div>
                                            <small><?php echo number_format($progress, 1); ?>%</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $status_class; ?> text-capitalize">
                                                <?php echo $campaign['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $campaign['deadline'] ? date('M j, Y', strtotime($campaign['deadline'])) : 'Not set'; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="campaign_detail.php?id=<?php echo $campaign['campaign_id']; ?>" 
                                                   class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit_campaign.php?id=<?php echo $campaign['campaign_id']; ?>" 
                                                   class="btn btn-outline-success" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="analytics_campaign.php?id=<?php echo $campaign['campaign_id']; ?>" 
                                                   class="btn btn-outline-info" title="Analytics">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                                <a href="delete_campaign.php?id=<?php echo $campaign['campaign_id']; ?>" 
                                                   class="btn btn-outline-danger" title="Delete" 
                                                   onclick="return confirm('Are you sure you want to delete this campaign?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>