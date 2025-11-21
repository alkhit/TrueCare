<?php
// Sidebar component to be included in other pages
$user_role = $_SESSION['user_role'] ?? 'donor';
?>

<div class="position-sticky pt-3">
    <div class="text-center mb-4">
        <img src="../../assets/images/avatar.png" alt="Avatar" class="rounded-circle" width="80">
        <h6 class="mt-2"><?php echo $_SESSION['user_name']; ?></h6>
        <span class="badge bg-<?php echo $user_role === 'donor' ? 'success' : 'warning'; ?>">
            <?php echo ucfirst($user_role); ?>
        </span>
    </div>
    
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </li>
        
        <?php if ($user_role === 'donor'): ?>
        <li class="nav-item">
            <a class="nav-link" href="../campaigns/campaigns.php">
                <i class="fas fa-hand-holding-heart me-2"></i>Browse Campaigns
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../donations/donate.php">
                <i class="fas fa-donate me-2"></i>Make Donation
            </a>
        </li>
        <?php elseif ($user_role === 'orphanage'): ?>
        <li class="nav-item">
            <a class="nav-link" href="../campaigns/my_campaigns.php">
                <i class="fas fa-hand-holding-heart me-2"></i>My Campaigns
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../campaigns/create_campaign.php">
                <i class="fas fa-plus-circle me-2"></i>Create Campaign
            </a>
        </li>
        <?php endif; ?>
        
        <li class="nav-item">
            <a class="nav-link" href="profile.php">
                <i class="fas fa-user me-2"></i>Profile
            </a>
        </li>
        
        <?php if ($user_role === 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link" href="../admin/admin.php">
                <i class="fas fa-cog me-2"></i>Admin Panel
            </a>
        </li>
        <?php endif; ?>
        
        <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </li>
    </ul>
</div>