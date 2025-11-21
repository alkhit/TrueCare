<?php
// This file is included in dashboard.php for admin users

// Fetch admin stats (mock data for now)
$stats = [
    'total_users' => 1248,
    'total_donations' => 2400000,
    'active_campaigns' => 156,
    'pending_verifications' => 18,
    'total_orphanages' => 42,
    'total_donors' => 1206
];
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_users']); ?></div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo formatCurrency($stats['total_donations']); ?></div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['active_campaigns']; ?></div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['pending_verifications']; ?></div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_orphanages']; ?></div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_donors']; ?></div>
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
                        <a href="<?php echo abs_path('src/admin/support.php'); ?>" class="btn btn-secondary btn-circle btn-xl">
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
                <div class="timeline">
                    <div class="timeline-item mb-4">
                        <div class="timeline-badge bg-success"></div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6 class="timeline-title">New Donation Received</h6>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>2 hours ago</small>
                            </div>
                            <div class="timeline-body">
                                <p>Ksh 5,000 donated to "Education for Orphans" campaign by John Doe</p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item mb-4">
                        <div class="timeline-badge bg-primary"></div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6 class="timeline-title">New Campaign Created</h6>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>5 hours ago</small>
                            </div>
                            <div class="timeline-body">
                                <p>"Medical Supplies for Children" campaign created by Hope Orphanage</p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item mb-4">
                        <div class="timeline-badge bg-warning"></div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6 class="timeline-title">Orphanage Registration</h6>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>1 day ago</small>
                            </div>
                            <div class="timeline-body">
                                <p>New orphanage "Grace Children Home" registered and awaiting verification</p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-badge bg-info"></div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6 class="timeline-title">Campaign Completed</h6>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>2 days ago</small>
                            </div>
                            <div class="timeline-body">
                                <p>"Food Drive 2024" campaign reached its goal of Ksh 200,000</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- System Stats -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Statistics</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="userDistributionChart" width="400" height="200"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Donors
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Orphanages
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Admins
                    </span>
                </div>
            </div>
        </div>

        <!-- Pending Actions -->
        <div class="card shadow mt-4">
            <div class="card-header py-3 bg-warning">
                <h6 class="m-0 font-weight-bold text-white">Pending Actions</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="<?php echo abs_path('src/admin/verify_orphanages.php'); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Orphanage Verifications
                        <span class="badge bg-primary rounded-pill"><?php echo $stats['pending_verifications']; ?></span>
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
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Distribution Chart
    const ctx = document.getElementById('userDistributionChart').getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Donors', 'Orphanages', 'Admins'],
                datasets: [{
                    data: [<?php echo $stats['total_donors']; ?>, <?php echo $stats['total_orphanages']; ?>, 3],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 80,
            },
        });
    }
});
</script>

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
.list-group-item {
    border: none;
    padding: 0.75rem 0;
}
</style>