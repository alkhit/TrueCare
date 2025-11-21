<?php
session_start();
checkAuth('admin');

include '../../includes/config.php';
$page_title = "Admin Panel - TrueCare";
include '../../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <?php include '../auth/sidebar.php'; ?>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-cog me-2"></i>Admin Panel
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Print</button>
                    </div>
                </div>
            </div>

            <!-- Admin Navigation -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-2 mb-3">
                                    <a href="verify_orphanages.php" class="btn btn-primary w-100 h-100 py-3">
                                        <i class="fas fa-clipboard-check fa-2x mb-2"></i><br>
                                        Verify Orphanages
                                    </a>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <a href="manage_users.php" class="btn btn-success w-100 h-100 py-3">
                                        <i class="fas fa-users-cog fa-2x mb-2"></i><br>
                                        Manage Users
                                    </a>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <a href="manage_campaigns.php" class="btn btn-info w-100 h-100 py-3">
                                        <i class="fas fa-hand-holding-heart fa-2x mb-2"></i><br>
                                        Campaigns
                                    </a>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <a href="reports.php" class="btn btn-warning w-100 h-100 py-3">
                                        <i class="fas fa-chart-bar fa-2x mb-2"></i><br>
                                        Reports
                                    </a>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <a href="settings.php" class="btn btn-danger w-100 h-100 py-3">
                                        <i class="fas fa-cogs fa-2x mb-2"></i><br>
                                        Settings
                                    </a>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <a href="support_tickets.php" class="btn btn-secondary w-100 h-100 py-3">
                                        <i class="fas fa-headset fa-2x mb-2"></i><br>
                                        Support
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
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
                                <a href="verify_orphanages.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    Orphanage Verifications
                                    <span class="badge bg-primary rounded-pill">18</span>
                                </a>
                                <a href="manage_campaigns.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    Campaign Approvals
                                    <span class="badge bg-success rounded-pill">12</span>
                                </a>
                                <a href="support_tickets.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    Support Tickets
                                    <span class="badge bg-info rounded-pill">8</span>
                                </a>
                                <a href="reports.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    Pending Reports
                                    <span class="badge bg-warning rounded-pill">5</span>
                                </a>
                                <a href="manage_users.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
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
        </main>
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
</style>

<?php include '../../includes/footer.php'; ?>