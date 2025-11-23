<?php
$user_role = $_SESSION['user_role'] ?? 'donor';
?>

<div class="position-sticky pt-3 sidebar bg-dark text-white rounded shadow-sm" style="min-height: 100vh; background: linear-gradient(135deg, #1a365f, #2c5aa0);">
    <div class="text-center mb-4">
        <?php
            $name = $_SESSION['user_name'] ?? '';
            $initials = '';
            if ($name) {
                $parts = explode(' ', $name);
                foreach ($parts as $p) {
                    if (strlen($p) > 0) $initials .= strtoupper($p[0]);
                }
            }
        ?>
        <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center mx-auto mb-2" style="width:70px;height:70px;font-size:2rem;">
            <?php echo $initials ?: '?'; ?>
        </div>
        <h6 class="mb-1 text-white fw-bold"><?php echo $_SESSION['user_name']; ?></h6>
        <span class="badge bg-<?php 
            echo $user_role === 'donor' ? 'success' : 
                     ($user_role === 'orphanage' ? 'warning' : 
                     ($user_role === 'admin' ? 'danger' : 'secondary')); 
        ?> px-3 py-1">
            <?php echo ucfirst($user_role); ?>
        </span>
    </div>

    <ul class="nav flex-column gap-2">
        <li class="nav-item">
            <a class="nav-link active text-white" href="<?php echo abs_path('src/auth/dashboard.php'); ?>">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </li>

        <!-- Donor Navigation -->
        <?php if ($user_role === 'donor'): ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo abs_path('src/campaigns/campaigns.php'); ?>">
                    <i class="fas fa-hand-holding-heart me-2"></i>Browse Campaigns
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo abs_path('src/auth/my_donations.php'); ?>">
                    <i class="fas fa-history me-2"></i>My Donations
                </a>
            </li>

        <!-- Orphanage Navigation -->
        <?php elseif ($user_role === 'orphanage'): ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo abs_path('src/campaigns/my_campaigns.php'); ?>">
                    <i class="fas fa-hand-holding-heart me-2"></i>My Campaigns
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo abs_path('src/campaigns/create_campaign.php'); ?>">
                    <i class="fas fa-plus-circle me-2"></i>Create Campaign
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo abs_path('src/donations/orphanage_donations.php'); ?>">
                    <i class="fas fa-donate me-2"></i>Received Donations
                </a>
            </li>

        <!-- Admin Navigation -->
        <?php elseif ($user_role === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo abs_path('src/admin/verify_orphanages.php'); ?>">
                    <i class="fas fa-clipboard-check me-2"></i>Verify Orphanages
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo abs_path('src/admin/manage_users.php'); ?>">
                    <i class="fas fa-users-cog me-2"></i>Manage Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo abs_path('src/admin/manage_campaigns.php'); ?>">
                    <i class="fas fa-hand-holding-heart me-2"></i>Manage Campaigns
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo abs_path('src/admin/reports.php'); ?>">
                    <i class="fas fa-chart-bar me-2"></i>Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo abs_path('src/admin/settings.php'); ?>">
                    <i class="fas fa-cogs me-2"></i>Settings
                </a>
            </li>
        <?php endif; ?>

        <!-- Common Navigation -->
        <li class="nav-item">
            <a class="nav-link" href="<?php echo abs_path('src/auth/profile.php'); ?>">
                <i class="fas fa-user me-2"></i>Profile
            </a>
        </li>

        <!-- Admin Panel Link (for admin users) -->
        <?php if ($user_role === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo abs_path('src/admin/admin.php'); ?>">
                    <i class="fas fa-cog me-2"></i>Admin Panel
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item mt-3">
            <a class="nav-link text-white bg-danger rounded-pill px-3 py-2" href="<?php echo abs_path('src/auth/logout.php'); ?>">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </li>
    </ul>
</div>