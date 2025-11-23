<?php
// admin.php
session_start();

// Show all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure user is logged in and is admin
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Include config and functions safely
$configPath = __DIR__ . '/../../includes/config.php';
$headerPath = __DIR__ . '/../../includes/header.php';
$footerPath = __DIR__ . '/../../includes/footer.php';
$sidebarPath = __DIR__ . '/../auth/sidebar.php';

if (!file_exists($configPath) || !file_exists($headerPath) || !file_exists($footerPath)) {
    die("Required files missing. Check includes folder.");
}

include $configPath;

$page_title = "Admin Panel - TrueCare";
include $headerPath;
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <?php 
            if (file_exists($sidebarPath)) {
                include $sidebarPath; 
            } else {
                echo "<p class='text-danger'>Sidebar file not found.</p>";
            }
            ?>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-cog me-2"></i>Admin Panel</h1>
            </div>

            <!-- Admin Navigation Cards -->
            <div class="row mb-4 text-center">
                <?php
                $admin_links = [
                    'verify_orphanages.php' => ['btn-primary', 'fas fa-clipboard-check', 'Verify Orphanages'],
                    'manage_users.php' => ['btn-success', 'fas fa-users-cog', 'Manage Users'],
                    'manage_campaigns.php' => ['btn-info', 'fas fa-hand-holding-heart', 'Campaigns'],
                    'reports.php' => ['btn-warning', 'fas fa-chart-bar', 'Reports'],
                    'settings.php' => ['btn-danger', 'fas fa-cogs', 'Settings'],
                    'support_tickets.php' => ['btn-secondary', 'fas fa-headset', 'Support'],
                ];

                foreach ($admin_links as $file => $data) {
                    echo '<div class="col-md-2 mb-3">
                            <a href="' . $file . '" class="btn ' . $data[0] . ' w-100 h-100 py-3">
                                <i class="' . $data[1] . ' fa-2x mb-2"></i><br>' . $data[2] . '
                            </a>
                          </div>';
                }
                ?>
            </div>

            <!-- Quick Stats / Recent Activity -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Platform Activity</h6>
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
                                        <tr><td>2 hours ago</td><td>New Donation</td><td>John Doe</td><td>Ksh 5,000 to Education Campaign</td></tr>
                                        <tr><td>5 hours ago</td><td>Campaign Created</td><td>Hope Orphanage</td><td>Medical Supplies Campaign</td></tr>
                                        <tr><td>1 day ago</td><td>User Registration</td><td>Grace Children Home</td><td>New orphanage registered</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Actions -->
                <div class="col-lg-4">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.table th { font-weight: 600; background-color: #f8f9fa; }
.list-group-item { border: none; padding: 0.75rem 0; }
</style>

<?php include $footerPath; ?>
