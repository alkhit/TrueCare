<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'orphanage') {
    header("Location: ../../login.php");
    exit;
}
include '../../includes/config.php';
include '../../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <?php include '../auth/sidebar.php'; ?>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">My Campaigns</h1>
                <a href="create_campaign.php" class="btn btn-success">
                    <i class="fas fa-plus-circle me-2"></i>Create New Campaign
                </a>
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
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">3</div>
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
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Ksh 110,000</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                        Active Campaigns</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">2</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                        Total Donors</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">42</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                    <th>Donors</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                // Get user ID from session
                                $user_id = $_SESSION['user_id'] ?? null;
                                $stmt = $db->prepare("SELECT campaign_id, title, description, category, target_amount, current_amount, deadline FROM campaigns WHERE user_id = ? ORDER BY created_at DESC");
                                $stmt->execute([$user_id]);
                                $myCampaigns = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                                if (count($myCampaigns) === 0): ?>
                                    <div class="col-12 text-center py-5">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">You have not created any campaigns yet.</h5>
                                    </div>
                                <?php else:
                                    foreach ($myCampaigns as $campaign): ?>
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
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?php
                                                        $days_left = ceil((strtotime($campaign['deadline']) - time()) / (60 * 60 * 24));
                                                        echo $days_left > 0 ? $days_left . ' days left' : 'Ended';
                                                        ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark"><?php echo ucfirst($campaign['category']); ?></span>
                                        </td>
                                        <td>Ksh <?php echo number_format($campaign['target_amount']); ?></td>
                                        <td>Ksh <?php echo number_format($campaign['current_amount']); ?></td>
                                        <td>
                                            <div class="progress" style="height: 6px; width: 100px;">
                                                <div class="progress-bar bg-success" style="width: <?php echo ($campaign['current_amount'] / $campaign['target_amount']) * 100; ?>%"></div>
                                            </div>
                                            <small><?php echo number_format(($campaign['current_amount'] / $campaign['target_amount']) * 100, 1); ?>%</small>
                                        </td>
                                        <td><?php echo $campaign['donors']; ?></td>
                                        <td>
                                            <span class="badge bg-success">
                                                <?php echo ucfirst($campaign['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="campaign_detail.php?id=<?php echo $campaign['campaign_id']; ?>" class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit_campaign.php?id=<?php echo $campaign['campaign_id']; ?>" class="btn btn-outline-success" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="analytics_campaign.php?id=<?php echo $campaign['campaign_id']; ?>" class="btn btn-outline-info" title="Analytics">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                                <a href="delete_campaign.php?id=<?php echo $campaign['campaign_id']; ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this campaign?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach;
                                endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>