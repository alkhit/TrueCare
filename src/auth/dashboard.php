<?php
session_start();
checkAuth();

// Use absolute path for includes
require_once __DIR__ . '/../../includes/config.php';
$page_title = "Dashboard - TrueCare";
include __DIR__ . '/../../includes/header.php';

$user_role = $_SESSION['user_role'];
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky pt-3">
                <div class="text-center mb-4">
                    <img src="<?php echo abs_path('assets/images/avatar.png'); ?>" alt="Avatar" class="rounded-circle" width="80">
                    <h6 class="mt-2 text-white"><?php echo $_SESSION['user_name']; ?></h6>
                    <span class="badge bg-<?php echo $user_role === 'donor' ? 'success' : 'warning'; ?>">
                        <?php echo ucfirst($user_role); ?>
                    </span>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo abs_path('src/auth/dashboard.php'); ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    
                    <?php if ($user_role === 'donor'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo abs_path('src/campaigns/campaigns.php'); ?>">
                            <i class="fas fa-hand-holding-heart me-2"></i>Browse Campaigns
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo abs_path('src/donations/donate.php'); ?>">
                            <i class="fas fa-donate me-2"></i>Make Donation
                        </a>
                    </li>
                    <?php elseif ($user_role === 'orphanage'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo abs_path('src/campaigns/my_campaigns.php'); ?>">
                            <i class="fas fa-hand-holding-heart me-2"></i>My Campaigns
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo abs_path('src/campaigns/create_campaign.php'); ?>">
                            <i class="fas fa-plus-circle me-2"></i>Create Campaign
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo abs_path('src/auth/profile.php'); ?>">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                    </li>
                    
                    <?php if ($user_role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo abs_path('src/admin/admin.php'); ?>">
                            <i class="fas fa-cog me-2"></i>Admin Panel
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="<?php echo abs_path('src/auth/logout.php'); ?>">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    <?php echo $user_role === 'donor' ? 'Donor' : ($user_role === 'orphanage' ? 'Orphanage' : 'Admin'); ?> Dashboard
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-calendar me-1"></i>
                            <?php echo date('M j, Y'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Welcome Alert -->
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle me-2"></i>Welcome to TrueCare!</h6>
                <p class="mb-0">
                    <?php if ($user_role === 'donor'): ?>
                    Thank you for joining TrueCare! Start by exploring campaigns and making your first donation to support orphanages in need.
                    <?php elseif ($user_role === 'orphanage'): ?>
                    Welcome to TrueCare! You can now create campaigns to receive support for your orphanage. Make sure to complete your profile verification.
                    <?php else: ?>
                    Welcome to the Admin Dashboard. You can manage users, verify orphanages, and monitor platform activity from here.
                    <?php endif; ?>
                </p>
            </div>

            <!-- Role-specific Dashboard Content -->
            <?php if ($user_role === 'donor'): ?>
                <?php include 'donor_dashboard.php'; ?>
            <?php elseif ($user_role === 'orphanage'): ?>
                <?php include 'orphanage_dashboard.php'; ?>
            <?php else: ?>
                <?php include 'admin_dashboard.php'; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>