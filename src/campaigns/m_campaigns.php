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
                                $campaigns = [
                                    [
                                        'id' => 1,
                                        'title' => 'Education for Orphans',
                                        'category' => 'Education',
                                        'goal' => 100000,
                                        'raised' => 65000,
                                        'donors' => 24,
                                        'status' => 'active',
                                        'days_left' => 15,
                                        'image' => 'campaign1.jpg'
                                    ],
                                    [
                                        'id' => 2,
                                        'title' => 'Medical Supplies',
                                        'category' => 'Medical',
                                        'goal' => 150000,
                                        'raised' => 45000,
                                        'donors' => 18,
                                        'status' => 'active',
                                        'days_left' => 30,
                                        'image' => 'campaign2.jpg'
                                    ],
                                    [
                                        'id' => 3,
                                        'title' => 'Food and Shelter',
                                        'category' => 'Food',
                                        'goal' => 200000,
                                        'raised' => 0,
                                        'donors' => 0,
                                        'status' => 'draft',
                                        'days_left' => 0,
                                        'image' => 'campaign3.jpg'
                                    ]
                                ];

                                foreach ($campaigns as $campaign):
                                    $progress = ($campaign['raised'] / $campaign['goal']) * 100;
                                    $status_class = [
                                        'active' => 'success',
                                        'completed' => 'primary',
                                        'draft' => 'secondary'
                                    ][$campaign['status']];
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../../assets/images/<?php echo $campaign['image']; ?>" 
                                                 class="rounded me-3" width="60" height="60" style="object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0"><?php echo $campaign['title']; ?></h6>
                                                <?php if ($campaign['status'] === 'active'): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo $campaign['days_left']; ?> days left
                                                </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark"><?php echo $campaign['category']; ?></span>
                                    </td>
                                    <td>Ksh <?php echo number_format($campaign['goal']); ?></td>
                                    <td>Ksh <?php echo number_format($campaign['raised']); ?></td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar bg-<?php echo $status_class; ?>" 
                                                 style="width: <?php echo $progress; ?>%"></div>
                                        </div>
                                        <small><?php echo number_format($progress, 1); ?>%</small>
                                    </td>
                                    <td><?php echo $campaign['donors']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $status_class; ?>">
                                            <?php echo ucfirst($campaign['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="campaign_detail.php?id=<?php echo $campaign['id']; ?>" 
                                               class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-outline-success" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-outline-info" title="Analytics">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                            <a href="#" class="btn btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>