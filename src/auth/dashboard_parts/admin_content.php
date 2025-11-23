<?php require_once __DIR__ . '/../../../includes/functions.php';
// Ensure we have the dashboard data
$data = $dashboard_data ?? [];
$total_users = $data['total_users'] ?? 0;
$total_donations = $data['total_donations'] ?? 0;
$active_campaigns = $data['active_campaigns'] ?? 0;
$pending_verifications = $data['pending_verifications'] ?? 0;
$total_orphanages = $data['total_orphanages'] ?? 0;
$total_donors = $data['total_donors'] ?? 0;
?>

<!-- Stats Cards -->
<div class="row">
    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo number_format($total_users); ?>
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
                            Total Donations</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo formatCurrency($total_donations); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-donate fa-2x text-gray-300"></i>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $active_campaigns; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
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
                            Pending Verifications</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_verifications; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                            Orphanages</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_orphanages; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-home fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Donors</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_donors; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-friends fa-2x text-gray-300"></i>
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
                <h6 class="m-0 font-weight-bold text-primary">Admin Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2 mb-3">
                        <a href="<?php echo abs_path('src/admin/verify_orphanages.php'); ?>" class="btn btn-primary btn-circle btn-xl">
                            <i class="fas fa-clipboard-check fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Verify Orphanages</small>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="<?php echo abs_path('src/admin/manage_users.php'); ?>" class="btn btn-success btn-circle btn-xl">
                            <i class="fas fa-users-cog fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Manage Users</small>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="<?php echo abs_path('src/campaigns/campaigns.php'); ?>" class="btn btn-info btn-circle btn-xl">
                            <i class="fas fa-hand-holding-heart fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>View Campaigns</small>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="<?php echo abs_path('src/admin/reports.php'); ?>" class="btn btn-warning btn-circle btn-xl">
                            <i class="fas fa-chart-bar fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Reports</small>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="<?php echo abs_path('src/admin/settings.php'); ?>" class="btn btn-danger btn-circle btn-xl">
                            <i class="fas fa-cog fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Settings</small>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="<?php echo abs_path('src/admin/support_tickets.php'); ?>" class="btn btn-secondary btn-circle btn-xl">
                            <i class="fas fa-question-circle fa-2x"></i>
                        </a>
                        <div class="mt-2">
                            <small>Support</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & System Stats -->
<div class="row">
    <div class="col-lg-8">
        <!-- Recent Activity -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Platform Activity</h6>
                <a href="#" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Time</th>
                                <th>Activity</th>
                                <th>User</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2 hours ago</td>
                                <td>New Donation</td>
                                <td>John Doe</td>
                                <td>Ksh 5,000 to Education Campaign</td>
                            </tr>
                            <tr>
                                <td>5 hours ago</td>
                                <td>Campaign Created</td>
                                <td>Hope Orphanage</td>
                                <td>Medical Supplies Campaign</td>
                            </tr>
                            <tr>
                                <td>1 day ago</td>
                                <td>User Registration</td>
                                <td>Grace Children Home</td>
                                <td>New orphanage registered</td>
                            </tr>
                            <tr>
                                <td>2 days ago</td>
                                <td>Campaign Completed</td>
                                <td>Sunshine Orphanage</td>
                                <td>Food Drive reached goal</td>
                            </tr>
                            <tr>
                                <td>3 days ago</td>
                                <td>Support Ticket</td>
                                <td>David Kim</td>
                                <td>Payment issue reported</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Pending Actions -->
        <div class="card shadow">
            <div class="card-header py-3 bg-warning">
                <h6 class="m-0 font-weight-bold text-white">Pending Actions</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="<?php echo abs_path('src/admin/verify_orphanages.php'); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Orphanage Verifications
                        <span class="badge bg-primary rounded-pill"><?php echo $pending_verifications; ?></span>
                    </a>
                    <a href="<?php echo abs_path('src/admin/manage_campaigns.php'); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Campaign Approvals
                        <span class="badge bg-success rounded-pill">12</span>
                    </a>
                    <a href="<?php echo abs_path('src/admin/support_tickets.php'); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Support Tickets
                        <span class="badge bg-info rounded-pill">8</span>
                    </a>
                    <a href="<?php echo abs_path('src/admin/reports.php'); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Pending Reports
                        <span class="badge bg-warning rounded-pill">5</span>
                    </a>
                    <a href="<?php echo abs_path('src/admin/manage_users.php'); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        User Reports
                        <span class="badge bg-danger rounded-pill">3</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Health</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Server Load</span>
                        <span>45%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: 45%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Database</span>
                        <span>Healthy</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Storage</span>
                        <span>68%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width: 68%"></div>
                    </div>
                </div>
                <div class="mb-0">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Uptime</span>
                        <span>99.9%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: 99.9%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}
.list-group-item {
    border: none;
    padding: 0.75rem 0;
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